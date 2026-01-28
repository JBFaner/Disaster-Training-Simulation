@echo off
cd "C:\Users\Rem\Documents\New folder\DisasterTraining\my-app"
echo Restarting Laravel dev server...
taskkill /F /IM php.exe 2>nul
timeout /t 2
php artisan serve
pause
