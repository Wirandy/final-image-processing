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

            // Save annotated image
            $annotatedFile = $result['annotated_path'] ?? null;
            if ($annotatedFile && file_exists($annotatedFile)) {
                $destName = 'uploads/annotated/' . basename($annotatedFile);
                $destPath = storage_path('app/public/' . $destName);
                
                if (!is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0777, true);
                }
                
                // Delete old annotated file if exists
                if ($image->annotated_path && $image->annotated_path !== $destName) {
                    Storage::disk('public')->delete($image->annotated_path);
                }
                
                copy($annotatedFile, $destPath);
                $image->annotated_path = $destName;
            }

            // Clear processed_path when running forensic analysis
            // This ensures annotated image is shown, not the old processed image
            if ($image->processed_path) {
                Storage::disk('public')->delete($image->processed_path);
                $image->processed_path = null;
                $image->features_text = null;
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
