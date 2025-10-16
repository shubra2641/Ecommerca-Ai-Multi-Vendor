# PowerShell helper: generate launcher icons for the Flutter app
# Usage: Run from the project root (vendor_app):
#   .\scripts\generate_icons.ps1

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $projectRoot

$iconPath = Join-Path $projectRoot "assets\icons\app_icon.png"
if (-Not (Test-Path $iconPath)) {
    Write-Host "ERROR: launcher icon not found at $iconPath" -ForegroundColor Red
    Write-Host "Please add a valid PNG at assets/icons/app_icon.png (square, 512x512 recommended) and rerun this script."
    exit 1
}

Write-Host "Found icon: $iconPath" -ForegroundColor Green
Write-Host "Running flutter pub get..."
flutter pub get
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Generating launcher icons..."
# Preferred newer syntax
dart run flutter_launcher_icons:main
if ($LASTEXITCODE -ne 0) {
    Write-Host "Icon generation failed. Fall back to legacy command: flutter pub run flutter_launcher_icons:main" -ForegroundColor Yellow
    flutter pub run flutter_launcher_icons:main
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "Launcher icons generated successfully." -ForegroundColor Green
} else {
    Write-Host "Launcher icon generation failed. See errors above." -ForegroundColor Red
}
