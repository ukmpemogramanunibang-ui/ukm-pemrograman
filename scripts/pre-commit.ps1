# scripts/pre-commit.ps1
Write-Host "Pre-commit checks..." -ForegroundColor Cyan
$errors = 0

# 1. Syntax check
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    $result = php -l $_.FullName 2>&1
    if ($result -notmatch "No syntax errors") {
        Write-Host "SYNTAX ERROR: $($_.Name)" -ForegroundColor Red
        Write-Host "   $result" -ForegroundColor DarkRed
        $errors++
    }
}

# 2. Duplicate methods
Get-ChildItem app\Observers\*.php, app\Policies\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $methods = [regex]::Matches($content, 'function\s+(\w+)\s*\(') | ForEach-Object { $_.Groups[1].Value }
    $dupes = $methods | Group-Object | Where-Object Count -gt 1
    if ($dupes) {
        Write-Host "DUPLICATE METHOD: $($_.Name)" -ForegroundColor Red
        $dupes | ForEach-Object { Write-Host "   - $($_.Name) ($($_.Count)x)" -ForegroundColor DarkRed }
        $errors++
    }
}

# 3. Double <?php
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if (($content | Select-String -Pattern '<\?php' -AllMatches).Matches.Count -gt 1) {
        Write-Host "DOUBLE PHP TAG: $($_.Name)" -ForegroundColor Red
        $errors++
    }
}

if ($errors -eq 0) {
    Write-Host "Semua check lolos. Siap commit." -ForegroundColor Green
    exit 0
} else {
    Write-Host "$errors masalah ditemukan." -ForegroundColor Red
    exit 1
}
