<?php

use App\Actions\PartyManagement\RecordCompanyBalancePaymentAction;
use App\Helpers\Constant;
use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\FinancialTransaction;
use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Models\PaymentAllocation;
use App\Models\User;
use App\Actions\BalanceManagement\RecordPartyBalancePaymentAction;
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
        'reference_type' => 'product_purchase',
        'reference_id' => 99,
        'total_amount' => 10000,
        'paid_amount' => 0,
        'remaining_amount' => 10000,
        'dr' => 10000,
        'addedby' => 1,
        ...$timestamps,
    ]);
    DB::table('party_balances')->insert([
        'id' => 1,
        'party_id' => 1,
        'reference_type' => 'product_sale',
        'reference_id' => 55,
        'total_amount' => 5000,
        'paid_amount' => 0,
        'remaining_amount' => 5000,
        'transaction_date' => '2026-06-01',
        'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
        'payment_status' => Constant::PAYMENT_STATUS['UnPaid'],
        'narration' => 'product sale balance (ProductSale #55)',
        'addedby' => 1,
        ...$timestamps,
    ]);
});

test('company payment allocates against original payable and posts a financial transaction', function () {
    $idempotencyKey = (string) Str::uuid();

    $this->post(route('companybalance.store'), [
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 2500,
        'payment_option' => 'cash',
        'description' => 'Partial payment',
        'idempotency_key' => $idempotencyKey,
    ])->assertOk()->assertJson(['success' => 'yes']);

    $balance = CompanyBalance::findOrFail(1);
    expect((float) $balance->paid_amount)->toBe(2500.0)
        ->and((float) $balance->remaining_amount)->toBe(7500.0)
        ->and($balance->status)->toBe('pending');

    expect(CompanyBalancePayment::count())->toBe(1)
        ->and(PaymentAllocation::count())->toBe(1)
        ->and(FinancialTransaction::where('transaction_type', 'company_balance_payment')->count())->toBe(1)
        ->and(AccountPayable::where('amount_type', 'company_balance_payment')->count())->toBe(0);

    $payable = AccountPayable::findOrFail(1);
    expect((float) $payable->paid_amount)->toBe(2500.0)
        ->and((float) $payable->remaining_amount)->toBe(7500.0)
        ->and($payable->amount_status)->toBe('pending');
});

test('duplicate company payment idempotency key does not double allocate', function () {
    $idempotencyKey = (string) Str::uuid();
    $payload = [
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 1000,
        'payment_option' => 'cash',
        'idempotency_key' => $idempotencyKey,
    ];

    $this->post(route('companybalance.store'), $payload)->assertOk();
    $this->post(route('companybalance.store'), $payload)->assertOk();

    expect(CompanyBalancePayment::count())->toBe(1)
        ->and(PaymentAllocation::count())->toBe(1)
        ->and((float) CompanyBalance::findOrFail(1)->paid_amount)->toBe(1000.0);
});

test('company payment rejects overpayment', function () {
    $this->expectException(ValidationException::class);

    app(RecordCompanyBalancePaymentAction::class)->execute([
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 15000,
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1);
});

test('party payment allocates and posts a financial transaction', function () {
    $this->post(route('partybalance.store'), [
        'balance_id' => 1,
        'party_id' => 1,
        'amount_payment' => 1500,
        'paid_date' => '2026-06-02',
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ])->assertOk()->assertJson(['success' => 'yes']);

    expect(PartyBalancePayment::count())->toBe(1)
        ->and(PaymentAllocation::count())->toBe(1)
        ->and(FinancialTransaction::where('transaction_type', 'party_balance_payment')->count())->toBe(1)
        ->and((float) PartyBalance::findOrFail(1)->remaining_amount)->toBe(3500.0);
});

test('party payment rejects overpayment', function () {
    $this->expectException(ValidationException::class);

    app(RecordPartyBalancePaymentAction::class)->execute([
        'balance_id' => 1,
        'party_id' => 1,
        'amount_payment' => 9000,
        'paid_date' => '2026-06-02',
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1);
});
