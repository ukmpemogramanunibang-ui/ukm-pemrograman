# scripts/pre-commit.ps1
Write-Host "Pre-commit checks..." -ForegroundColor Cyan
$errors = 0

# 1. PHP Syntax
Write-Host "`n[1/4] PHP Syntax Check..." -ForegroundColor Yellow
Get-ChildItem app, routes, database -Recurse -Filter *.php -ErrorAction SilentlyContinue | ForEach-Object {
    $r = php -l $_.FullName 2>&1
    if ($r -notmatch "No syntax errors") {
        Write-Host "  SYNTAX ERROR: $($_.Name)" -ForegroundColor Red
        Write-Host "  $r" -ForegroundColor DarkRed
        $script:errors++
    }
}
if ($errors -eq 0) { Write-Host "  OK" -ForegroundColor Green }

# 2. Duplicate Methods
Write-Host "`n[2/4] Duplicate Method Check..." -ForegroundColor Yellow
Get-ChildItem app\Observers\*.php, app\Policies\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $c = Get-Content $_.FullName -Raw
    $m = [regex]::Matches($c, 'function\s+(\w+)\s*\(') | ForEach-Object { $_.Groups[1].Value }
    $d = $m | Group-Object | Where-Object Count -gt 1
    if ($d) {
        Write-Host "  DUPLICATE: $($_.Name)" -ForegroundColor Red
        $d | ForEach-Object { Write-Host "    - $($_.Name) ($($_.Count)x)" -ForegroundColor DarkRed }
        $script:errors++
    }
}
if ($errors -eq 0) { Write-Host "  OK" -ForegroundColor Green }

# 3. Double PHP Tag
Write-Host "`n[3/4] Double PHP Tag Check..." -ForegroundColor Yellow
$phpErrors = 0
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    $tags = Get-Content $_.FullName | Select-String -Pattern '^<\?php'
    if ($tags.Count -gt 1) {
        Write-Host "  DOUBLE <?php: $($_.Name)" -ForegroundColor Red
        $phpErrors++
    }
}
if ($phpErrors -eq 0) { Write-Host "  OK" -ForegroundColor Green }
$errors += $phpErrors

# 4. Route Duplicates
Write-Host "`n[4/4] Route Duplicate Check..." -ForegroundColor Yellow
try {
    $result = & .\scripts\check-route-duplicates.ps1 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  ROUTE DUPLICATES FOUND" -ForegroundColor Red
        Write-Host $result -ForegroundColor DarkRed
        $script:errors++
    } else {
        Write-Host "  OK" -ForegroundColor Green
    }
} catch {
    Write-Host "  (route:list failed, skipped)" -ForegroundColor DarkGray
}

Write-Host ""
if ($errors -eq 0) {
    Write-Host "Semua check lolos. Siap commit." -ForegroundColor Green
    exit 0
} else {
    Write-Host "$errors masalah ditemukan." -ForegroundColor Red
    exit 1
}
