@echo off
echo ========================================
echo   DEBUG UPLOAD IMAGE
echo ========================================
echo.

echo [1] Checking database connection...
php artisan tinker --execute="echo 'DB Connected: ' . (DB::connection()->getPdo() ? 'YES' : 'NO');"
echo.

echo [2] Checking study_images table...
php artisan tinker --execute="echo 'Total images in DB: ' . App\Models\StudyImage::count();"
echo.

echo [3] Checking last uploaded image...
php artisan tinker --execute="$img = App\Models\StudyImage::latest()->first(); if($img) { echo 'Last image ID: ' . $img->id . PHP_EOL; echo 'Patient ID: ' . $img->patient_id . PHP_EOL; echo 'Path: ' . $img->original_path . PHP_EOL; } else { echo 'No images found in database'; }"
echo.

echo [4] Checking storage folder...
dir storage\app\public\uploads\originals
echo.

echo [5] Checking public/storage symlink...
dir public\storage
echo.

echo ========================================
echo   DONE!
echo ========================================
echo.
echo If "Total images in DB" is 0, then images are not being saved to database.
echo If storage folder is empty, then files are not being uploaded.
echo.
pause
