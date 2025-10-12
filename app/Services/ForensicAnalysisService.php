<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForensicAnalysisService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $modelId;

    public function __construct()
    {
        $this->apiKey = config('services.roboflow.api_key');
        $this->apiUrl = config('services.roboflow.api_url', 'https://detect.roboflow.com');
        $this->modelId = config('services.roboflow.model_id', 'wrist-fracture-bindi/1');
    }

    /**
     * Analyze image for forensic injuries using Roboflow API
     */
    public function analyzeImage(string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                throw new \Exception("Image file not found: {$imagePath}");
            }

            $endpoint = "{$this->apiUrl}/{$this->modelId}";
            
            $response = Http::attach(
                'file',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post($endpoint, [
                'api_key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                throw new \Exception("API request failed: " . $response->body());
            }

            $data = $response->json();
            $predictions = $data['predictions'] ?? [];

            return [
                'status' => 'success',
                'predictions' => $predictions,
                'analysis' => $this->processAnalysis($predictions),
                'raw_response' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Forensic analysis failed', [
                'error' => $e->getMessage(),
                'image' => $imagePath,
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'predictions' => [],
                'analysis' => null,
            ];
        }
    }

    /**
     * Process predictions and generate comprehensive forensic analysis
     */
    protected function processAnalysis(array $predictions): array
    {
        if (empty($predictions)) {
            return [
                'injury_count' => 0,
                'classifications' => [],
                'severity_assessment' => 'No injuries detected',
                'cause_suggestions' => [],
                'post_mortem_features' => $this->detectPostMortemFeatures([]),
                'summary' => 'No forensic injuries detected in the image.',
            ];
        }

        $classifications = [];
        $causeSuggestions = [];
        $severityLevels = [];

        foreach ($predictions as $prediction) {
            $bbox = $this->extractBoundingBox($prediction);
            $area = $this->calculateArea($bbox);
            $severity = $this->assessSeverity($bbox, $area);
            $cause = $this->suggestCause($prediction, $severity, $area);
            $classification = $this->classifyInjury($prediction);

            $classifications[] = [
                'type' => $classification,
                'class' => $prediction['class'] ?? 'unknown',
                'confidence' => round(($prediction['confidence'] ?? 0) * 100, 2),
                'bbox' => $bbox,
                'area' => $area,
                'severity' => $severity,
                'cause' => $cause,
            ];

            $causeSuggestions[] = $cause;
            $severityLevels[] = $severity;
        }

        $overallSeverity = $this->determineOverallSeverity($severityLevels);
        $postMortemFeatures = $this->detectPostMortemFeatures($predictions);

        return [
            'injury_count' => count($predictions),
            'classifications' => $classifications,
            'severity_assessment' => $overallSeverity,
            'cause_suggestions' => array_unique($causeSuggestions),
            'post_mortem_features' => $postMortemFeatures,
            'summary' => $this->generateSummary($classifications, $overallSeverity, $postMortemFeatures),
        ];
    }

    /**
     * Extract bounding box coordinates from prediction
     */
    protected function extractBoundingBox(array $prediction): array
    {
        $x = $prediction['x'] ?? 0;
        $y = $prediction['y'] ?? 0;
        $width = $prediction['width'] ?? 0;
        $height = $prediction['height'] ?? 0;

        return [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'xmin' => max(0, (int)($x - $width / 2)),
            'ymin' => max(0, (int)($y - $height / 2)),
            'xmax' => (int)($x + $width / 2),
            'ymax' => (int)($y + $height / 2),
        ];
    }

    /**
     * Calculate area of bounding box
     */
    protected function calculateArea(array $bbox): float
    {
        return $bbox['width'] * $bbox['height'];
    }

    /**
     * Assess injury severity based on area and characteristics
     */
    protected function assessSeverity(array $bbox, float $area): string
    {
        // Severity thresholds based on area
        if ($area < 1000) {
            return 'ringan';
        } elseif ($area < 3000) {
            return 'sedang';
        } else {
            return 'parah';
        }
    }

    /**
     * Suggest probable cause of injury
     */
    protected function suggestCause(array $prediction, string $severity, float $area): string
    {
        $class = strtolower($prediction['class'] ?? '');
        
        // Fracture-specific analysis
        if (str_contains($class, 'fracture')) {
            if ($severity === 'parah') {
                return 'blunt trauma (high impact)';
            } elseif ($severity === 'sedang') {
                return 'blunt trauma (moderate impact)';
            } else {
                return 'stress fracture or minor trauma';
            }
        }

        // General injury analysis
        if ($severity === 'parah') {
            return 'blunt trauma';
        } elseif ($severity === 'sedang') {
            return 'sharp force';
        } else {
            return 'minor trauma';
        }
    }

    /**
     * Classify injury type
     */
    protected function classifyInjury(array $prediction): string
    {
        $class = strtolower($prediction['class'] ?? 'unknown');
        
        // Map detected classes to injury classifications
        if (str_contains($class, 'fracture')) {
            return 'Fracture';
        } elseif (str_contains($class, 'bruise')) {
            return 'Bruise';
        } elseif (str_contains($class, 'burn')) {
            return 'Burn';
        } elseif (str_contains($class, 'laceration')) {
            return 'Laceration';
        }
        
        return 'Unclassified Injury';
    }

    /**
     * Detect post-mortem features and artifacts
     */
    protected function detectPostMortemFeatures(array $predictions): array
    {
        if (empty($predictions)) {
            return [
                'detected' => true,
                'features' => ['No region detected - possible post-mortem'],
                'confidence' => 'low',
            ];
        }

        $anomalies = [];
        $artifactCount = 0;

        foreach ($predictions as $prediction) {
            $bbox = $this->extractBoundingBox($prediction);
            $area = $this->calculateArea($bbox);

            // Detect artifacts based on unusual sizes
            if ($area > 6000) {
                $anomalies[] = 'Large region detected - possible decomposition or artifact';
                $artifactCount++;
            } elseif ($area < 300) {
                $anomalies[] = 'Very small region - possible artifact or early stage injury';
                $artifactCount++;
            }

            // Check confidence levels
            $confidence = $prediction['confidence'] ?? 0;
            if ($confidence < 0.5) {
                $anomalies[] = 'Low confidence detection - possible artifact';
                $artifactCount++;
            }
        }

        if (empty($anomalies)) {
            return [
                'detected' => false,
                'features' => ['No post-mortem features detected'],
                'confidence' => 'high',
            ];
        }

        return [
            'detected' => true,
            'features' => $anomalies,
            'artifact_count' => $artifactCount,
            'confidence' => $artifactCount > 2 ? 'high' : 'medium',
        ];
    }

    /**
     * Determine overall severity from multiple injuries
     */
    protected function determineOverallSeverity(array $severityLevels): string
    {
        if (empty($severityLevels)) {
            return 'none';
        }

        if (in_array('parah', $severityLevels)) {
            return 'parah (severe)';
        } elseif (in_array('sedang', $severityLevels)) {
            return 'sedang (moderate)';
        } else {
            return 'ringan (mild)';
        }
    }

    /**
     * Generate comprehensive summary
     */
    protected function generateSummary(array $classifications, string $overallSeverity, array $postMortem): string
    {
        $count = count($classifications);
        $summary = "Forensic Analysis Summary:\n\n";
        
        $summary .= "Total Injuries Detected: {$count}\n";
        $summary .= "Overall Severity: {$overallSeverity}\n\n";

        if ($count > 0) {
            $summary .= "Injury Details:\n";
            foreach ($classifications as $i => $class) {
                $num = $i + 1;
                $summary .= "{$num}. {$class['type']} ({$class['class']})\n";
                $summary .= "   - Confidence: {$class['confidence']}%\n";
                $summary .= "   - Severity: {$class['severity']}\n";
                $summary .= "   - Probable Cause: {$class['cause']}\n";
                $summary .= "   - Area: " . round($class['area'], 2) . " px²\n\n";
            }
        }

        if ($postMortem['detected']) {
            $summary .= "Post-Mortem Analysis:\n";
            foreach ($postMortem['features'] as $feature) {
                $summary .= "- {$feature}\n";
            }
        }

        return $summary;
    }

    /**
     * Create annotated image with bounding boxes and labels
     */
    public function createAnnotatedImage(string $imagePath, array $predictions, string $outputPath): bool
    {
        try {
            if (!extension_loaded('gd')) {
                throw new \Exception('GD extension not loaded');
            }

            $image = @imagecreatefromstring(file_get_contents($imagePath));
            if (!$image) {
                throw new \Exception('Failed to load image');
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Colors
            $green = imagecolorallocate($image, 0, 255, 0);
            $red = imagecolorallocate($image, 255, 0, 0);
            $yellow = imagecolorallocate($image, 255, 255, 0);
            $white = imagecolorallocate($image, 255, 255, 255);

            foreach ($predictions as $prediction) {
                $bbox = $this->extractBoundingBox($prediction);
                $area = $this->calculateArea($bbox);
                $severity = $this->assessSeverity($bbox, $area);
                
                // Choose color based on severity
                $color = match($severity) {
                    'parah' => $red,
                    'sedang' => $yellow,
                    default => $green,
                };

                // Draw rectangle
                imagerectangle(
                    $image,
                    $bbox['xmin'],
                    $bbox['ymin'],
                    $bbox['xmax'],
                    $bbox['ymax'],
                    $color
                );

                // Draw thicker border
                imagerectangle(
                    $image,
                    $bbox['xmin'] + 1,
                    $bbox['ymin'] + 1,
                    $bbox['xmax'] - 1,
                    $bbox['ymax'] - 1,
                    $color
                );

                // Add label
                $label = ($prediction['class'] ?? 'injury') . ' ' . 
                         round(($prediction['confidence'] ?? 0) * 100) . '%';
                
                imagestring($image, 5, $bbox['xmin'], max(15, $bbox['ymin'] - 15), $label, $color);
            }

            // Save annotated image
            $result = imagepng($image, $outputPath);
            imagedestroy($image);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to create annotated image', [
                'error' => $e->getMessage(),
                'image' => $imagePath,
            ]);
            return false;
        }
    }
}
