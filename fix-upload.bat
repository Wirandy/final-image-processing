@echo off
echo ========================================
echo   FIX UPLOAD IMAGE ERROR
echo ========================================
echo.

echo [1/5] Creating storage folders...
if not exist "storage\app\public\uploads" mkdir "storage\app\public\uploads"
if not exist "storage\app\public\uploads\originals" mkdir "storage\app\public\uploads\originals"
if not exist "storage\app\public\uploads\processed" mkdir "storage\app\public\uploads\processed"
if not exist "storage\app\public\uploads\annotated" mkdir "storage\app\public\uploads\annotated"
echo Done!
echo.

echo [2/5] Creating storage link...
php artisan storage:link
echo.

echo [3/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo [4/5] Checking migration status...
php artisan migrate:status
echo.

echo [5/5] Testing permissions...
echo Testing write to storage folder...
echo test > storage\app\public\test.txt
if exist "storage\app\public\test.txt" (
    echo SUCCESS: Storage folder is writable!
    del storage\app\public\test.txt
) else (
    echo ERROR: Storage folder is NOT writable!
    echo Please run this script as Administrator
)
echo.

echo ========================================
echo   DONE!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure you are LOGGED IN
echo 2. Go to Patients page
echo 3. Select or create a patient
echo 4. Try uploading an image
echo.
echo If still error, check TROUBLESHOOTING_UPLOAD.md
echo.
pause
