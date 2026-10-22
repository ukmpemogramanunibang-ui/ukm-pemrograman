<?php
// storage/audit/audit_final.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "═══════════════════════════════════════════════════════\n";
echo "  AUDIT FINAL UNICODE MULTI-ORGANIZATION\n";
echo "═══════════════════════════════════════════════════════\n\n";

/* ─── 1. Levels & Roles Mapping ─── */
echo "=== [1] LEVELS & ROLES ===\n";
$levels = DB::table('levels')->orderBy('id')->get(['id','name','display_name']);
$roles  = DB::table('roles')->orderBy('id')->get(['id','name']);

$map = [
    1 => 'owner', 2 => 'superadmin', 3 => 'ketua', 4 => 'pengurus',
    5 => 'anggota', 6 => 'anggota umum', 7 => 'pj/assisten', 8 => 'guest',
];

foreach ($levels as $l) {
    $roleName = $map[(int)$l->id] ?? 'N/A';
    $roleExists = $roles->contains('name', $roleName) ? '✅' : '❌ TIDAK ADA';
    echo "  Level {$l->id} ({$l->name}): Role '{$roleName}' {$roleExists}\n";
}

/* ─── 2. Users per Level ─── */
echo "\n=== [2] DISTRIBUSI USER PER LEVEL ===\n";
DB::table('users')->select('level_id', DB::raw('COUNT(*) as total'))
    ->groupBy('level_id')->orderBy('level_id')->get()
    ->each(fn($u) => print("  Level {$u->level_id}: {$u->total} user\n"));

/* ─── 3. Users missing Role ─── */
echo "\n=== [3] USER TANPA ROLE ===\n";
foreach ($levels as $l) {
    $roleName = $map[(int)$l->id] ?? null;
    if (!$roleName) continue;
    
    $role = DB::table('roles')->where('name', $roleName)->first();
    if (!$role) continue;
    
    $missing = DB::table('users')
        ->where('level_id', $l->id)
        ->whereNull('deleted_at')
        ->whereNotExists(function($q) use ($role) {
            $q->select(DB::raw(1))->from('model_has_roles')
              ->whereColumn('model_has_roles.model_id', 'users.id')
              ->where('model_has_roles.role_id', $role->id)
              ->where('model_has_roles.model_type', 'App\Models\User');
        })->count();
        
    if ($missing > 0) {
        echo "  ⚠️ Level {$l->id} ({$l->name}): {$missing} user belum punya role '{$roleName}'\n";
    } else {
        echo "  ✅ Level {$l->id} ({$l->name}): semua user sudah punya role\n";
    }
}

/* ─── 4. Organization & Memberships ─── */
echo "\n=== [4] ORGANISASI & KEANGGOTAAN ===\n";
echo "  Total Organisasi   : " . DB::table('organizations')->count() . "\n";
echo "  Total Membership   : " . DB::table('organization_memberships')->count() . "\n\n";

DB::table('organizations')->orderBy('id')->get(['id','name','slug','type','status'])
    ->each(function($o) {
        $count = DB::table('organization_memberships')->where('organization_id', $o->id)->count();
        $leaders = DB::table('organization_memberships')
            ->where('organization_id', $o->id)->where('access_role', 'leader')->count();
        echo "  - {$o->name} ({$o->type}) [{$o->status}]: {$count} member, {$leaders} leader\n";
    });

/* ─── 5. Duplicate Method Check di Observers ─── */
echo "\n=== [5] DUPLICATE METHOD DI OBSERVERS ===\n";
$observerDir = app_path('Observers');
foreach (glob($observerDir . '/*.php') as $file) {
    $content = file_get_contents($file);
    preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches);
    $methods = $matches[1];
    $dupes = array_filter(array_count_values($methods), fn($c) => $c > 1);
    if (!empty($dupes)) {
        echo "  ❌ " . basename($file) . ": " . json_encode($dupes) . "\n";
    }
}
echo "  ✅ Cek selesai.\n";

echo "\n═══════════════════════════════════════════════════════\n";
echo "  AUDIT SELESAI\n";
echo "═══════════════════════════════════════════════════════\n";