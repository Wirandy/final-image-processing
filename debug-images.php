<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "  DEBUG IMAGES\n";
echo "========================================\n\n";

// Get all images
$images = App\Models\StudyImage::with('patient')->get();

echo "Total images in database: " . $images->count() . "\n\n";

if ($images->count() > 0) {
    echo "Images list:\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($images as $img) {
        echo "ID: " . $img->id . "\n";
        echo "Patient ID: " . $img->patient_id . "\n";
        echo "Patient Name: " . ($img->patient ? $img->patient->name : 'NULL') . "\n";
        echo "Original Path: " . $img->original_path . "\n";
        echo "File exists: " . (file_exists(storage_path('app/public/' . $img->original_path)) ? 'YES' : 'NO') . "\n";
        echo str_repeat("-", 80) . "\n";
    }
} else {
    echo "No images found in database.\n";
}

// Get all patients
$patients = App\Models\Patient::withCount('images')->get();
echo "\nPatients:\n";
echo str_repeat("-", 80) . "\n";
foreach ($patients as $patient) {
    echo "ID: " . $patient->id . "\n";
    echo "Name: " . $patient->name . "\n";
    echo "Images count: " . $patient->images_count . "\n";
    echo str_repeat("-", 80) . "\n";
}
