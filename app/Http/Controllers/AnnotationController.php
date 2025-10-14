<?php

namespace App\Http\Controllers;

use App\Models\StudyImage;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AnnotationController extends Controller
{
    /**
     * Show annotation page
     */
    public function showAnnotate(StudyImage $image)
    {
        $patient = $image->patient;
        return view('annotations.annotate', compact('image', 'patient'));
    }

    /**
     * Save annotations to an image
     */
    public function saveAnnotations(Request $request, StudyImage $image): JsonResponse
    {
        $validated = $request->validate([
            'annotations' => ['required', 'array'],
            'annotations.*.type' => ['required', 'string'],
        ]);

        $python = config('app.python_path');
        $localOriginal = storage_path('app/public/' . $image->original_path);
        $script = base_path('python/annotation_tools.py');

        // Write JSON to temporary file to avoid shell escaping issues
        $tempFile = storage_path('app/temp_annotations_' . $image->id . '.json');
        file_put_contents($tempFile, json_encode($validated['annotations'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $cmd = '"' . $python . '" ' . escapeshellarg($script) . ' ' . 
               escapeshellarg($localOriginal) . ' ' . escapeshellarg($tempFile);

        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        $stdout = implode("\n", $output);

        // Clean up temp file
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }

        if ($code !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Annotation processing failed: ' . $stdout
            ], 500);
        }

        $resp = json_decode($stdout, true);
        if (!is_array($resp) || ($resp['status'] ?? 'error') !== 'ok') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid processor output: ' . $stdout
            ], 500);
        }

        $outputFile = $resp['output_path'] ?? null;
        $summary = $resp['summary'] ?? null;

        if ($outputFile && file_exists($outputFile)) {
            $destName = 'uploads/annotated/' . basename($outputFile);
            $destPath = storage_path('app/public/' . $destName);
            
            if (!is_dir(dirname($destPath))) {
                mkdir(dirname($destPath), 0777, true);
            }
            
            copy($outputFile, $destPath);

            // Delete old annotated file if exists
            if ($image->annotated_path && $image->annotated_path !== $destName) {
                Storage::disk('public')->delete($image->annotated_path);
            }

            $image->annotated_path = $destName;
            $image->annotations_data = json_encode($validated['annotations']);
            $image->measurements_data = json_encode($summary['measurements'] ?? []);
            
            // Add annotation history to method field for display
            $annotationCount = count($validated['annotations']);
            $measurementCount = count($summary['measurements'] ?? []);
            $image->method = "Annotations & Measurements";
            $image->features_text = "Added {$annotationCount} annotations and {$measurementCount} measurements";
            
            $image->save();

            // Log activity
            ActivityLog::create([
                'action' => 'image.annotate',
                'user_id' => optional($request->user())->id,
                'patient_id' => $image->patient_id,
                'study_image_id' => $image->id,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'meta' => json_encode([
                    'annotations_count' => count($validated['annotations']),
                    'measurements_count' => count($summary['measurements'] ?? [])
                ]),
            ]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Annotations saved successfully',
                'annotated_path' => asset('storage/' . $destName),
                'summary' => $summary
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to save annotated image'
        ], 500);
    }

    /**
     * Generate report for an image
     */
    public function generateReport(Request $request, StudyImage $image): BinaryFileResponse|RedirectResponse
    {
        $python = config('app.python_path');
        $script = base_path('python/report_generator.py');

        // Prepare report data
        $reportData = [
            'patient_name' => $image->patient->name ?? 'Unknown',
            'patient_id' => $image->patient->identifier ?? 'N/A',
            'image_id' => $image->id,
            'original_image_path' => storage_path('app/public/' . $image->original_path),
            'processed_image_path' => $image->processed_path ? storage_path('app/public/' . $image->processed_path) : '',
            'processing_history' => $image->processing_history ?? [],
            'method' => $image->method ?? '',
            'features_text' => $image->features_text ?? '',
            'forensic_summary' => $image->forensic_summary ?? '',
            'injury_count' => $image->injury_count ?? 0,
            'severity_level' => $image->severity_level ?? '',
            'notes' => $image->patient->notes ?? '',
        ];

        // Write JSON to temporary file to avoid shell escaping issues
        $tempFile = storage_path('app/temp_report_data_' . $image->id . '.json');
        file_put_contents($tempFile, json_encode($reportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $cmd = '"' . $python . '" ' . escapeshellarg($script) . ' ' . escapeshellarg($tempFile);

        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        $stdout = implode("\n", $output);

        // Clean up temp file
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }

        if ($code !== 0) {
            return back()->with('error', 'Report generation failed: ' . $stdout);
        }

        $resp = json_decode($stdout, true);
        if (!is_array($resp) || ($resp['status'] ?? 'error') !== 'ok') {
            return back()->with('error', 'Invalid report generator output: ' . $stdout);
        }

        $reportPath = $resp['report_path'] ?? null;
        $reportName = $resp['report_name'] ?? null;

        if ($reportPath && file_exists($reportPath)) {
            // Move report to storage
            $destName = 'reports/' . $reportName;
            $destPath = storage_path('app/public/' . $destName);
            
            if (!is_dir(dirname($destPath))) {
                mkdir(dirname($destPath), 0777, true);
            }
            
            copy($reportPath, $destPath);

            // Log activity
            ActivityLog::create([
                'action' => 'report.generate',
                'user_id' => optional($request->user())->id,
                'patient_id' => $image->patient_id,
                'study_image_id' => $image->id,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'meta' => json_encode(['report_name' => $reportName]),
            ]);

            // Return file download
            return response()->download($destPath, $reportName)->deleteFileAfterSend(false);
        }

        return back()->with('error', 'Failed to generate report');
    }

    /**
     * Get annotations for an image
     */
    public function getAnnotations(StudyImage $image): JsonResponse
    {
        $annotations = $image->annotations_data ? json_decode($image->annotations_data, true) : [];
        $measurements = $image->measurements_data ? json_decode($image->measurements_data, true) : [];

        return response()->json([
            'status' => 'ok',
            'annotations' => $annotations,
            'measurements' => $measurements
        ]);
    }
}
