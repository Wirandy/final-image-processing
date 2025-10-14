@echo off
echo ========================================
echo AIFI Annotation & Report Setup
echo ========================================
echo.

echo [1/4] Installing Python dependencies...
cd python
pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo ERROR: Failed to install Python dependencies
    pause
    exit /b 1
)
cd ..
echo Done!
echo.

echo [2/4] Running database migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed
    pause
    exit /b 1
)
echo Done!
echo.

echo [3/4] Creating storage directories...
if not exist "storage\app\public\uploads\annotated" mkdir "storage\app\public\uploads\annotated"
if not exist "storage\app\public\reports" mkdir "storage\app\public\reports"
echo Done!
echo.

echo [4/4] Setting permissions...
icacls "storage\app\public\uploads\annotated" /grant Everyone:(OI)(CI)F /T
icacls "storage\app\public\reports" /grant Everyone:(OI)(CI)F /T
echo Done!
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo New Features Available:
echo - Interactive Annotation Tools
echo - Measurement Tools (Distance, Angle, Area)
echo - Automated PDF Report Generation
echo.
echo Next Steps:
echo 1. Navigate to a patient page
echo 2. Click "Annotate" button on any image
echo 3. Use annotation and measurement tools
echo 4. Click "Generate Report" to create PDF
echo.
echo See ANNOTATION_MEASUREMENT_GUIDE.md for detailed documentation
echo.
pause
