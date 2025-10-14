<?php

namespace App\Http\Controllers;

use App\Models\StudyImage;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ForensicAnalysisController extends Controller
{
    /**
     * Run forensic AI analysis on an image
     */
    public function analyze(Request $request, StudyImage $image): RedirectResponse
    {
        try {
            $python = config('app.python_path');
            $apiKey = config('app.roboflow_api_key', 'iN6mDa0muAE7Y0Gvp7OM');
            $modelId = config('app.roboflow_model_id', 'wrist-fracture-bindi/1');

            $localOriginal = storage_path('app/public/' . $image->original_path);
            $script = base_path('python/forensic_analysis.py');

            // Verify files exist
            if (!file_exists($localOriginal)) {
                return back()->with('error', 'Image file not found: ' . $image->original_path);
            }

            if (!file_exists($script)) {
                return back()->with('error', 'Python script not found. Please check installation.');
            }

            // Build command
            $cmd = '"' . $python . '" ' . 
                   escapeshellarg($script) . ' ' . 
                   escapeshellarg($localOriginal) . ' ' . 
                   escapeshellarg($apiKey) . ' ' . 
                   escapeshellarg($modelId);

            \Log::info('Running forensic analysis', ['command' => $cmd]);

            $output = [];
            $code = 0;
            exec($cmd . ' 2>&1', $output, $code);
            $stdout = implode("\n", $output);

            \Log::info('Forensic analysis output', ['code' => $code, 'output' => $stdout]);

            if ($code !== 0) {
                \Log::error('Forensic analysis failed', ['code' => $code, 'output' => $stdout]);
                return back()->with('error', 'Forensic analysis failed. Check if Python and required libraries are installed. Error: ' . substr($stdout, 0, 200));
            }

            $result = json_decode($stdout, true);
            if (!is_array($result) || ($result['status'] ?? 'error') !== 'ok') {
                \Log::error('Invalid forensic analysis output', ['output' => $stdout]);
                return back()->with('error', 'Analysis completed but output format invalid. Please check Python script.');
            }

            // Save forensic annotated image to processed_path
            $annotatedFile = $result['annotated_path'] ?? null;
            if ($annotatedFile && file_exists($annotatedFile)) {
                // Generate unique filename to prevent overwriting
                $extension = pathinfo($annotatedFile, PATHINFO_EXTENSION);
                $uniqueName = $image->id . '_' . time() . '_' . \Str::random(6) . '.' . $extension;
                $destName = 'uploads/processed/' . $uniqueName;
                $destPath = storage_path('app/public/' . $destName);
                
                if (!is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0777, true);
                }
                
                copy($annotatedFile, $destPath);
                
                // Add to processing history (keep max 3)
                $history = $image->processing_history ?? [];
                
                // Add new entry at the beginning
                array_unshift($history, [
                    'path' => $destName,
                    'method' => 'Forensic AI Analysis',
                    'features_text' => sprintf(
                        'Detected %d injuries with %s severity. AI-powered forensic analysis with bounding boxes.',
                        $result['injury_count'] ?? 0,
                        $result['severity_level'] ?? 'unknown'
                    ),
                    'timestamp' => now()->toDateTimeString(),
                ]);
                
                // Keep only last 3 entries
                $history = array_slice($history, 0, 3);
                
                // Delete old files that are no longer in history (but keep processed_path)
                if ($image->processing_history) {
                    $historyPaths = array_column($history, 'path');
                    foreach ($image->processing_history as $old) {
                        $oldPath = $old['path'] ?? null;
                        // Only delete if: not in new history AND not the current processed_path
                        if ($oldPath && !in_array($oldPath, $historyPaths) && $oldPath !== $image->processed_path) {
                            if (Storage::disk('public')->exists($oldPath)) {
                                Storage::disk('public')->delete($oldPath);
                            }
                        }
                    }
                }
                
                $image->processed_path = $destName;
                $image->processing_history = $history;
            }

            // Save forensic analysis results
            $image->forensic_analysis = [
                'classifications' => $result['classifications'] ?? [],
                'cause_suggestions' => $result['cause_suggestions'] ?? [],
                'post_mortem_features' => $result['post_mortem_features'] ?? [],
                'raw_predictions' => $result['raw_predictions'] ?? [],
            ];
            
            $image->forensic_summary = $result['summary'] ?? 'No summary available';
            $image->injury_count = $result['injury_count'] ?? 0;
            $image->severity_level = $result['severity_level'] ?? 'none';
            $image->method = 'Forensic AI Analysis';
            $image->features_text = sprintf(
                'Detected %d injuries with %s severity. AI-powered forensic analysis with bounding boxes and classifications.',
                $result['injury_count'] ?? 0,
                $result['severity_level'] ?? 'unknown'
            );
            $image->save();

            ActivityLog::create([
                'action' => 'forensic.analyze',
                'user_id' => optional($request->user())->id,
                'patient_id' => $image->patient_id,
                'study_image_id' => $image->id,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'meta' => json_encode([
                    'injury_count' => $image->injury_count,
                    'severity' => $image->severity_level,
                ]),
            ]);

            $message = sprintf(
                'Forensic analysis completed! Found %d injuries with %s severity. Image ID: %d',
                $image->injury_count,
                $image->severity_level,
                $image->id
            );

            return back()->with('status', $message);
            
        } catch (\Exception $e) {
            \Log::error('Forensic analysis exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Analysis failed: ' . $e->getMessage());
        }
    }
}
