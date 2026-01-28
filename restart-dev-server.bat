@echo off
REM Kill any existing PHP processes
taskkill /F /IM php.exe 2>nul >nul

REM Wait a moment
timeout /t 2 /nobreak

REM Start the dev server
cd /d "C:\Users\Rem\Documents\New folder\DisasterTraining\my-app"
echo.
echo ========================================
echo Starting Laravel Development Server
echo ========================================
echo.
echo Server will be available at: http://127.0.0.1:8000
echo.
echo Press Ctrl+C to stop the server
echo.
php artisan serve --host=127.0.0.1 --port=8000

pause
