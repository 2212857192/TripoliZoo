@echo off
echo Starting TripoliZoo on all network interfaces (port 8000)...
echo Open admin from: http://192.168.7.3:8000/admin/animals
echo.
php artisan serve --host=0.0.0.0 --port=8000
