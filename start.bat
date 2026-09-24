@echo off
title Stock Management System
echo ========================================
echo   Starting Stock Management System...
echo ========================================
echo.

REM Start MySQL via XAMPP if not already running
echo [1/2] Checking MySQL...
net start MySQL 2>nul || echo MySQL is already running or managed by XAMPP.

echo.
echo [2/2] Starting the application server...
echo.
echo System will open in your browser shortly.
echo DO NOT close this window while using the system.
echo To stop the system, close this window.
echo.

REM Wait a moment then open browser
start "" "http://localhost:8000"

REM Run Laravel server using XAMPP's PHP
C:\xampp\php\php.exe artisan serve

pause
