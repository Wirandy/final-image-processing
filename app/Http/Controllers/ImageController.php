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
    /**
     * Resize and optimize image to standard size
     */
    protected function resizeAndOptimizeImage(string $sourcePath, string $destinationPath, int $maxWidth = 1280, int $maxHeight = 1280): bool
    {
        try {
            // Get image info
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('Invalid image file');
            }

            list($originalWidth, $originalHeight, $imageType) = $imageInfo;

            // Create image resource based on type
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case IMAGETYPE_BMP:
                    $sourceImage = imagecreatefrombmp($sourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    // Try to load as generic image
                    $sourceImage = imagecreatefromstring(file_get_contents($sourcePath));
                    if (!$sourceImage) {
                        throw new \Exception('Unsupported image type');
                    }
            }

            // Calculate new dimensions (maintain aspect ratio)
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            
            // If image is smaller than max, keep original size
            if ($ratio >= 1) {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            } else {
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
            }

            // Create new image with calculated dimensions
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG/GIF
            if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Save as JPEG with good quality
            $result = imagejpeg($resizedImage, $destinationPath, 90);

            // Free memory
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Image resize failed', ['error' => $e->getMessage()]);
            // If resize fails, just copy original file
            return copy($sourcePath, $destinationPath);
        }
    }

    public function upload(Request $request, Patient $patient): RedirectResponse
    {
        try {
            $data = $request->validate([
                'image' => ['required', 'file', 'mimes:png,jpg,jpeg,dcm,bmp,gif,webp', 'max:20480'], // max 20MB
            ]);

            $file = $data['image'];
            
            // Ensure directory exists
            $uploadPath = storage_path('app/public/uploads/originals');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $filename = Str::uuid()->toString() . '.jpg'; // Always save as JPG for consistency
            $fullPath = $uploadPath . '/' . $filename;

            // Resize and optimize image
            $this->resizeAndOptimizeImage($file->getRealPath(), $fullPath);

            // Store path for database
            $originalPath = 'uploads/originals/' . $filename;
            
            // Verify file was stored
            if (!Storage::disk('public')->exists($originalPath)) {
                return back()->with('error', 'Failed to store file. Please check storage permissions.');
            }

            // Create database record
            $image = StudyImage::create([
                'patient_id' => $patient->id,
                'original_path' => $originalPath,
            ]);

            // Get image dimensions for success message
            $imageInfo = getimagesize($fullPath);
            $dimensions = $imageInfo ? "{$imageInfo[0]}x{$imageInfo[1]}px" : 'unknown';

            // Log activity
            ActivityLog::create([
                'action' => 'image.upload',
                'user_id' => optional($request->user())->id,
                'patient_id' => $patient->id,
                'study_image_id' => $image->id,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'meta' => json_encode([
                    'filename' => $file->getClientOriginalName(),
                    'dimensions' => $dimensions,
                ]),
            ]);

            return redirect()->route('patients.show', $patient)->with('status', "Image uploaded & optimized successfully! ID: {$image->id} | Size: {$dimensions}");
            
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
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
            
            // Delete old processed file if exists
            if ($image->processed_path && $image->processed_path !== $destName) {
                Storage::disk('public')->delete($image->processed_path);
            }
            
            $image->processed_path = $destName;
        }

        // Clear forensic data when applying regular filters
        // This ensures the new processed image is shown, not the old annotated image
        // Note: We don't actually clear the data, just let processed_path take priority in display

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
            if ($image->annotated_path) {
                Storage::disk('public')->delete($image->annotated_path);
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


