<?php

use App\Models\CompanyBalance;
use App\Models\PartyCompany;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('prevents guests from accessing company endpoints', function (): void {
    $this->get(route('inertia.companies.index'))->assertRedirect(route('inertia.login'));
    $this->get(route('inertia.companies.create'))->assertRedirect(route('inertia.login'));
    $this->get(route('inertia.company-balances.index'))->assertRedirect(route('inertia.login'));
});

it('lists companies', function (): void {
    PartyCompany::insert([
        ['company_name' => 'Company A', 'addedby' => 1],
        ['company_name' => 'Company B', 'addedby' => 1],
        ['company_name' => 'Company C', 'addedby' => 1],
    ]);

    $this->actingAs($this->user)
        ->get(route('inertia.companies.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Companies/Index')
            ->has('companies.data', 3)
        );
});

it('shows company create form', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.companies.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Companies/Create')
            ->has('businessTypes')
        );
});

it('can store a company with logo', function (): void {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.jpg');

    $this->actingAs($this->user)
        ->post(route('inertia.companies.store'), [
            'name'       => 'Test Company',
            'contact_no' => '1234567890',
            'email'      => 'test@example.com',
            'address'    => 'Test Address',
            'image_file' => $file,
        ])
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertDatabaseHas('party_companies', [
        'company_name' => 'Test Company',
        'company_address' => 'Test Address',
    ]);
});

it('can update a company', function (): void {
    $company = PartyCompany::create(['company_name' => 'Old Name', 'addedby' => 1]);

    $this->actingAs($this->user)
        ->put(route('inertia.companies.update', $company), [
            'name'       => 'Updated Company',
            'contact_no' => '0987654321',
            'email'      => 'updated@example.com',
            'address'    => 'Updated Address',
        ])
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertDatabaseHas('party_companies', [
        'id'           => $company->id,
        'company_name' => 'Updated Company',
    ]);
});

it('can toggle company status', function (): void {
    $company = PartyCompany::create(['company_name' => 'Old Name', 'is_active' => 1, 'addedby' => 1]);

    $this->actingAs($this->user)
        ->put(route('inertia.companies.toggle-status', $company))
        ->assertRedirect(route('inertia.companies.index'));

    $this->assertDatabaseHas('party_companies', [
        'id'        => $company->id,
        'is_active' => 0,
    ]);
});

it('lists company balances', function (): void {
    CompanyBalance::insert([
        ['company_id' => 1, 'type' => 'opening_balance', 'total_amount' => 100, 'remaining_amount' => 100, 'status' => 'Unpaid', 'addedby' => 1],
        ['company_id' => 1, 'type' => 'opening_balance', 'total_amount' => 100, 'remaining_amount' => 100, 'status' => 'Unpaid', 'addedby' => 1],
        ['company_id' => 1, 'type' => 'opening_balance', 'total_amount' => 100, 'remaining_amount' => 100, 'status' => 'Unpaid', 'addedby' => 1],
    ]);

    $this->actingAs($this->user)
        ->get(route('inertia.company-balances.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CompanyBalances/Index')
            ->has('balances.data', 3)
        );
});

it('shows company balance detail with payments', function (): void {
    $balance = CompanyBalance::create(['company_id' => 1, 'type' => 'opening_balance', 'total_amount' => 100, 'remaining_amount' => 100, 'status' => 'Unpaid', 'addedby' => 1]);

    $this->actingAs($this->user)
        ->get(route('inertia.company-balances.show', $balance))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CompanyBalances/Show')
            ->has('balance')
            ->has('payments')
        );
});

it('stores a company balance payment via cash', function (): void {
    $company = PartyCompany::create(['company_name' => 'Test', 'addedby' => 1]);
    
    $balance = CompanyBalance::create([
        'company_id' => $company->id,
        'type' => 'opening_balance',
        'total_amount' => 1000,
        'remaining_amount' => 1000,
        'status' => 'Unpaid',
        'addedby' => 1
    ]);

    $this->actingAs($this->user)
        ->post(route('inertia.company-balances.store'), [
            'company_balance_id' => $balance->id,
            'company_id'         => $balance->company_id,
            'amount_payment'     => 400,
            'payment_option'     => 1, // Cash
        ])
        ->assertRedirect(); // back

    $this->assertDatabaseHas('company_balance_payments', [
        'company_balance_id' => $balance->id,
        'paid_amount'        => 400.00,
        'payment_option'     => 1,
    ]);

    $this->assertDatabaseHas('company_balances', [
        'id'               => $balance->id,
        'remaining_amount' => 600.00,
        'status'           => 'Partially Paid',
    ]);
});
