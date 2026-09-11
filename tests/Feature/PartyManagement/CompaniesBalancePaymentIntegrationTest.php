<?php

use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());

    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    DB::table('business_types')->insert([
        'id' => 1, 'name' => 'Supplier', 'slug' => 'supplier', ...$timestamps,
    ]);
    DB::table('parties')->insert([
        'id' => 1,
        'is_vendor' => true,
        'name' => 'Fixture Vendor',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        ...$timestamps,
    ]);
    DB::table('company_balances')->insert([
        'id' => 1,
        'type' => 'product_purchase',
        'total_amount' => 10000,
        'paid_amount' => 0,
        'remaining_amount' => 10000,
        'status' => 'unpaid',
        'company_id' => 1,
        'addedby' => 1,
        ...$timestamps,
    ]);
    DB::table('account_payables')->insert([
        'id' => 1,
        'amount_type' => 'product_purchase',
        'amount_status' => 'unpaid',
        'narration' => 'company balance on product purchase',
        'entry_date' => '2026-06-01',
        'model_id' => 1,
        'company_balance_id' => 1,
        'total_amount' => 10000,
        'paid_amount' => 0,
        'remaining_amount' => 10000,
        'dr' => 10000,
        'addedby' => 1,
        ...$timestamps,
    ]);
});

test('company balance payment reduces remaining balance and syncs original payable', function () {
    $this->withoutExceptionHandling();

    $this->post(route('companybalance.store'), [
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 2500,
        'payment_option' => 'cash',
        'description' => 'Partial payment',
    ])
        ->assertOk()
        ->assertJson([
            'success' => 'yes',
            'message' => 'Payment added successfully!',
        ]);

    $balance = CompanyBalance::findOrFail(1);
    expect((float) $balance->paid_amount)->toBe(2500.0);
    expect((float) $balance->remaining_amount)->toBe(7500.0);
    expect($balance->status)->toBe('pending');

    expect(CompanyBalancePayment::count())->toBe(1);
    expect(AccountPayable::where('amount_type', 'company_balance_payment')->count())->toBe(0);
    expect((float) AccountPayable::findOrFail(1)->paid_amount)->toBe(2500.0);
    expect((float) AccountPayable::findOrFail(1)->remaining_amount)->toBe(7500.0);
});

test('company balance payments show page handles empty payments successfully', function () {
    $this->withoutExceptionHandling();

    $this->get(route('companybalance.show', 1))
        ->assertOk()
        ->assertViewHas('balance_payments')
        ->assertViewHas('company_balance')
        ->assertSee('Fixture Company')
        ->assertSee('Balance Payments');
});

