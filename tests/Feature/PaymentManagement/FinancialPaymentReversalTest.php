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

test('company payment reversal voids allocation and restores payable balances', function () {
    $payment = app(RecordCompanyBalancePaymentAction::class)->execute([
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 400,
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1);

    $this->post(route('companybalance.payments.reverse', $payment), [
        'reversal_reason' => 'Entered twice',
    ])->assertOk()->assertJson(['success' => 'yes']);

    expect($payment->fresh()->payment_status)->toBe('reversed')
        ->and(PaymentAllocation::where('status', PaymentAllocation::STATUS_VOIDED)->count())->toBe(1)
        ->and(FinancialTransaction::where('status', FinancialTransaction::STATUS_REVERSED)->count())->toBe(1)
        ->and((float) CompanyBalance::findOrFail(1)->remaining_amount)->toBe(1000.0)
        ->and((float) AccountPayable::findOrFail(1)->remaining_amount)->toBe(1000.0);
});

test('company payment cannot be reversed twice', function () {
    $payment = app(RecordCompanyBalancePaymentAction::class)->execute([
        'company_balance_id' => 1,
        'party_company_id' => 1,
        'amount_payment' => 200,
        'payment_option' => 'cash',
        'idempotency_key' => (string) Str::uuid(),
    ], null, null, 1);

    app(ReverseCompanyBalancePaymentAction::class)->execute($payment, 1, 'first');

    expect(fn () => app(ReverseCompanyBalancePaymentAction::class)->execute($payment->fresh(), 1, 'second'))
        ->toThrow(ValidationException::class);
});
