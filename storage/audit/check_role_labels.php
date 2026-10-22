<?php
// storage/audit/check_role_labels.php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "═══════════════════════════════════════════════════════\n";
echo "  CEK LABEL ROLE SPESIFIK\n";
echo "═══════════════════════════════════════════════════════\n\n";

/* ─── 1. Cek levels.display_name ─── */
echo "=== [1] LEVELS DISPLAY NAME ===\n";
DB::table('levels')->orderBy('id')->get(['id','name','display_name'])
    ->each(fn($l) => print("  Level {$l->id} ({$l->name}): {$l->display_name}\n"));

/* ─── 2. Cek roles.name (TIDAK BERUBAH) ─── */
echo "\n=== [2] SPATIE ROLES (HARUS TETAP SAMA) ===\n";
DB::table('roles')->whereIn('id', [1,2,3,4,5,10])->orderBy('id')->get(['id','name'])
    ->each(fn($r) => print("  Role {$r->id}: {$r->name}\n"));

/* ─── 3. Cek position_label per ketua ─── */
echo "\n=== [3] KETUA + POSITION LABEL ===\n";
$ketuas = DB::table('organization_memberships')
    ->join('users', 'users.id', '=', 'organization_memberships.user_id')
    ->join('organizations', 'organizations.id', '=', 'organization_memberships.organization_id')
    ->where('organization_memberships.access_role', 'leader')
    ->where('organization_memberships.membership_status', 'active')
    ->select(
        'users.id as user_id', 'users.name as user_name',
        'organizations.name as org_name', 'organizations.slug as org_slug',
        'organization_memberships.position_label'
    )
    ->get();

foreach ($ketuas as $k) {
    $label = $k->position_label ?? '❌ (null)';
    echo "  User {$k->user_id} ({$k->user_name}) @ {$k->org_slug}\n";
    echo "    → {$label}\n";
}

/* ─── 4. Cek accessor role_display ─── */
echo "\n=== [4] TEST ACCESSOR role_display ===\n";
foreach ([3, 4, 5, 6, 8] as $lvl) {
    $u = User::where('level_id', $lvl)->first();
    if (!$u) continue;
    echo "  Level {$lvl} ({$u->name}): " . $u->role_display . "\n";
}

/* ─── 5. Cek tidak ada double method di User.php ─── */
echo "\n=== [5] CEK DUPLIKASI METHOD User.php ===\n";
$content = file_get_contents(app_path('Models/User.php'));
preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches);
$dupes = array_filter(array_count_values($matches[1]), fn($c) => $c > 1);
if (empty($dupes)) {
    echo "  ✅ Tidak ada duplikasi method.\n";
} else {
    foreach ($dupes as $m => $c) {
        echo "  ❌ {$m}: {$c}x\n";
    }
}

echo "\n═══════════════════════════════════════════════════════\n";