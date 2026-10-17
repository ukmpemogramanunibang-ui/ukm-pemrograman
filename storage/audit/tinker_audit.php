<?php
// storage/audit/tinker_audit.php

echo "=== [1] LEVELS ===\n";
DB::table('levels')->select('id','name','display_name')->orderBy('id')->get()
    ->each(fn($l) => print("  ID={$l->id}  {$l->name} ({$l->display_name})\n"));

echo "\n=== [2] DISTRIBUSI level_id di users ===\n";
DB::table('users')->select('level_id', DB::raw('COUNT(*) as total'))
    ->groupBy('level_id')->orderBy('level_id')->get()
    ->each(fn($r) => print("  level_id=" . ($r->level_id ?? 'NULL') . ": {$r->total}\n"));

echo "\n=== [3] User dengan user_uuid kosong / NULL ===\n";
echo "  NULL: " . DB::table('users')->whereNull('user_uuid')->count() . "\n";
echo "  Empty string: " . DB::table('users')->where('user_uuid', '')->count() . "\n";

echo "\n=== [4] Kolom kritis pada users ===\n";
$critical = ['user_uuid','semester','portfolio_url','social_media','skills','portfolio_links','generation_id','pin'];
foreach ($critical as $c) {
    echo "  " . str_pad($c, 20) . ": " . (Schema::hasColumn('users', $c) ? 'ADA' : '❌ TIDAK ADA') . "\n";
}

echo "\n=== [5] Tabel terkait jabatan/organisasi/divisi ===\n";
foreach (['jabatans','jabatan_user','organizational_periods','positions','user_positions','divisions','division_members','groups','group_members','kelompok'] as $t) {
    echo "  " . str_pad($t, 25) . ": " . (Schema::hasTable($t) ? 'ADA (' . DB::table($t)->count() . ' rows)' : '❌ TIDAK ADA') . "\n";
}

echo "\n=== [6] Struktur jabatan_user ===\n";
if (Schema::hasTable('jabatan_user')) {
    print_r(Schema::getColumnListing('jabatan_user'));
}

echo "\n=== [7] Struktur organizational_periods ===\n";
if (Schema::hasTable('organizational_periods')) {
    print_r(Schema::getColumnListing('organizational_periods'));
    DB::table('organizational_periods')->limit(5)->get()
        ->each(fn($p) => print("  ID={$p->id} name={$p->name} start={$p->start_date} end={$p->end_date}\n"));
}

echo "\n=== [8] Struktur divisions & division_members ===\n";
if (Schema::hasTable('divisions')) {
    print_r(Schema::getColumnListing('divisions'));
    echo "  Total divisions: " . DB::table('divisions')->count() . "\n";
}
if (Schema::hasTable('division_members')) {
    print_r(Schema::getColumnListing('division_members'));
    echo "  Total division_members: " . DB::table('division_members')->count() . "\n";
}

echo "\n=== [9] Spatie Permission: roles & permissions ===\n";
if (Schema::hasTable('roles')) {
    print_r(DB::table('roles')->get(['id','name','guard_name'])->toArray());
    echo "  Team feature active? cek config/permission.php `teams` => ";
    echo config('permission.teams') ? 'YA' : 'TIDAK';
    echo "\n";
}

echo "\n=== [10] Cek user yang jadi 'ketua' (level_id=3) ===\n";
DB::table('users')->where('level_id', 3)->where('is_active', true)->whereNull('deleted_at')
    ->select('id','name','nim','ukm_id')->get()
    ->each(fn($u) => print("  {$u->id} - {$u->name} ({$u->nim}) ukm_id={$u->ukm_id}\n"));

echo "\n=== SELESAI ===\n";