@echo off
SETLOCAL
set "INPUT_DIR=input_mp4"
set "OUTPUT_DIR=output_m3u8"

echo === STREAM-CREATOR .BAT CONVERTER ===
echo.
if not exist "%INPUT_DIR%" (
    mkdir "%INPUT_DIR%"
    echo Created input folder: %INPUT_DIR%
)

if not exist "%OUTPUT_DIR%" (
    mkdir "%OUTPUT_DIR%"
    echo Created output folder: %OUTPUT_DIR%
)

for %%F in (%INPUT_DIR%\*.mp4) do (
    echo Processing: %%F
    mkdir "%OUTPUT_DIR%\%%~nF"
    ffmpeg -i "%%F" -codec: copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls "%OUTPUT_DIR%\%%~nF\%%~nF.m3u8"
)
echo.
echo ✅ Done! Converted all .mp4 files to .m3u8.
pause
