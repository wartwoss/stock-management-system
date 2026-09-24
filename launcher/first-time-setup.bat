@echo off
:: ============================================================
:: StockFlow First-Time Setup — Google Drive Backup via rclone
:: Run this ONCE to configure Google Drive backups.
:: ============================================================

echo.
echo ============================================================
echo  StockFlow — Google Drive Backup Setup
echo ============================================================
echo.
echo This will download rclone and connect it to your Google Drive.
echo You will be asked to open a browser and log in to Google.
echo.
pause

set RCLONE_DIR=%~dp0rclone
set RCLONE_EXE=%RCLONE_DIR%\rclone.exe

:: Check if rclone already exists
if exist "%RCLONE_EXE%" (
    echo [Setup] rclone already downloaded.
    goto :configure
)

:: Download rclone
echo [Setup] Downloading rclone...
mkdir "%RCLONE_DIR%" 2>NUL

powershell -Command "& { $url = 'https://downloads.rclone.org/rclone-current-windows-amd64.zip'; $zip = '%RCLONE_DIR%\rclone.zip'; Invoke-WebRequest -Uri $url -OutFile $zip -UseBasicParsing; Expand-Archive -Path $zip -DestinationPath '%RCLONE_DIR%\tmp' -Force; $exe = Get-ChildItem '%RCLONE_DIR%\tmp' -Recurse -Filter 'rclone.exe' | Select-Object -First 1; Copy-Item $exe.FullName '%RCLONE_EXE%'; Remove-Item '%RCLONE_DIR%\tmp' -Recurse -Force; Remove-Item $zip -Force }"

if not exist "%RCLONE_EXE%" (
    echo [Setup] ERROR: rclone download failed. Check your internet connection.
    pause
    exit /b 1
)

echo [Setup] rclone downloaded successfully!

:configure
echo.
echo [Setup] Configuring Google Drive...
echo.
echo When prompted:
echo   1. Press N for "New remote"
echo   2. Name it: gdrive
echo   3. Choose Google Drive (option number)
echo   4. Leave Client ID and Secret blank (press Enter)
echo   5. Choose "Full access" scope (option 1)
echo   6. Press Enter for defaults, then Y to open browser
echo   7. Log in to your Google account
echo   8. Press Q to quit when done
echo.
pause

"%RCLONE_EXE%" config

echo.
echo ============================================================
echo  Setup complete!
echo  Backups will be uploaded to: gdrive:/stockflow-backups
echo ============================================================
echo.
echo You can now use the "Backup to Google Drive" button in Settings.
echo.
pause
