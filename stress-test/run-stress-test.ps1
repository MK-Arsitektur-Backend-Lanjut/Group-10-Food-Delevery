# Stress Test Runner (Windows PowerShell)
# Usage:
#   .\stress-test\run-stress-test.ps1
#   .\stress-test\run-stress-test.ps1 -Vus 20 -Duration "3m"

param(
    [string]$BaseUrl = "http://localhost:8000/api",
    [int]$Vus = 10,
    [string]$Duration = "2m",
    [string]$DriverEmail = "stresstest-driver@example.com",
    [string]$DriverPassword = "password123"
)

$ErrorActionPreference = "Stop"
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$K6Script = Join-Path $ScriptDir "k6-stress-test.js"

# Cek k6 terinstall
$k6 = Get-Command k6 -ErrorAction SilentlyContinue
if (-not $k6) {
    Write-Host ""
    Write-Host "k6 belum terinstall." -ForegroundColor Red
    Write-Host "Install via Chocolatey : choco install k6" -ForegroundColor Yellow
    Write-Host "Atau winget          : winget install k6 --source winget" -ForegroundColor Yellow
    Write-Host "Atau unduh manual    : https://k6.io/docs/get-started/installation/" -ForegroundColor Yellow
    Write-Host ""
    exit 1
}

Write-Host "Menjalankan stress test..." -ForegroundColor Cyan
Write-Host "  Base URL : $BaseUrl"
Write-Host "  VUs      : $Vus"
Write-Host "  Duration : $Duration"
Write-Host ""

$env:BASE_URL = $BaseUrl
$env:VUS = $Vus
$env:DURATION = $Duration
$env:DRIVER_EMAIL = $DriverEmail
$env:DRIVER_PASSWORD = $DriverPassword

k6 run $K6Script
