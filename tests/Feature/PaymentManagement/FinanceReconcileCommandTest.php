<?php

use App\Models\CompanyBalance;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Artisan;
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
});

test('finance reconcile exits successfully when balances are clean', function () {
    CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'model_id' => 1,
        'reference_type' => 'product_purchase',
        'reference_id' => 1,
        'total_amount' => 100,
        'paid_amount' => 40,
        'remaining_amount' => 60,
        'status' => 'pending',
        'addedby' => 1,
    ]);

    $exit = Artisan::call('finance:reconcile', ['--report' => true]);

    expect($exit)->toBe(0);
});

test('finance reconcile fails when balance equation drifts', function () {
    CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'model_id' => 2,
        'reference_type' => 'product_purchase',
        'reference_id' => 2,
        'total_amount' => 100,
        'paid_amount' => 40,
        'remaining_amount' => 50,
        'status' => 'pending',
        'addedby' => 1,
    ]);

    $exit = Artisan::call('finance:reconcile', ['--report' => true]);

    expect($exit)->toBe(1);
});
