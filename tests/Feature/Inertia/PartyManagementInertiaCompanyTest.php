<?php

use App\Models\AccountPayable;
use App\Models\BusinessType;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
    Storage::fake('public');
    seedCompanyFixtures();
});

function inertiaCompanyUser(): User
{
    return User::query()->firstOrFail();
}

function seedCompanyFixtures(): void
{
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
        'is_customer' => false,
        'name' => 'Fixture Vendor',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        'email' => 'vendor@example.com',
        'address' => 'Vendor Address',
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        'company_address' => 'Fixture Address',
        'company_code' => 'COMP-001',
        'is_active' => 1,
        'addedby' => 1,
        ...$timestamps,
    ]);
}

it('prevents guests from accessing company endpoints', function (): void {
    $this->get(route('inertia.companies.index'))->assertRedirect(route('inertia.login'));
    $this->get(route('inertia.companies.create'))->assertRedirect(route('inertia.login'));
    $this->get(route('inertia.company-balances.index'))->assertRedirect(route('inertia.login'));
});

it('lists companies with logo url props', function (): void {
    $this->actingAs(inertiaCompanyUser())
        ->get(route('inertia.companies.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Companies/Index')
            ->has('companies.data', 1)
            ->where('companies.data.0.company_name', 'Fixture Company')
            ->where('companies.data.0.vendor_name', 'Fixture Vendor')
            ->where('companies.data.0.business_type', 'Supplier')
            ->where('companies.data.0.company_logo_url', null)
        );
});

it('shows company create form', function (): void {
    $this->actingAs(inertiaCompanyUser())
        ->get(route('inertia.companies.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Companies/Create')
            ->has('businessTypes')
        );
});

it('shows company detail with edit-ready props', function (): void {
    $company = PartyCompany::query()->firstOrFail();

    $this->actingAs(inertiaCompanyUser())
        ->get(route('inertia.companies.show', $company))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Companies/Show')
            ->where('company.company_name', 'Fixture Company')
            ->where('company.vendor_name', 'Fixture Vendor')
        );
});

it('can store a company with logo', function (): void {
    $file = UploadedFile::fake()->image('logo.jpg');

    $this->actingAs(inertiaCompanyUser())
        ->post(route('inertia.companies.store'), [
            'name' => 'Test Company',
            'contact_no' => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'business_type_id' => 1,
            'image_file' => $file,
        ])
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertDatabaseHas('party_companies', [
        'company_name' => 'Test Company',
        'company_address' => 'Test Address',
        'business_type_id' => 1,
    ]);
});

it('can update a company including business type', function (): void {
    BusinessType::query()->create([
        'name' => 'Distributor',
        'slug' => 'distributor',
    ]);
    $typeId = BusinessType::query()->where('slug', 'distributor')->value('id');
    $company = PartyCompany::query()->firstOrFail();

    $this->actingAs(inertiaCompanyUser())
        ->put(route('inertia.companies.update', $company), [
            'name' => 'Updated Company',
            'contact_no' => '0987654321',
            'email' => 'updated@example.com',
            'address' => 'Updated Address',
            'business_type_id' => $typeId,
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertDatabaseHas('party_companies', [
        'id' => $company->id,
        'company_name' => 'Updated Company',
        'business_type_id' => $typeId,
    ]);

    $this->assertDatabaseHas('parties', [
        'id' => 1,
        'name' => 'Updated Company',
        'contact_no' => '0987654321',
        'email' => 'updated@example.com',
    ]);
});

it('can toggle company status', function (): void {
    $company = PartyCompany::query()->firstOrFail();

    $this->actingAs(inertiaCompanyUser())
        ->put(route('inertia.companies.toggle-status', $company))
        ->assertRedirect(route('inertia.companies.index'));

    expect((int) $company->refresh()->is_active)->toBe(0);
});

it('can destroy a company', function (): void {
    $company = PartyCompany::query()->firstOrFail();

    $this->actingAs(inertiaCompanyUser())
        ->delete(route('inertia.companies.destroy', $company))
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertSoftDeleted('party_companies', ['id' => $company->id]);
});

it('lists company balances', function (): void {
    CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'status' => 'unpaid',
        'addedby' => 1,
    ]);

    $this->actingAs(inertiaCompanyUser())
        ->get(route('inertia.company-balances.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CompanyBalances/Index')
            ->has('balances.data', 1)
            ->where('balances.data.0.company_name', 'Fixture Company')
        );
});

it('shows company balance detail with payments', function (): void {
    $balance = CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'status' => 'unpaid',
        'addedby' => 1,
    ]);

    $this->actingAs(inertiaCompanyUser())
        ->get(route('inertia.company-balances.show', $balance))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CompanyBalances/Show')
            ->where('balance.company_name', 'Fixture Company')
            ->has('payments', 0)
        );
});

it('stores a company balance payment via cash', function (): void {
    $balance = CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'status' => 'unpaid',
        'addedby' => 1,
    ]);

    $this->actingAs(inertiaCompanyUser())
        ->post(route('inertia.company-balances.store'), [
            'company_balance_id' => $balance->id,
            'party_company_id' => 1,
            'amount_payment' => 400,
            'payment_option' => 'cash',
            'description' => 'Partial cash payment',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('company_balance_payments', [
        'company_balance_id' => $balance->id,
        'party_company_id' => 1,
        'paid_amount' => 400,
        'payment_option' => 'cash',
    ]);

    expect((float) $balance->refresh()->remaining_amount)->toBe(600.0)
        ->and($balance->status)->toBe('pending')
        ->and(CompanyBalancePayment::count())->toBe(1)
        ->and(AccountPayable::where('amount_type', 'company_balance_payment')->count())->toBe(0);
});

it('rejects cheque payments without cheque fields', function (): void {
    $balance = CompanyBalance::query()->create([
        'company_id' => 1,
        'type' => 'product_purchase',
        'total_amount' => 1000,
        'paid_amount' => 0,
        'remaining_amount' => 1000,
        'status' => 'unpaid',
        'addedby' => 1,
    ]);

    $this->actingAs(inertiaCompanyUser())
        ->post(route('inertia.company-balances.store'), [
            'company_balance_id' => $balance->id,
            'party_company_id' => 1,
            'amount_payment' => 100,
            'payment_option' => 'cheque',
        ])
        ->assertSessionHasErrors(['cheque_date', 'bank_name', 'cheque_picture']);

    expect(CompanyBalancePayment::count())->toBe(0);
});

it('forbids company mutations for users without privileges', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inertia.companies.store'), [
            'name' => 'Forbidden Co',
            'contact_no' => '111',
            'email' => 'x@y.com',
            'address' => 'Somewhere',
            'business_type_id' => 1,
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('inertia.company-balances.index'))
        ->assertForbidden();
});
