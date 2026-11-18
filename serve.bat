@echo off
title PLN WebGIS - port 8000
cd /d "%~dp0"
php artisan serve --port=8000
pause
