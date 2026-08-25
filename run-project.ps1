$ErrorActionPreference = 'Stop'

$projectFolder = Split-Path -Parent $MyInvocation.MyCommand.Path
$backendFolder = Join-Path $projectFolder 'backend'
$frontendFolder = Join-Path $projectFolder 'frontend'
$runtimeFolder = Join-Path $projectFolder 'runtime'
$phpFile = 'C:\xampp\php\php.exe'
$mysqlStartFile = 'C:\xampp\mysql_start.bat'

New-Item -ItemType Directory -Path $runtimeFolder -Force | Out-Null

function Test-Port([int]$port) {
    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $result = $client.BeginConnect('127.0.0.1', $port, $null, $null)
        if (-not $result.AsyncWaitHandle.WaitOne(700)) { return $false }
        $client.EndConnect($result)
        return $true
    } catch {
        return $false
    } finally {
        $client.Close()
    }
}

function Wait-For([scriptblock]$check, [string]$name) {
    for ($attempt = 1; $attempt -le 30; $attempt++) {
        if (& $check) {
            Write-Host "$name is ready." -ForegroundColor Green
            return
        }
        Start-Sleep -Milliseconds 500
    }
    throw "$name did not start. Check the files in the runtime folder."
}

if (-not (Test-Path $phpFile)) {
    throw 'PHP was not found in C:\xampp\php\php.exe'
}

Write-Host 'Starting TaskFlow...' -ForegroundColor Cyan

if (-not (Test-Port 3306)) {
    if (-not (Test-Path $mysqlStartFile)) { throw 'XAMPP MySQL was not found.' }
    Start-Process -FilePath $mysqlStartFile -WorkingDirectory 'C:\xampp' -WindowStyle Hidden
    Wait-For { Test-Port 3306 } 'MySQL'
} else {
    Write-Host 'MySQL is ready.' -ForegroundColor Green
}

$apiUrl = 'http://127.0.0.1:8000/api.php?action=health'
$apiReady = $false
try { $apiReady = (Invoke-RestMethod -Uri $apiUrl -TimeoutSec 2).ok -eq $true } catch {}

if (-not $apiReady) {
    Start-Process -FilePath $phpFile `
        -ArgumentList '-S', '127.0.0.1:8000', '-t', $backendFolder `
        -WorkingDirectory $backendFolder `
        -WindowStyle Hidden `
        -RedirectStandardOutput (Join-Path $runtimeFolder 'php-output.log') `
        -RedirectStandardError (Join-Path $runtimeFolder 'php-error.log')
}

Wait-For {
    try { return (Invoke-RestMethod -Uri $apiUrl -TimeoutSec 2).ok -eq $true } catch { return $false }
} 'PHP API'

$frontendUrl = 'http://127.0.0.1:4300'
$frontendReady = $false
try { $frontendReady = (Invoke-WebRequest -UseBasicParsing -Uri $frontendUrl -TimeoutSec 2).StatusCode -eq 200 } catch {}

if (-not $frontendReady) {
    Start-Process -FilePath 'npm.cmd' `
        -ArgumentList 'start', '--', '--host', '127.0.0.1', '--port', '4300' `
        -WorkingDirectory $frontendFolder `
        -WindowStyle Hidden `
        -RedirectStandardOutput (Join-Path $runtimeFolder 'angular-output.log') `
        -RedirectStandardError (Join-Path $runtimeFolder 'angular-error.log')
}

Wait-For {
    try { return (Invoke-WebRequest -UseBasicParsing -Uri $frontendUrl -TimeoutSec 2).StatusCode -eq 200 } catch { return $false }
} 'Angular'

Write-Host ''
Write-Host 'TaskFlow is ready: http://127.0.0.1:4300' -ForegroundColor Cyan
Write-Host 'You can close this window. The project will keep running.' -ForegroundColor DarkGray
Start-Process $frontendUrl
