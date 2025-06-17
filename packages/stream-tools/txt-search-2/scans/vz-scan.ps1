# Sicherstellen, dass das Skriptverzeichnis korrekt erkannt wird
$ScriptPath = $MyInvocation.MyCommand.Definition
$ScriptDir = Split-Path -Path $ScriptPath -Parent

# Laufwerk ermitteln
$drive = $ScriptDir.Substring(0, 2)
$driveLetter = $drive.Substring(0,1)

# Volumenbezeichnung holen
try {
    $volume = (Get-Volume -DriveLetter $driveLetter).FileSystemLabel
} catch {
    $volume = "UNBEKANNT"
}
if ([string]::IsNullOrWhiteSpace($volume)) {
    $volume = "UNBEKANNT"
}

# Dateinamen bereinigen
$cleanName = ($volume -replace '[\\/:*?"<>|]', '_')
$outputTxt = Join-Path $ScriptDir "$cleanName.txt"

# Ausgabe starten
"===========================================" | Out-File -FilePath $outputTxt -Encoding utf8
"SCAN von Laufwerk $drive am $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"===========================================" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"" | Out-File -FilePath $outputTxt -Encoding utf8 -Append

"[VERZEICHNIS- UND DATEILISTE]" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"" | Out-File -FilePath $outputTxt -Encoding utf8 -Append

# Dateiliste aufbauen
Get-ChildItem -Path $drive -Recurse -Force -ErrorAction SilentlyContinue | ForEach-Object {
    try {
        "$($_.LastWriteTime) [$($_.Length) Bytes] $($_.FullName)"
    } catch {
        "?? [Ordner oder kein Zugriff] $($_.FullName)"
    }
} | Out-File -FilePath $outputTxt -Encoding utf8 -Append

"" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"===========================================" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"SCAN ENDE: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" | Out-File -FilePath $outputTxt -Encoding utf8 -Append
"===========================================" | Out-File -FilePath $outputTxt -Encoding utf8 -Append

Write-Host "✅ Scan abgeschlossen. Datei gespeichert als: $outputTxt"
pause
