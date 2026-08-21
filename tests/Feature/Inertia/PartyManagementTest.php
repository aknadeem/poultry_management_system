<?php

use App\Helpers\Constant;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\City;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Party;
use App\Models\PartyAccount;
use App\Models\PartyBalance;
use App\Models\PartyBalanceLimit;
use App\Models\PartyCompany;
use App\Models\PartyDocument;
use App\Models\PartyFarm;
use App\Models\Province;
use App\Models\User;
use App\Models\UserRole;
use App\Models\VendorType;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
});

function inertiaPartyAdmin(): User
{
    return User::query()->where('email', 'admin@admin.com')->firstOrFail();
}

function seedInertiaPartyLookups(): array
{
    $countryId = Country::query()->insertGetId([
        'name' => 'Pakistan',
        'slug' => 'pakistan',
    ]);

    $provinceId = Province::query()->insertGetId([
        'name' => 'Punjab',
        'country_id' => $countryId,
    ]);

    $cityId = City::query()->insertGetId([
        'name' => 'Lahore',
        'province_id' => $provinceId,
    ]);

    $customerTypeId = CustomerType::query()->insertGetId([
        'name' => 'Retail',
        'slug' => 'retail',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $farmTypeId = FarmType::query()->insertGetId([
        'name' => 'Layer',
        'slug' => 'layer',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $farmSubtypeId = FarmSubtype::query()->insertGetId([
        'name' => 'Commercial',
        'slug' => 'commercial',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $vendorTypeId = VendorType::query()->insertGetId([
        'name' => 'Feed Supplier',
        'slug' => 'feed-supplier',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $businessTypeId = BusinessType::query()->insertGetId([
        'name' => 'Private Limited',
        'slug' => 'private-limited',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $divisionId = Division::query()->insertGetId([
        'name' => 'North',
        'slug' => 'north',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return compact(
        'countryId',
        'provinceId',
        'cityId',
        'customerTypeId',
        'farmTypeId',
        'farmSubtypeId',
        'vendorTypeId',
        'businessTypeId',
        'divisionId',
    );
}

function createInertiaParty(array $lookups, array $overrides = []): Party
{
    return Party::query()->create(array_merge([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Listed Party',
        'guardian_name' => 'Guardian',
        'cnic_no' => '3520212345671',
        'email' => 'listed-party@example.com',
        'contact_no' => '03001234571',
        'manual_number' => 'MN-101',
        'country_id' => $lookups['countryId'],
        'province_id' => $lookups['provinceId'],
        'city_id' => $lookups['cityId'],
        'customer_type_id' => $lookups['customerTypeId'],
        'cnic_front' => 'front.jpg',
        'cnic_back' => 'back.jpg',
    ], $overrides));
}

it('lists parties in an inertia datatable for a super admin', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    createInertiaParty($lookups);

    $this->actingAs($admin)
        ->get(route('inertia.parties.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Parties/Index')
            ->has('parties.data')
            ->has('parties.links')
            ->has('filters')
            ->where('parties.data.0.name', 'Listed Party')
            ->has('parties.data.0.accounts')
            ->has('parties.data.0.documents')
            ->has('parties.data.0.balance_limits')
            ->where('can.parties.viewAny', true)
            ->where('can.parties.create', true)
            ->has('routes')
        );
});

it('searches parties through the query allow-list', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    createInertiaParty($lookups, [
        'name' => 'Alpha Party',
        'cnic_no' => '3520212345672',
        'contact_no' => '03001234572',
        'email' => 'alpha-party@example.com',
        'manual_number' => 'MN-102',
    ]);
    createInertiaParty($lookups, [
        'name' => 'Beta Party',
        'cnic_no' => '3520212345673',
        'contact_no' => '03001234573',
        'email' => 'beta-party@example.com',
        'manual_number' => 'MN-103',
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.parties.index', ['search' => 'Alpha']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Parties/Index')
            ->where('parties.data.0.name', 'Alpha Party')
            ->where('parties.total', 1)
        );
});

it('ignores unknown sort columns on the parties list', function () {
    $admin = inertiaPartyAdmin();

    $this->actingAs($admin)
        ->get(route('inertia.parties.index', ['sort' => 'drop table parties', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Parties/Index')
            ->where('filters.sort', 'id')
        );
});

it('allows a hod to view parties but not create them', function () {
    $hodRole = UserRole::query()->where('slug', 'hod')->firstOrFail();
    $hod = User::factory()->create(['user_role_id' => $hodRole->id]);

    $this->actingAs($hod)
        ->get(route('inertia.parties.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('can.parties.viewAny', true)
            ->where('can.parties.create', false)
        );

    $this->actingAs($hod)
        ->get(route('inertia.parties.create'))
        ->assertForbidden();
});

it('creates a vendor party through inertia using the existing action', function () {
    Storage::fake('public');
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->post(route('inertia.parties.store'), [
            'is_vendor' => '1',
            'name' => 'Inertia Vendor',
            'guardian_name' => 'Guardian Name',
            'cnic_no' => '3520212345680',
            'email' => 'inertia-vendor@example.com',
            'contact_no' => '03001234580',
            'manual_number' => 'MN-206',
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'address' => 'Vendor address',
            'vendor_division_id' => $lookups['divisionId'],
            'vendor_type_id' => $lookups['vendorTypeId'],
            'company_name' => 'Inertia Vendor Company',
            'business_type_id' => $lookups['businessTypeId'],
            'company_address' => 'Company address',
            'cnic_front' => UploadedFile::fake()->image('cnic-front.jpg'),
            'cnic_back' => UploadedFile::fake()->image('cnic-back.jpg'),
            'company_logo' => UploadedFile::fake()->image('company-logo.jpg'),
            'customer_type_id' => '',
            'farm_type_id' => '',
            'farm_subtype_id' => '',
            'farm_name' => '',
            'farm_noc' => '',
            'farm_address' => '',
            'farm_image' => '',
        ])
        ->assertRedirect(route('inertia.parties.index'));

    $party = Party::query()->where('cnic_no', '3520212345680')->first();

    expect($party)->not->toBeNull()
        ->and((int) $party->is_vendor)->toBe(1)
        ->and($party->vendor_division_id)->toBe($lookups['divisionId']);

    expect(PartyCompany::query()->where('party_id', $party->id)->exists())->toBeTrue();
});

it('creates a customer through the inertia customers resource', function () {
    Storage::fake('public');
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->post(route('inertia.customers.store'), [
            'is_customer' => '1',
            'name' => 'Inertia Customer',
            'guardian_name' => 'Guardian Name',
            'cnic_no' => '3520212345681',
            'email' => 'inertia-customer@example.com',
            'contact_no' => '03001234581',
            'manual_number' => 'MN-207',
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'customer_type_id' => $lookups['customerTypeId'],
            'farm_type_id' => $lookups['farmTypeId'],
            'farm_subtype_id' => $lookups['farmSubtypeId'],
            'farm_name' => 'Inertia Farm',
            'farm_noc' => 'NOC-1',
            'farm_address' => 'Farm address',
            'cnic_front' => UploadedFile::fake()->image('cnic-front.jpg'),
            'cnic_back' => UploadedFile::fake()->image('cnic-back.jpg'),
            'farm_image' => UploadedFile::fake()->image('farm.jpg'),
            'vendor_division_id' => '',
            'vendor_type_id' => '',
            'company_name' => '',
            'business_type_id' => '',
            'company_address' => '',
            'company_logo' => '',
        ])
        ->assertRedirect(route('inertia.customers.index'));

    $party = Party::query()->where('cnic_no', '3520212345681')->first();

    expect($party)->not->toBeNull()
        ->and((int) $party->is_customer)->toBe(1);

    expect(PartyFarm::query()->where('party_id', $party->id)->value('farm_name'))->toBe('Inertia Farm');
});

it('lists only customers on the inertia customers index', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    createInertiaParty($lookups, [
        'name' => 'Only Customer',
        'cnic_no' => '3520212345682',
        'contact_no' => '03001234582',
        'email' => 'only-customer@example.com',
        'manual_number' => 'MN-208',
        'is_customer' => 1,
        'is_vendor' => 0,
    ]);
    createInertiaParty($lookups, [
        'name' => 'Only Vendor',
        'cnic_no' => '3520212345683',
        'contact_no' => '03001234583',
        'email' => 'only-vendor@example.com',
        'manual_number' => 'MN-209',
        'is_customer' => 0,
        'is_vendor' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Customers/Index')
            ->where('customers.total', 1)
            ->where('customers.data.0.name', 'Only Customer')
        );
});

it('updates a party through inertia', function () {
    Storage::fake('public');
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);
    PartyFarm::query()->create([
        'party_id' => $party->id,
        'farm_type_id' => $lookups['farmTypeId'],
        'farm_subtype_id' => $lookups['farmSubtypeId'],
        'farm_name' => 'Old Farm',
        'farm_noc' => 'NOC-OLD',
        'farm_address' => 'Old farm address',
        'farm_image' => 'farm.jpg',
    ]);

    $this->actingAs($admin)
        ->put(route('inertia.parties.update', $party), [
            'is_customer' => '1',
            'name' => 'Updated Inertia Party',
            'guardian_name' => $party->guardian_name,
            'cnic_no' => $party->cnic_no,
            'email' => $party->email,
            'contact_no' => $party->contact_no,
            'manual_number' => $party->manual_number,
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'customer_type_id' => $lookups['customerTypeId'],
            'farm_type_id' => $lookups['farmTypeId'],
            'farm_subtype_id' => $lookups['farmSubtypeId'],
            'farm_name' => 'Updated Farm',
            'farm_noc' => 'NOC-OLD',
            'farm_address' => 'Old farm address',
        ])
        ->assertRedirect(route('inertia.parties.index'));

    expect($party->fresh()->name)->toBe('Updated Inertia Party');
});

it('deletes a party through inertia', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);

    $this->actingAs($admin)
        ->delete(route('inertia.parties.destroy', $party))
        ->assertRedirect(route('inertia.parties.index'));

    expect(Party::query()->find($party->id))->toBeNull();
});

it('creates a contact person through inertia', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->post(route('inertia.conduct-persons.store'), [
            'name' => 'Inertia Contact',
            'guardian_name' => 'Guardian',
            'cnic_no' => '3520212345690',
            'email' => 'contact@example.com',
            'contact_number' => '03001234590',
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'address' => 'Contact address',
        ])
        ->assertRedirect(route('inertia.conduct-persons.index'));

    expect(ConductPerson::query()->where('cnic_no', '3520212345690')->exists())->toBeTrue();
});

it('creates a broker through inertia', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->post(route('inertia.brokers.store'), [
            'name' => 'Inertia Broker',
            'guardian_name' => 'Guardian',
            'cnic_no' => '3520212345691',
            'email' => 'broker@example.com',
            'contact_number' => '03001234591',
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'address' => 'Broker address',
        ])
        ->assertRedirect(route('inertia.brokers.index'));

    expect(Broker::query()->where('cnic_no', '3520212345691')->value('contact_no'))->toBe('03001234591');
});

it('lists party balances in an inertia datatable', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);

    PartyBalance::query()->create([
        'party_id' => $party->id,
        'total_amount' => 1500,
        'paid_amount' => 500,
        'remaining_amount' => 1000,
        'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
        'payment_status' => Constant::PAYMENT_STATUS['Pending'],
        'transaction_date' => now()->toDateString(),
        'narration' => 'Opening',
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.party-balances.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PartyBalances/Index')
            ->where('balances.data.0.party_name', 'Listed Party')
            ->where('can.partyBalances.viewAny', true)
        );
});

it('shows a customer and vendor through the inertia resources', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $customer = createInertiaParty($lookups, [
        'name' => 'Show Customer',
        'cnic_no' => '3520212345700',
        'contact_no' => '03001234700',
        'email' => 'show-customer@example.com',
        'manual_number' => 'MN-300',
        'is_customer' => 1,
        'is_vendor' => 0,
    ]);
    $vendor = createInertiaParty($lookups, [
        'name' => 'Show Vendor',
        'cnic_no' => '3520212345701',
        'contact_no' => '03001234701',
        'email' => 'show-vendor@example.com',
        'manual_number' => 'MN-301',
        'is_customer' => 0,
        'is_vendor' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.customers.show', $customer))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Customers/Show')
            ->where('party.name', 'Show Customer')
        );

    $this->actingAs($admin)
        ->get(route('inertia.vendors.show', $vendor))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Vendors/Show')
            ->where('party.name', 'Show Vendor')
        );
});

it('still stores parties through the blade route', function () {
    Storage::fake('public');
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->post(route('parties.store'), [
            'is_vendor' => '1',
            'name' => 'Blade Vendor',
            'guardian_name' => 'Guardian Name',
            'cnic_no' => '3520212345699',
            'email' => 'blade-vendor@example.com',
            'contact_no' => '03001234599',
            'manual_number' => 'MN-299',
            'country_id' => $lookups['countryId'],
            'province_id' => $lookups['provinceId'],
            'city_id' => $lookups['cityId'],
            'vendor_division_id' => $lookups['divisionId'],
            'vendor_type_id' => $lookups['vendorTypeId'],
            'company_name' => 'Blade Vendor Company',
            'business_type_id' => $lookups['businessTypeId'],
            'company_address' => 'Company address',
            'cnic_front' => UploadedFile::fake()->image('cnic-front.jpg'),
            'cnic_back' => UploadedFile::fake()->image('cnic-back.jpg'),
            'company_logo' => UploadedFile::fake()->image('company-logo.jpg'),
        ])
        ->assertRedirect(route('parties.index'));

    expect(Party::query()->where('cnic_no', '3520212345699')->exists())->toBeTrue();
});

it('exposes create form lookups including customer and vendor sections', function () {
    $admin = inertiaPartyAdmin();
    seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->get(route('inertia.parties.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Parties/Create')
            ->has('countries')
            ->has('divisions')
            ->has('customerTypes')
            ->has('farmTypes')
            ->has('farmSubtypes')
            ->has('vendorTypes')
            ->has('businessTypes')
            ->has('contactPersons')
            ->has('amountTypes')
        );
});

it('stores a party bank account through inertia', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);

    $this->actingAs($admin)
        ->from(route('inertia.parties.index'))
        ->post(route('inertia.party-accounts.store'), [
            'party_id' => $party->id,
            'account_title' => 'Main Account',
            'account_number' => '123456789',
            'bank_name' => 'HBL',
            'opening_balance' => 2500,
        ])
        ->assertRedirect(route('inertia.parties.index'));

    $account = PartyAccount::query()->where('party_id', $party->id)->first();
    expect($account)->not->toBeNull()
        ->and($account->account_title)->toBe('Main Account')
        ->and($account->bank_name)->toBe('HBL');
});

it('stores a party document through inertia', function () {
    Storage::fake('public');
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);

    $this->actingAs($admin)
        ->from(route('inertia.parties.index'))
        ->post(route('inertia.party-documents.store'), [
            'party_id' => $party->id,
            'document_title' => 'NTN Certificate',
            'document_name' => UploadedFile::fake()->create('ntn.pdf', 20, 'application/pdf'),
        ])
        ->assertRedirect(route('inertia.parties.index'));

    $document = PartyDocument::query()->where('party_id', $party->id)->first();
    expect($document)->not->toBeNull()
        ->and($document->title)->toBe('NTN Certificate');
});

it('stores a party debit credit limit through inertia', function () {
    $admin = inertiaPartyAdmin();
    $lookups = seedInertiaPartyLookups();
    $party = createInertiaParty($lookups);

    $this->actingAs($admin)
        ->from(route('inertia.parties.index'))
        ->post(route('inertia.party-balance-limits.store'), [
            'party_id' => $party->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'debit_limit' => 10000,
            'credit_limit' => 5000,
        ])
        ->assertRedirect(route('inertia.parties.index'));

    $limit = PartyBalanceLimit::query()->where('party_id', $party->id)->first();
    expect($limit)->not->toBeNull()
        ->and((float) $limit->debit_limit)->toBe(10000.0)
        ->and((float) $limit->credit_limit)->toBe(5000.0);
});

it('creates a lookup type through inertia and keeps the blade json endpoint', function () {
    $admin = inertiaPartyAdmin();
    seedInertiaPartyLookups();

    $this->actingAs($admin)
        ->from(route('inertia.parties.create'))
        ->post(route('inertia.lookup-types.store'), [
            'tag_name' => 'customer_types',
            'name' => 'Wholesale Inertia',
        ])
        ->assertRedirect(route('inertia.parties.create'));

    expect(\Illuminate\Support\Facades\DB::table('customer_types')->where('name', 'Wholesale Inertia')->exists())->toBeTrue();

    $this->actingAs($admin)
        ->post(route('addalltypes'), [
            'tag_name' => 'vendor_types',
            'name' => 'Medicine Supplier',
        ])
        ->assertStatus(201)
        ->assertJson([
            'success' => 'yes',
            'data' => ['name' => 'Medicine Supplier'],
        ]);
});
