<?php

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
        'id' => 1,
        'name' => 'Supplier',
        'slug' => 'supplier',
        ...$timestamps,
    ]);
    DB::table('parties')->insert([
        'id' => 1,
        'is_vendor' => true,
        'is_customer' => true,
        'name' => 'Fixture Party',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        'company_address' => 'Fixture Address',
        ...$timestamps,
    ]);
    DB::table('product_categories')->insert([
        'id' => 1,
        'name' => 'Fixture Product Category',
        'slug' => 'fixture-product-category',
        'company_id' => 1,
        ...$timestamps,
    ]);
    DB::table('party_balances')->insert([
        'id' => 1,
        'party_id' => 1,
        'transaction_date' => '2026-06-01',
        'total_amount' => 100,
        'paid_amount' => 0,
        'remaining_amount' => 100,
        'narration' => 'Fixture balance',
        ...$timestamps,
    ]);
    DB::table('product_purchases')->insert([
        'id' => 1,
        'purchase_code' => 'PRODUCT-PURCHASE-1',
        'product_category_id' => 1,
        'party_company_id' => 1,
        'purchase_date' => '2026-06-01',
        'total_amount' => 100,
        'discount_amount' => 5,
        'final_amount' => 95,
        ...$timestamps,
    ]);
});

it('renders the party balance index with a formatted transaction date', function () {
    $this->get(route('partybalance.index'))
        ->assertOk()
        ->assertSee('01 Jun, 2026', false);
});

it('renders the product purchase index with a formatted purchase date', function () {
    $this->get(route('productpurchases.index'))
        ->assertOk()
        ->assertSee('01 Jun, 2026', false);
});
