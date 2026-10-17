# scripts/pre-commit.ps1
Write-Host "Pre-commit checks..." -ForegroundColor Cyan
$errors = 0

Write-Host "`n[1/3] PHP Syntax Check..." -ForegroundColor Yellow
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    $r = php -l $_.FullName 2>&1
    if ($r -notmatch "No syntax errors") {
        Write-Host "  SYNTAX ERROR: $($_.Name)" -ForegroundColor Red
        Write-Host "  $r" -ForegroundColor DarkRed
        $script:errors++
    }
}
if ($errors -eq 0) { Write-Host "  OK" -ForegroundColor Green }

Write-Host "`n[2/3] Duplicate Method Check..." -ForegroundColor Yellow
Get-ChildItem app\Observers\*.php, app\Policies\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $c = Get-Content $_.FullName -Raw
    $m = [regex]::Matches($c, 'function\s+(\w+)\s*\(') | ForEach-Object { $_.Groups[1].Value }
    $d = $m | Group-Object | Where-Object Count -gt 1
    if ($d) {
        Write-Host "  DUPLICATE: $($_.Name)" -ForegroundColor Red
        $d | ForEach-Object { Write-Host "    - $($_.Name) ($($_.Count)x)" }
        $script:errors++
    }
}
if ($errors -eq 0) { Write-Host "  OK" -ForegroundColor Green }

Write-Host "`n[3/3] Double PHP Tag Check..." -ForegroundColor Yellow
$phpErrors = 0
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    $tags = Get-Content $_.FullName | Select-String -Pattern '^<\?php'
    if ($tags.Count -gt 1) {
        Write-Host "  DOUBLE <?php: $($_.Name)" -ForegroundColor Red
        $tags | ForEach-Object { Write-Host "    line $($_.LineNumber)" -ForegroundColor DarkRed }
        $script:phpErrors++
    }
}
if ($phpErrors -eq 0) { Write-Host "  OK" -ForegroundColor Green }
$errors += $phpErrors

Write-Host ""
if ($errors -eq 0) {
    Write-Host "Semua check lolos. Siap commit." -ForegroundColor Green
    exit 0
} else {
    Write-Host "$errors masalah ditemukan." -ForegroundColor Red
    exit 1
}
