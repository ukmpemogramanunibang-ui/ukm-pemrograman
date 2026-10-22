<?php

namespace Database\Factories;

use App\Models\Financial\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = $this->faker->numberBetween(10_000, 500_000);

        return [
            'user_id'           => User::factory(),
            'created_by'        => null,
            'invoice_number'    => 'INV-' . strtoupper(Str::random(10)),
            'title'             => 'Iuran ' . $this->faker->words(3, true),
            'amount'            => $amount,
            'type'              => 'manual',
            'period'            => now()->format('Y-m'),
            'standard_due_date' => now()->addDays(7),
            'final_due_date'    => now()->addDays(7),
            'due_date'          => now()->addDays(7),
            'subtotal'          => $amount,
            'penalty'           => 0,
            'discount'          => 0,
            'total'             => $amount,
            'status'            => 'pending',
            'meta'              => null,
            'organization_id'   => null,   // auto-set dari OrganizationContext::id()
        ];
    }
}