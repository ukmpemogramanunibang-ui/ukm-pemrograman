<?php
// storage/audit/check_roles.php
echo "=== Dashboard Route per Level ===\n";
foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $lvl) {
    $u = App\Models\User::where('level_id', $lvl)->first();
    if (!$u) {
        echo "level {$lvl}: (tidak ada user)\n";
        continue;
    }
    $route = $u->getDashboardRoute();
    $roles = implode(',', $u->getRoleNames()->toArray());
    echo "level {$lvl}: {$u->name} | roles=[{$roles}] | dashboard={$route}\n";
}

echo "\n=== Level 7 User Details ===\n";
foreach (App\Models\User::where('level_id', 7)->get() as $u) {
    echo "  {$u->id}: {$u->name} roles=[" . implode(',', $u->getRoleNames()->toArray()) . "]\n";
}