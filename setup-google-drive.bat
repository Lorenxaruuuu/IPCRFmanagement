@echo off
REM Google Drive Integration Setup Script
REM This script automates the setup process

echo.
echo ========================================
echo Google Drive Integration Setup
echo ========================================
echo.

REM Check if we're in the right directory
if not exist "config\services.php" (
    echo ERROR: Please run this script from the project root directory!
    exit /b 1
)

echo [1/5] Creating credentials directory...
if not exist "storage\credentials" (
    mkdir storage\credentials
    echo ✓ Credentials directory created
) else (
    echo ✓ Credentials directory already exists
)

echo.
echo [2/5] Checking for credentials file...
if exist "storage\credentials\google-drive-credentials.json" (
    echo ✓ Credentials file found
) else (
    echo ✗ Credentials file NOT found
    echo.
    echo Please copy your google-drive-credentials.json file to:
    echo   storage\credentials\google-drive-credentials.json
    echo.
    pause
)

echo.
echo [3/5] Clearing Laravel cache...
call php artisan config:clear
call php artisan cache:clear
echo ✓ Cache cleared

echo.
echo [4/5] Running database migration...
call php artisan migrate
echo ✓ Migration completed

echo.
echo [5/5] Final verification...
echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Update your .env file:
echo    GOOGLE_DRIVE_ENABLE=true
echo    GOOGLE_DRIVE_FOLDER_ID=
echo.
echo 2. (Optional) Set your Google Drive folder ID:
echo    - Create a folder in Google Drive
echo    - Copy the folder ID from the URL
echo    - Add to .env as GOOGLE_DRIVE_FOLDER_ID=your_id
echo.
echo 3. Test the upload:
echo    - Go to your upload form
echo    - Complete and submit
echo    - Check Google Drive for the file
echo.
echo Documentation:
echo - GOOGLE_DRIVE_QUICKSTART.md - Quick start guide
echo - GOOGLE_DRIVE_SETUP.md - Detailed setup
echo - IMPLEMENTATION_CHECKLIST.md - Feature checklist
echo.
pause
