# scripts/check-route-duplicates.ps1
Write-Host "Route Audit — Duplicate Detection" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor DarkGray

$routes = php artisan route:list --json | ConvertFrom-Json

# 1. Duplicate NAME (paling berbahaya — yang kedua menang)
Write-Host "`n[1] Duplicate by NAME (destructive — kedua menimpa pertama):" -ForegroundColor Yellow
$byName = $routes | Where-Object { $_.name } | Group-Object name | Where-Object Count -gt 1
if ($byName.Count -eq 0) {
    Write-Host "  OK — no duplicate names." -ForegroundColor Green
} else {
    foreach ($dup in ($byName | Sort-Object Count -Descending)) {
        Write-Host "  [$($dup.Count)x] $($dup.Name)" -ForegroundColor Red
        foreach ($route in $dup.Group) {
            $methods = if ($route.method -is [array]) { $route.method -join '|' } else { $route.method }
            Write-Host "       $methods  $($route.uri)" -ForegroundColor DarkGray
        }
    }
    $hasError = $true
}

# 2. Duplicate PATH+METHOD (path sama, method sama = true duplicate)
Write-Host "`n[2] Duplicate by PATH + METHOD (true duplicates):" -ForegroundColor Yellow
$byPathMethod = @{}
foreach ($r in $routes) {
    $methods = if ($r.method -is [array]) { ($r.method | Sort-Object) -join '|' } else { $r.method }
    $key = "$methods|$($r.uri)"
    if (-not $byPathMethod.ContainsKey($key)) {
        $byPathMethod[$key] = @()
    }
    $byPathMethod[$key] += $r
}
$pathDupes = $byPathMethod.GetEnumerator() | Where-Object { $_.Value.Count -gt 1 }
if ($pathDupes.Count -eq 0) {
    Write-Host "  OK — no duplicate path+method." -ForegroundColor Green
} else {
    foreach ($entry in $pathDupes) {
        Write-Host "  [$($entry.Value.Count)x] $($entry.Key)" -ForegroundColor Red
        foreach ($r in $entry.Value) {
            Write-Host "       name: $($r.name)  source: $($r.source)" -ForegroundColor DarkGray
        }
    }
    $hasError = $true
}

Write-Host ""
if ($hasError) {
    Write-Host "DUPLICATES FOUND — fix in routes/web.php" -ForegroundColor Red
    exit 1
} else {
    Write-Host "All clean." -ForegroundColor Green
    exit 0
}
