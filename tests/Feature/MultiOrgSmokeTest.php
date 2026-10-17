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

        // Pastikan belum leader
        $this->assertFalse(OrganizationContext::authorize($user->fresh(), $org1->id, 'leader'));
        $this->assertFalse(OrganizationContext::authorize($user->fresh(), $org2->id, 'leader'));

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
        $user = User::where('level_id', 5)->first();
        $otherOrg = Organization::where('slug', 'bem-universitas')->first();
        $this->assertNotNull($otherOrg);
        $this->assertFalse(OrganizationContext::authorize($user, $otherOrg->id, 'leader'));
    }

    public function test_two_ketua_in_same_org_is_rejected(): void
    {
        $org = Organization::where('slug', 'hima-kebidanan')->first();
        $this->assertNotNull($org);

        if ($org->memberships()->where('access_role', 'leader')->exists()) {
            $this->markTestSkipped('HIMA Kebidanan sudah punya leader.');
        }

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

        if ($org->memberships()->where('access_role', 'leader')->exists()) {
            $this->markTestSkipped('HIMA ARS sudah punya leader.');
        }

        $u1 = User::where('level_id', 4)->first();
        $u2 = User::where('level_id', 5)->first();

        OrganizationLeadershipService::promoteToLeader($org->id, $u1->id, 'Ketua');
        $this->assertTrue(OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'));

        OrganizationLeadershipService::transferLeadership($org->id, $u1->id, $u2->id);

        $this->assertFalse(OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'));
        $this->assertTrue(OrganizationContext::authorize($u2->fresh(), $org->id, 'leader'));
    }
    public function test_diagnostic_transfer_leadership(): void
{
    $org = Organization::where('slug', 'hima-ars')->first();
    if (!$org) {
        $this->markTestSkipped('HIMA ARS tidak ada.');
    }

    // Cleanup dulu
    \DB::table('organization_memberships')
        ->where('organization_id', $org->id)
        ->delete();

    $u1 = User::where('level_id', 4)->first();
    $u2 = User::where('level_id', 5)->first();

    // Step 1: promote u1
    OrganizationLeadershipService::promoteToLeader($org->id, $u1->id, 'Ketua');

    $membershipU1 = \DB::table('organization_memberships')
        ->where('organization_id', $org->id)
        ->where('user_id', $u1->id)
        ->first();

    dump([
        'step_1_promote_u1' => [
            'u1_id'              => $u1->id,
            'u2_id'              => $u2->id,
            'u1_membership'      => $membershipU1,
            'authorize_u1_leader' => OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'),
        ],
    ]);

    // Step 2: transfer
    OrganizationLeadershipService::transferLeadership($org->id, $u1->id, $u2->id);

    $allMemberships = \DB::table('organization_memberships')
        ->where('organization_id', $org->id)
        ->get();

    dump([
        'step_2_transfer' => [
            'all_memberships' => $allMemberships,
            'u1_after'        => \DB::table('organization_memberships')
                ->where('organization_id', $org->id)
                ->where('user_id', $u1->id)
                ->first(),
            'u2_after'        => \DB::table('organization_memberships')
                ->where('organization_id', $org->id)
                ->where('user_id', $u2->id)
                ->first(),
            'authorize_u1'    => OrganizationContext::authorize($u1->fresh(), $org->id, 'leader'),
            'authorize_u2'    => OrganizationContext::authorize($u2->fresh(), $org->id, 'leader'),
        ],
    ]);
}
}