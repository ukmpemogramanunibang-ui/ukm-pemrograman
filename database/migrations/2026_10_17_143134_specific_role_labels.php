<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /* ═══════════════════════════════════════════════════════
         | 1. Update levels.display_name — jadi netral
         |    (karena label spesifik akan ambil dari position_label)
         * ═══════════════════════════════════════════════════════ */
        DB::table('levels')->where('id', 3)->update(['display_name' => 'Ketua']);
        DB::table('levels')->where('id', 4)->update(['display_name' => 'Pengurus']);
        DB::table('levels')->where('id', 5)->update(['display_name' => 'Anggota']);
        // Level 6, 7, 8 tidak diubah (sudah sesuai)

        /* ═══════════════════════════════════════════════════════
         | 2. Populate position_label UNTUK KETUA EXISTING
         |    Ambil dari organisasi UNICODE (default org)
         * ═══════════════════════════════════════════════════════ */
        $unicode = DB::table('organizations')
            ->where('slug', 'ukm-pemrograman-unicode')
            ->first();

        if ($unicode) {
            // Ketua UNICODE
            DB::table('organization_memberships')
                ->where('organization_id', $unicode->id)
                ->where('access_role', 'leader')
                ->whereNull('position_label')
                ->update(['position_label' => 'Ketua UKM Pemrograman UNICODE']);

            // Pengurus UNICODE
            DB::table('organization_memberships')
                ->where('organization_id', $unicode->id)
                ->where('access_role', 'officer')
                ->whereNull('position_label')
                ->update(['position_label' => 'Pengurus UKM Pemrograman UNICODE']);

            // PJ UNICODE
            DB::table('organization_memberships')
                ->where('organization_id', $unicode->id)
                ->where('access_role', 'pj')
                ->whereNull('position_label')
                ->update(['position_label' => 'PJ UKM Pemrograman UNICODE']);
        }

        /* ═══════════════════════════════════════════════════════
         | 3. Backfill untuk HIMA/BEM jika sudah ada membership
         * ═══════════════════════════════════════════════════════ */
        $orgPrefixMap = [
            'bem-universitas'        => 'BEM Universitas Bhakti Asih',
            'hima-informatika'       => 'HIMA Informatika',
            'hima-keperawatan'       => 'HIMA Keperawatan',
            'hima-kebidanan'         => 'HIMA Kebidanan',
            'hima-manajemen'         => 'HIMA Manajemen',
            'hima-hukum'             => 'HIMA Hukum',
            'hima-ars'               => 'HIMA ARS',
        ];

        foreach ($orgPrefixMap as $slug => $orgName) {
            $org = DB::table('organizations')->where('slug', $slug)->first();
            if (!$org) continue;

            // Leader
            DB::table('organization_memberships')
                ->where('organization_id', $org->id)
                ->where('access_role', 'leader')
                ->whereNull('position_label')
                ->update(['position_label' => "Ketua {$orgName}"]);

            // Officer
            DB::table('organization_memberships')
                ->where('organization_id', $org->id)
                ->where('access_role', 'officer')
                ->whereNull('position_label')
                ->update(['position_label' => "Pengurus {$orgName}"]);
        }
    }

    public function down(): void
    {
        // Rollback levels.display_name
        DB::table('levels')->where('id', 3)->update(['display_name' => 'Ketua UKM']);
        DB::table('levels')->where('id', 4)->update(['display_name' => 'Pengurus Inti']);
        DB::table('levels')->where('id', 5)->update(['display_name' => 'Anggota UKM']);

        // Hapus position_label yang kita buat
        DB::table('organization_memberships')
            ->whereIn('access_role', ['leader', 'officer', 'pj'])
            ->whereNotNull('position_label')
            ->update(['position_label' => null]);
    }
};