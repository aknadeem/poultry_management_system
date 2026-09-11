<?php

use App\Actions\PartyManagement\RecordCompanyBalancePaymentAction;
use App\Actions\PartyManagement\ReverseCompanyBalancePaymentAction;
use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\FinancialTransaction;
use App\Models\PaymentAllocation;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
        'model_id' => 99,
        'reference_type' => 'product_purchase',
        'reference_id' => 99,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
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
        'reference_type' => 'product_purchase',
        'reference_id' => 99,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'dr' => 1000,
        'addedby' => 1,
        ...$timestamps,
    ]);
});

test('sequential allocations cannot exceed remaining balance', function () {
    $action = app(RecordCompanyBalancePaymentAction::class);

    $action->execute([
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 700,
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1);

    expect(fn () => $action->execute([
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 400,
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1))->toThrow(ValidationException::class);

    expect(PaymentAllocation::where('status', PaymentAllocation::STATUS_POSTED)->count())->toBe(1)
        ->and((float) CompanyBalance::findOrFail(1)->remaining_amount)->toBe(300.0);
})->group('financial-concurrency');

test('true mysql concurrent over-allocation is opt-in and skipped by default', function () {
    if (! env('FINANCIAL_CONCURRENCY_MYSQL', false)) {
        $this->markTestSkipped('Set FINANCIAL_CONCURRENCY_MYSQL=true and a *_testing MySQL database to run this suite.');
    }

    expect(config('database.default'))->toBe('mysql');
    expect(str_ends_with((string) config('database.connections.mysql.database'), '_testing'))->toBeTrue();
})->group('financial-concurrency');
