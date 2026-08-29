<?php

use App\Helpers\Constant;
use App\Models\AccountPayable;
use App\Models\BrokerBalance;
use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
});

function remainingMigrationUser(): User
{
    return User::query()->firstOrFail();
}

it('redirects guests from remaining inertia balance and payable pages', function (): void {
    $this->get(route('inertia.payables.index'))->assertRedirect();
    $this->get(route('inertia.broker-balances.index'))->assertRedirect();
    $this->get(route('inertia.party-balances.index'))->assertRedirect();
});

it('lists account payables through inertia', function (): void {
    AccountPayable::query()->create([
        'entry_date' => '2026-06-01',
        'total_amount' => 1000,
        'paid_amount' => 200,
        'remaining_amount' => 800,
        'amount_status' => 'unpaid',
        'amount_type' => 'debit',
        'narration' => 'Fixture payable',
        'addedby' => 1,
    ]);

    $this->actingAs(remainingMigrationUser())
        ->get(route('inertia.payables.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccountPayables/Index')
            ->has('payables.data', 1)
            ->where('payables.data.0.amount_status', 'Unpaid')
            ->where('payables.data.0.total_amount', 1000)
        );
});

it('lists broker balances through inertia', function (): void {
    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    DB::table('brokers')->insert([
        'id' => 1,
        'broker_code' => 'BRK-001',
        'name' => 'Fixture Broker',
        'is_active' => true,
        ...$timestamps,
    ]);

    BrokerBalance::query()->create([
        'broker_id' => 1,
        'total_amount' => 5000,
        'paid_amount' => 0,
        'remaining_amount' => 5000,
        'status' => 'unpaid',
        'narration' => 'chicken sale commission',
        'addedby' => 1,
    ]);

    $this->actingAs(remainingMigrationUser())
        ->get(route('inertia.broker-balances.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BrokerBalances/Index')
            ->has('balances.data', 1)
            ->where('balances.data.0.broker_name', 'Fixture Broker')
            ->where('balances.data.0.status', 'Unpaid')
        );
});

it('shows party balance payments and records a payment through inertia', function (): void {
    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    DB::table('parties')->insert([
        'id' => 1,
        'is_customer' => true,
        'is_vendor' => false,
        'is_active' => true,
        'name' => 'Fixture Party',
        'cnic_no' => '1000000000001',
        'contact_no' => '03000000001',
        ...$timestamps,
    ]);

    $balance = PartyBalance::query()->create([
        'party_id' => 1,
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'transaction_date' => '2026-06-01',
        'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
        'payment_status' => Constant::PAYMENT_STATUS['UnPaid'],
        'narration' => 'Fixture party balance',
        'addedby' => 1,
    ]);

    $this->actingAs(remainingMigrationUser())
        ->get(route('inertia.party-balances.show', $balance))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PartyBalances/Show')
            ->where('balance.party_name', 'Fixture Party')
            ->has('payments', 0)
            ->has('today')
        );

    $this->actingAs(remainingMigrationUser())
        ->post(route('inertia.party-balances.store'), [
            'balance_id' => $balance->id,
            'party_id' => 1,
            'amount_payment' => 250,
            'paid_date' => '2026-06-15',
            'payment_option' => 'cash',
            'description' => 'Partial payment',
        ])
        ->assertRedirect();

    expect((float) $balance->refresh()->paid_amount)->toBe(250.0)
        ->and((float) $balance->remaining_amount)->toBe(750.0)
        ->and(PartyBalancePayment::count())->toBe(1);
});

it('registers remaining inertia routes', function (): void {
    expect(Route::has('inertia.payables.index'))->toBeTrue()
        ->and(Route::has('inertia.broker-balances.index'))->toBeTrue()
        ->and(Route::has('inertia.party-balances.store'))->toBeTrue()
        ->and(Route::has('inertia.product-sales.invoice'))->toBeTrue()
        ->and(Route::has('inertia.product-purchases.invoice'))->toBeTrue();
});
