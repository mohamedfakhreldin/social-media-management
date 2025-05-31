@echo off
cd C:\Users\mido\.config\herd\getpayin
php artisan schedule:run >> NUL 2>&1
