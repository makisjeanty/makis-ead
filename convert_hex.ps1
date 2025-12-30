$bytes = [System.IO.File]::ReadAllBytes("public/images/brand/logo.png")
$hex = [BitConverter]::ToString($bytes) -replace '-'
Set-Content "logo.hex" $hex
