@echo off
setlocal

:: ============================================================
:: StockFlow Launcher
:: Double-click this to start the Stock Management System.
:: ============================================================

set BACKEND_DIR=%~dp0..
set MYSQLD=C:\xampp\mysql\bin\mysqld.exe
set PHP=php
set PORT=8000
set CHROME="C:\Program Files\Google\Chrome\Application\chrome.exe"

echo [StockFlow] Starting Stock Management System...

:: --- Start MySQL if not running ---
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if %ERRORLEVEL% NEQ 0 (
    echo [StockFlow] Starting MySQL...
    start "" /B "%MYSQLD%" --defaults-file="C:\xampp\mysql\bin\my.ini"
    timeout /t 3 /nobreak >NUL
) else (
    echo [StockFlow] MySQL already running.
)

:: --- Start Laravel backend ---
tasklist /FI "IMAGENAME eq php.exe" 2>NUL | find /I "php.exe" >NUL
if %ERRORLEVEL% NEQ 0 (
    echo [StockFlow] Starting Laravel backend on port %PORT%...
    start "" /B cmd /c "cd /d "%BACKEND_DIR%" && php artisan serve --port=%PORT% --host=127.0.0.1 > launcher\backend.log 2>&1"
    timeout /t 4 /nobreak >NUL
    echo [StockFlow] Starting queue worker...
    start "" /B cmd /c "cd /d "%BACKEND_DIR%" && php artisan queue:work --sleep=3 --tries=3 > launcher\queue.log 2>&1"
) else (
    echo [StockFlow] PHP server may already be running.
)

:: --- Open Chrome ---
echo [StockFlow] Opening app in Chrome...
if exist %CHROME% (
    start "" %CHROME% --new-window "http://localhost:%PORT%"
) else (
    start "" "http://localhost:%PORT%"
)

echo [StockFlow] System started! Close this window to keep it running in background.
exit
