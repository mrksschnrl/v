@echo off
SETLOCAL ENABLEDELAYEDEXPANSION

:: Always work from STREAM-CREATOR root
cd /d "%~dp0.."

set "INPUT_DIR=input_mp4"
set "OUTPUT_DIR=output_m3u8"
set "LOGFILE=logs/strict_ui_converter.log"

echo === STREAM-CREATOR STRICT CONVERTER ===
echo [START] %DATE% %TIME% >> %LOGFILE%

where ffmpeg >nul 2>&1
if errorlevel 1 (
    echo ❌ FFmpeg not found. Please install FFmpeg and add it to your PATH.
    echo [ERROR] FFmpeg not found >> %LOGFILE%
    pause
    exit /b
)

if not exist "%INPUT_DIR%" (
    echo ❌ Input folder not found: %INPUT_DIR%
    echo [ERROR] Input folder missing >> %LOGFILE%
    pause
    exit /b
)

if not exist "%OUTPUT_DIR%" (
    mkdir "%OUTPUT_DIR%"
    echo 📁 Created output folder: %OUTPUT_DIR%
    echo [INFO] Created output folder >> %LOGFILE%
)

for %%F in ("%INPUT_DIR%\*.mp4") do (
    set "FILENAME=%%~nF"
    set "OUTDIR=%OUTPUT_DIR%\!FILENAME!"
    
    if exist "!OUTDIR!\!FILENAME!.m3u8" (
        echo ⚠️ Output already exists for: !FILENAME!
        echo [SKIP] !FILENAME! already exists >> %LOGFILE%
        echo ---------------------------
        continue
    )

    mkdir "!OUTDIR!" >nul 2>&1
    echo 🔄 Converting: %%~nxF
    echo [CONVERT] %%~nxF started >> %LOGFILE%
    ffmpeg -i "%%F" -codec: copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls "!OUTDIR!\!FILENAME!.m3u8"

    if exist "!OUTDIR!\!FILENAME!.m3u8" (
        echo ✅ Successfully created: !OUTDIR!\!FILENAME!.m3u8
        echo [DONE] !FILENAME!.m3u8 created >> %LOGFILE%
    ) else (
        echo ❌ ERROR: Conversion failed for %%~nxF
        echo [ERROR] Conversion failed for %%~nxF >> %LOGFILE%
    )
    echo ---------------------------
)

echo ✅ All possible conversions complete.
echo [END] %DATE% %TIME% >> %LOGFILE%
pause
