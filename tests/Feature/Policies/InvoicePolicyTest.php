<?php

namespace Tests\Feature\Policies;

use App\Models\Financial\Invoice;
use App\Models\Organization;
use App\Models\User;
use App\Policies\InvoicePolicy;
use App\Services\OrganizationContext;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoicePolicyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        OrganizationContext::clear();
    }

    public function test_owner_can_view_any_invoice(): void
    {
        $owner = User::where('level_id', 1)->first();
        $this->assertNotNull($owner);

        $this->assertTrue((new InvoicePolicy())->viewAny($owner));
    }

    public function test_owner_can_update_any_invoice_across_orgs(): void
    {
        $owner = User::where('level_id', 1)->first();
        $this->assertNotNull($owner);

        $orgB = Organization::where('slug', 'bem-universitas')->first();
        $this->assertNotNull($orgB);

        OrganizationContext::set($orgB->id);
        $invoice = Invoice::factory()->create([
            'organization_id' => $orgB->id,
            'user_id'         => $owner->id,
        ]);

        $this->assertTrue((new InvoicePolicy())->update($owner, $invoice));
    }

    public function test_officer_org_a_cannot_update_invoice_org_b(): void
    {
        $orgA = Organization::where('slug', 'ukm-pemrograman-unicode')->first();
        $orgB = Organization::where('slug', 'bem-universitas')->first();
        $this->assertNotNull($orgA);
        $this->assertNotNull($orgB);

        $officer = User::where('level_id', 3)->first();
        $this->assertNotNull($officer);

        // Pastikan user hanya officer di A, BUKAN di B
        \DB::table('organization_memberships')
            ->where('user_id', $officer->id)
            ->where('organization_id', $orgA->id)
            ->update(['access_role' => 'officer', 'membership_status' => 'active']);

        \DB::table('organization_memberships')
            ->where('user_id', $officer->id)
            ->where('organization_id', $orgB->id)
            ->delete();

        OrganizationContext::set($orgB->id);
        $invoiceOrgB = Invoice::factory()->create([
            'organization_id' => $orgB->id,
            'user_id'         => $officer->id,
        ]);

        $this->assertFalse(
            (new InvoicePolicy())->update($officer->fresh(), $invoiceOrgB)
        );
    }

    public function test_member_cannot_create_invoice(): void
    {
        $member = User::where('level_id', 5)->first();
        $this->assertNotNull($member);

        $orgB = Organization::where('slug', 'bem-universitas')->first();
        OrganizationContext::set($orgB->id);

        $this->assertFalse((new InvoicePolicy())->create($member->fresh()));
    }
}