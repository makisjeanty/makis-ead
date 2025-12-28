<#
Enable PHP intl extension script

Usage:
  - Run without args to auto-detect CLI php.ini:  .\enable-intl.ps1
  - Or pass a path: .\enable-intl.ps1 -IniPath 'C:\path\to\php.ini'

This script will:
  1. Detect the CLI php.ini (or use provided path).
  2. Back it up to php.ini.bak.timestamp.
  3. Uncomment or add `extension=php_intl.dll` (Windows) or `extension=intl` (non-Windows) as appropriate.

Note: You may need to restart services (Apache/XAMPP, Herd app, PHP-FPM) after running.
#>

param(
    [string]$IniPath
)

function Backup-File($path) {
    $ts = Get-Date -Format "yyyyMMddHHmmss"
    $bak = "$path.bak.$ts"
    Copy-Item -Path $path -Destination $bak -Force
    Write-Host "Backup created: $bak"
}

if (-not $IniPath) {
    $iniOutput = & php --ini 2>&1
    $match = $iniOutput | Select-String "Loaded Configuration File" -SimpleMatch
    if ($match) {
        $IniPath = ($match -split ':',2)[1].Trim()
    }
}

if (-not $IniPath -or -not (Test-Path $IniPath)) {
    Write-Error "php.ini not found. Provide path with -IniPath or ensure 'php' is on PATH."
    exit 1
}

Write-Host "Using php.ini: $IniPath"
Backup-File $IniPath

$content = Get-Content $IniPath -Raw
if ($IsWindows) {
    # Windows: look for php_intl.dll
    if ($content -match "^[\s;]*extension\s*=\s*php_intl\.dll" -or $content -match "php_intl\.dll") {
        # replace commented or existing line
        $new = $content -replace "^[\s;]*extension\s*=\s*php_intl\.dll.*","extension=php_intl.dll" -replace "(?m)^;\s*extension=php_intl\.dll","extension=php_intl.dll"
    } else {
        $new = $content + "`r`nextension=php_intl.dll`r`n"
    }
} else {
    # non-Windows: use intl
    if ($content -match "^[\s;]*extension\s*=\s*intl" -or $content -match "\bintl\b") {
        $new = $content -replace "^[\s;]*extension\s*=\s*intl.*","extension=intl" -replace "(?m)^;\s*extension=intl","extension=intl"
    } else {
        $new = $content + "`nextension=intl`n"
    }
}

Set-Content -Path $IniPath -Value $new -Force
Write-Host "Updated php.ini. Please restart your webserver/PHP service and verify with:"
Write-Host "  php -m | Select-String intl"
