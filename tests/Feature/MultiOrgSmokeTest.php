<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationContext;
use App\Services\OrganizationLeadershipService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MultiOrgSmokeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // 🧹 Bersihkan context session org di setiap awal test
        OrganizationContext::clear();
    }

    public function test_unicode_owner_can_access_owner_dashboard(): void
    {
        $owner = User::where('level_id', 1)->first();
        $this->assertNotNull($owner, 'Owner user harus ada di database.');
        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertOk();
    }

    public function test_ketua_can_access_ketua_dashboard(): void
    {
        $ketua = User::where('level_id', 3)->first();
        $this->assertNotNull($ketua, 'Ketua user harus ada di database.');
        $response = $this->actingAs($ketua)->get('/ketua/dashboard');
        $response->assertOk();
    }

    public function test_organizations_table_has_unicode(): void
    {
        $unicode = Organization::where('slug', 'ukm-pemrograman-unicode')->first();
        $this->assertNotNull($unicode, 'UNICODE organization harus ada.');
        $this->assertEquals('ukm', $unicode->type);
        $this->assertEquals('active', $unicode->status);
    }

    public function test_same_user_can_be_leader_in_two_orgs(): void
    {
        $user = User::where('level_id', 5)->first();
        $this->assertNotNull($user, 'Anggota user harus ada.');

        $org1 = Organization::where('slug', 'hima-hukum')->first();
        $org2 = Organization::where('slug', 'hima-manajemen')->first();
        $this->assertNotNull($org1, 'HIMA Hukum harus ada.');
        $this->assertNotNull($org2, 'HIMA Manajemen harus ada.');

        // Bersihkan jika ada sisa data
        \DB::table('organization_memberships')->whereIn('organization_id', [$org1->id, $org2->id])->delete();

        OrganizationLeadershipService::promoteToLeader($org1->id, $user->id, 'Ketua HIMA Hukum');
        OrganizationLeadershipService::promoteToLeader($org2->id, $user->id, 'Ketua HIMA Manajemen');

        $this->assertTrue(OrganizationContext::authorize($user->fresh(), $org1->id, 'leader'));
        $this->assertTrue(OrganizationContext::authorize($user->fresh(), $org2->id, 'leader'));

        $leaderCount = $user->fresh()->organizationMemberships()
            ->where('access_role', 'leader')->where('membership_status', 'active')->count();
        $this->assertGreaterThanOrEqual(2, $leaderCount);
    }

    public function test_member_cannot_manage_other_org(): void
    {
        // Ambil user level 5 yang murni belum jadi leader di mana pun
        $user = User::where('level_id', 5)
            ->whereDoesntHave('organizationMemberships', fn ($q) => $q->where('access_role', 'leader'))
            ->first();
            
        $this->assertNotNull($user, 'Butuh user anggota yang bukan leader.');

        $otherOrg = Organization::where('slug', 'bem-universitas')->first();
        $this->assertNotNull($otherOrg);
        
        $this->assertFalse(OrganizationContext::authorize($user, $otherOrg->id, 'leader'));
    }

    public function test_two_ketua_in_same_org_is_rejected(): void
    {
        $org = Organization::where('slug', 'hima-kebidanan')->first();
        $this->assertNotNull($org);

        // Hapus leader lama agar bersih
        \DB::table('organization_memberships')->where('organization_id', $org->id)->delete();

        $u1 = User::where('level_id', 4)->first();
        $u2 = User::where('level_id', 5)->first();
        $this->assertNotEquals($u1->id, $u2->id);

        OrganizationLeadershipService::promoteToLeader($org->id, $u1->id, 'Ketua');
        $this->assertTrue(OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Sudah ada leader aktif/');

        OrganizationLeadershipService::promoteToLeader($org->id, $u2->id, 'Ketua');
    }

    public function test_transfer_leadership_works(): void
    {
        $org = Organization::where('slug', 'hima-ars')->first();
        $this->assertNotNull($org);

        \DB::table('organization_memberships')->where('organization_id', $org->id)->delete();

        $u1 = User::where('level_id', 4)->first();
        $u2 = User::where('level_id', 5)->first();

        OrganizationLeadershipService::promoteToLeader($org->id, $u1->id, 'Ketua');
        $this->assertTrue(OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'));

        OrganizationLeadershipService::transferLeadership($org->id, $u1->id, $u2->id);

        $this->assertFalse(OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'));
        $this->assertTrue(OrganizationContext::authorize($u2->fresh(), $org->id, 'leader'));
    }

    public function test_ketua_cannot_access_owner_routes(): void
    {
        $ketua = User::where('level_id', 3)->first();
        $response = $this->actingAs($ketua)->get('/owner/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_anggota_cannot_access_ketua_routes(): void
    {
        $anggota = User::where('level_id', 5)->first();
        $response = $this->actingAs($anggota)->get('/ketua/management/qr');
        $response->assertStatus(302);
    }

    public function test_ketua_hanya_bisa_melihat_absensi_organisasinya_sendiri(): void
    {
        $orgUnicode = Organization::where('slug', 'ukm-pemrograman-unicode')->first();
        $orgHima    = Organization::where('slug', 'hima-informatika')->first();
        
        $user = User::where('level_id', 3)->first(); // Ketua
        
        // 🧹 Bersihkan HIMA dari ketua lain agar tidak bentrok
        \DB::table('organization_memberships')
            ->where('organization_id', $orgHima->id)
            ->where('access_role', 'leader')
            ->delete();
            
        // 1. Jadikan Ketua di HIMA
        OrganizationLeadershipService::promoteToLeader($orgHima->id, $user->id, 'Ketua HIMA');
        
        // 2. Set session user sedang di UNICODE
        OrganizationContext::set($orgUnicode->id);
        
        // 3. Buat sesi dummy di UNICODE
        $sessionTitle = 'Rapat UNICODE ' . uniqid();
        \App\Models\AttendanceSession::create([
            'organization_id' => $orgUnicode->id,
            'title' => $sessionTitle,
            'created_by' => $user->id,
            'session_status' => 'scheduled',
            'scheduled_start_at' => now(),
            'scheduled_end_at' => now()->addHour(),
            'attendance_open_at' => now(),
            'attendance_close_at' => now()->addHour(),
        ]);

        // 4. Uji query di context UNICODE
        $this->assertEquals(1, \App\Models\AttendanceSession::where('title', $sessionTitle)->count());
        
        // 5. Ganti context ke HIMA
        OrganizationContext::set($orgHima->id);
        
        // 6. Uji query di context HIMA (harusnya 0 karena sesi UNICODE tidak terlihat)
        $this->assertEquals(0, \App\Models\AttendanceSession::where('title', $sessionTitle)->count());
    }
    public function test_isolasi_data_keuangan_antar_organisasi(): void
    {
        $orgUnicode = Organization::where('slug', 'ukm-pemrograman-unicode')->first();
        $orgHima    = Organization::where('slug', 'hima-informatika')->first();
        
        $user = User::where('level_id', 3)->first(); // Ketua
        
        // 1. Set context ke UNICODE
        OrganizationContext::set($orgUnicode->id);
        
        // 2. Buat Invoice dummy di UNICODE lewat factory
        $invoiceTitle = 'Iuran UNICODE ' . uniqid();
        \App\Models\Financial\Invoice::factory()->create([
            'organization_id' => $orgUnicode->id,
            'user_id'         => $user->id,
            'created_by'      => $user->id,
            'title'           => $invoiceTitle,
        ]);

        // 3. Uji: di context UNICODE harus ketemu
        $this->assertEquals(1, \App\Models\Financial\Invoice::where('title', $invoiceTitle)->count());
        
        // 4. Ganti context ke HIMA
        OrganizationContext::set($orgHima->id);
        
        // 5. Uji: di context HIMA harus 0 (TIDAK BOCOR)
        $this->assertEquals(0, \App\Models\Financial\Invoice::where('title', $invoiceTitle)->count());
    }
    public function test_officer_org_a_cannot_update_invoice_org_b(): void
{
    // setup officer di org A, invoice di org B
    $this->assertFalse(Gate::forUser($officer)->allows('update', $invoiceOrgB));
}

}