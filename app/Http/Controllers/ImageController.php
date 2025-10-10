<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\StudyImage;
use App\Models\ActivityLog;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['required', 'file', 'mimes:png,jpg,jpeg,dcm'],
        ]);

        $file = $data['image'];
        $originalPath = $file->storeAs('uploads/originals', Str::uuid()->toString().'.'.$file->getClientOriginalExtension(), 'public');

        $image = StudyImage::create([
            'patient_id' => $patient->id,
            'original_path' => $originalPath,
        ]);

        ActivityLog::create([
            'action' => 'image.upload',
            'user_id' => optional($request->user())->id,
            'patient_id' => $patient->id,
            'study_image_id' => $image->id,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
            'meta' => json_encode(['filename' => $file->getClientOriginalName()]),
        ]);

        return redirect()->route('patients.show', $patient)->with('status', 'Image uploaded');
    }

    public function process(Request $request, StudyImage $image): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'string'],
        ]);

        $python = config('app.python_path');
        $method = $validated['method'];

        $localOriginal = storage_path('app/public/'.$image->original_path);

        $script = base_path('python/process.py');

        $cmd = '"'.$python.'" '.escapeshellarg($script).' '.escapeshellarg($localOriginal).' '.escapeshellarg($method);

        $output = [];
        $code = 0;
        exec($cmd.' 2>&1', $output, $code);
        $stdout = implode("\n", $output);

        if ($code !== 0) {
            return back()->with('error', 'Processing failed: '.$stdout);
        }

        $resp = json_decode($stdout, true);
        if (!is_array($resp) || ($resp['status'] ?? 'error') !== 'ok') {
            return back()->with('error', 'Invalid processor output: '.$stdout);
        }

        $resultFile = $resp['result_file'] ?? null;
        $features = $resp['features_text'] ?? null;

        if ($resultFile && file_exists($resultFile)) {
            $destName = 'uploads/processed/'.basename($resultFile);
            $destPath = storage_path('app/public/'.$destName);
            if (!is_dir(dirname($destPath))) {
                mkdir(dirname($destPath), 0777, true);
            }
            copy($resultFile, $destPath);
            $image->processed_path = $destName;
        }

        $image->method = $method;
        $image->features_text = $features;
        $image->save();

        ActivityLog::create([
            'action' => 'image.process',
            'user_id' => optional($request->user())->id,
            'patient_id' => $image->patient_id,
            'study_image_id' => $image->id,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
            'meta' => json_encode(['method' => $method]),
        ]);

        return back()->with('status', 'Processing complete');
    }

    public function destroy(StudyImage $image): RedirectResponse
    {
        try {
            if ($image->original_path) {
                Storage::disk('public')->delete($image->original_path);
            }
            if ($image->processed_path) {
                Storage::disk('public')->delete($image->processed_path);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $patient = $image->patient;
        $image->delete();

        ActivityLog::create([
            'action' => 'image.delete',
            'user_id' => optional(request()->user())->id,
            'patient_id' => $patient->id,
            'study_image_id' => null,
            'ip' => request()->ip(),
            'user_agent' => (string) request()->header('User-Agent'),
            'meta' => null,
        ]);

        return redirect()->route('patients.show', $patient)->with('status', 'Image deleted');
    }
}


