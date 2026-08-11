<?php

use App\Models\BusinessType;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\PartyFarm;
use App\Models\Province;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\VendorType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function seedPartyActionFixture(): array
{
    $userLevelId = UserLevel::query()->insertGetId([
        'name' => 'Admin',
        'slug' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $creatorId = User::query()->insertGetId([
        'name' => 'Creator User',
        'email' => 'creator@example.com',
        'password' => bcrypt('password'),
        'user_level_id' => $userLevelId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $updaterId = User::query()->insertGetId([
        'name' => 'Updater User',
        'email' => 'updater@example.com',
        'password' => bcrypt('password'),
        'user_level_id' => $userLevelId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

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
        'creatorId',
        'updaterId',
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

function customerPartyUpdatePayload(array $fixture, Party $party, PartyFarm $farm): array
{
    return [
        '_method' => 'PUT',
        'is_customer' => '1',
        'name' => 'Updated Customer Party',
        'guardian_name' => $party->guardian_name,
        'cnic_no' => $party->cnic_no,
        'email' => $party->email,
        'contact_no' => $party->contact_no,
        'manual_number' => $party->manual_number,
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'address' => $party->address,
        'customer_type_id' => $fixture['customerTypeId'],
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => $farm->farm_name,
        'farm_noc' => $farm->farm_noc,
        'farm_address' => $farm->farm_address,
    ];
}

function vendorPartyUpdatePayload(array $fixture, Party $party, PartyCompany $company): array
{
    return [
        '_method' => 'PUT',
        'is_vendor' => '1',
        'name' => 'Updated Vendor Party',
        'guardian_name' => $party->guardian_name,
        'cnic_no' => $party->cnic_no,
        'email' => $party->email,
        'contact_no' => $party->contact_no,
        'manual_number' => $party->manual_number,
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'address' => $party->address,
        'vendor_division_id' => $fixture['divisionId'],
        'vendor_type_id' => $fixture['vendorTypeId'],
        'company_name' => $company->company_name,
        'business_type_id' => $fixture['businessTypeId'],
        'company_address' => $company->company_address,
    ];
}

it('updates a customer party without replacement farm_image and preserves attribution', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/existing-profile.jpg', 'profile-bytes');
    Storage::disk('public')->put('party/farm/existing-farm.jpg', 'farm-bytes');

    $fixture = seedPartyActionFixture();

    $party = Party::query()->create([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Original Customer Party',
        'guardian_name' => 'Guardian Name',
        'cnic_no' => '3520212345671',
        'email' => 'customer@example.com',
        'contact_no' => '03001234567',
        'manual_number' => 'MN-001',
        'address' => 'Customer address',
        'customer_type_id' => $fixture['customerTypeId'],
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'cnic_front' => 'existing-front.jpg',
        'cnic_back' => 'existing-back.jpg',
        'profile_picture' => 'existing-profile.jpg',
        'addedby' => $fixture['creatorId'],
    ]);

    $farm = PartyFarm::query()->create([
        'party_id' => $party->id,
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => 'Farm One',
        'farm_noc' => 'NOC-001',
        'farm_image' => 'existing-farm.jpg',
        'farm_address' => 'Farm address',
        'addedby' => $fixture['creatorId'],
    ]);

    $this->actingAs(User::find($fixture['updaterId']))
        ->put(route('parties.update', $party->id), customerPartyUpdatePayload($fixture, $party, $farm))
        ->assertRedirect(route('parties.index'));

    $party->refresh();
    $farm->refresh();

    expect($party->name)->toBe('Updated Customer Party');
    expect($party->profile_picture)->toBe('existing-profile.jpg');
    expect($party->addedby)->toBe($fixture['creatorId']);
    expect($party->updatedby)->toBe($fixture['updaterId']);
    expect($farm->farm_image)->toBe('existing-farm.jpg');
    expect($farm->addedby)->toBe($fixture['creatorId']);
    expect($farm->updatedby)->toBe($fixture['updaterId']);
    Storage::disk('public')->assertExists('party/existing-profile.jpg');
    Storage::disk('public')->assertExists('party/farm/existing-farm.jpg');
});

it('updates a vendor party without replacement company_logo and preserves attribution', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/company/existing-logo.jpg', 'logo-bytes');

    $fixture = seedPartyActionFixture();

    $party = Party::query()->create([
        'is_customer' => 0,
        'is_vendor' => 1,
        'name' => 'Original Vendor Party',
        'guardian_name' => 'Guardian Name',
        'cnic_no' => '3520212345672',
        'email' => 'vendor@example.com',
        'contact_no' => '03001234568',
        'manual_number' => 'MN-002',
        'address' => 'Vendor address',
        'vendor_type_id' => $fixture['vendorTypeId'],
        'vendor_division_id' => $fixture['divisionId'],
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'cnic_front' => 'existing-front.jpg',
        'cnic_back' => 'existing-back.jpg',
        'addedby' => $fixture['creatorId'],
    ]);

    $company = PartyCompany::query()->create([
        'party_id' => $party->id,
        'company_name' => 'Vendor Co',
        'business_type_id' => $fixture['businessTypeId'],
        'company_logo' => 'existing-logo.jpg',
        'company_address' => 'Company address',
        'addedby' => $fixture['creatorId'],
    ]);

    $this->actingAs(User::find($fixture['updaterId']))
        ->put(route('parties.update', $party->id), vendorPartyUpdatePayload($fixture, $party, $company))
        ->assertRedirect(route('parties.index'));

    $party->refresh();
    $company->refresh();

    expect($party->name)->toBe('Updated Vendor Party');
    expect($party->addedby)->toBe($fixture['creatorId']);
    expect($party->updatedby)->toBe($fixture['updaterId']);
    expect($company->company_logo)->toBe('existing-logo.jpg');
    expect($company->addedby)->toBe($fixture['creatorId']);
    expect($company->updatedby)->toBe($fixture['updaterId']);
    Storage::disk('public')->assertExists('party/company/existing-logo.jpg');
});

it('soft-deletes a party without removing media filenames or files', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/existing-profile.jpg', 'profile-bytes');

    $fixture = seedPartyActionFixture();

    $party = Party::query()->create([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Party To Delete',
        'guardian_name' => 'Guardian Name',
        'cnic_no' => '3520212345673',
        'email' => 'delete@example.com',
        'contact_no' => '03001234569',
        'manual_number' => 'MN-003',
        'address' => 'Delete address',
        'customer_type_id' => $fixture['customerTypeId'],
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'cnic_front' => 'existing-front.jpg',
        'cnic_back' => 'existing-back.jpg',
        'profile_picture' => 'existing-profile.jpg',
        'addedby' => $fixture['creatorId'],
    ]);

    $this->actingAs(User::find($fixture['updaterId']))
        ->delete(route('parties.destroy', $party->id))
        ->assertRedirect(route('parties.index'));

    expect(Party::query()->find($party->id))->toBeNull();

    $deletedParty = Party::withTrashed()->find($party->id);

    expect($deletedParty)->not->toBeNull();
    expect($deletedParty->deleted_at)->not->toBeNull();
    expect($deletedParty->profile_picture)->toBe('existing-profile.jpg');
    expect($deletedParty->addedby)->toBe($fixture['creatorId']);
    Storage::disk('public')->assertExists('party/existing-profile.jpg');
});

it('stores a replacement signature file on update', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/existing-signature.jpg', 'signature-bytes');

    $fixture = seedPartyActionFixture();

    $party = Party::query()->create([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Signature Party',
        'guardian_name' => 'Guardian Name',
        'cnic_no' => '3520212345674',
        'email' => 'signature@example.com',
        'contact_no' => '03001234570',
        'manual_number' => 'MN-004',
        'address' => 'Signature address',
        'customer_type_id' => $fixture['customerTypeId'],
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'cnic_front' => 'existing-front.jpg',
        'cnic_back' => 'existing-back.jpg',
        'signature' => 'existing-signature.jpg',
        'addedby' => $fixture['creatorId'],
    ]);

    $farm = PartyFarm::query()->create([
        'party_id' => $party->id,
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => 'Farm One',
        'farm_noc' => 'NOC-001',
        'farm_image' => 'existing-farm.jpg',
        'farm_address' => 'Farm address',
        'addedby' => $fixture['creatorId'],
    ]);

    Storage::disk('public')->put('party/existing-front.jpg', 'front-bytes');
    Storage::disk('public')->put('party/existing-back.jpg', 'back-bytes');
    Storage::disk('public')->put('party/farm/existing-farm.jpg', 'farm-bytes');

    $payload = customerPartyUpdatePayload($fixture, $party, $farm);
    $payload['signature_image'] = UploadedFile::fake()->image('replacement-signature.jpg');

    $this->actingAs(User::find($fixture['updaterId']))
        ->put(route('parties.update', $party->id), $payload)
        ->assertRedirect(route('parties.index'));

    $party->refresh();

    expect($party->signature)->not->toBe('existing-signature.jpg');
    expect($party->signature)->not->toBeNull();
    Storage::disk('public')->assertExists('party/'.$party->signature);
    Storage::disk('public')->assertExists('party/existing-signature.jpg');
});

it('renders edit form without required replacement media inputs', function () {
    $fixture = seedPartyActionFixture();

    $party = Party::query()->create([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Edit Form Party',
        'guardian_name' => 'Guardian Name',
        'cnic_no' => '3520212345675',
        'email' => 'editform@example.com',
        'contact_no' => '03001234571',
        'manual_number' => 'MN-005',
        'address' => 'Edit form address',
        'customer_type_id' => $fixture['customerTypeId'],
        'country_id' => $fixture['countryId'],
        'province_id' => $fixture['provinceId'],
        'city_id' => $fixture['cityId'],
        'cnic_front' => 'existing-front.jpg',
        'cnic_back' => 'existing-back.jpg',
        'signature' => 'existing-signature.jpg',
        'addedby' => $fixture['creatorId'],
    ]);

    PartyFarm::query()->create([
        'party_id' => $party->id,
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => 'Farm One',
        'farm_noc' => 'NOC-001',
        'farm_image' => 'existing-farm.jpg',
        'farm_address' => 'Farm address',
        'addedby' => $fixture['creatorId'],
    ]);

    $editResponse = $this->actingAs(User::find($fixture['updaterId']))
        ->get(route('parties.edit', $party->id));

    $editResponse->assertOk();
    $editHtml = $editResponse->getContent();

    expect($editHtml)->toMatch('/name="cnic_front"/');
    expect($editHtml)->toMatch('/name="cnic_back"/');
    expect($editHtml)->toMatch('/name="signature_image"/');
    expect($editHtml)->not->toMatch('/name="cnic_front"[^>]*\srequired/');
    expect($editHtml)->not->toMatch('/name="cnic_back"[^>]*\srequired/');
    expect($editHtml)->not->toMatch('/name="signature_image"[^>]*\srequired/');

    $createResponse = $this->actingAs(User::find($fixture['updaterId']))
        ->get(route('parties.create'));

    $createResponse->assertOk();
    $createHtml = $createResponse->getContent();

    expect($createHtml)->toMatch('/name="cnic_front"[^>]*\srequired/');
    expect($createHtml)->toMatch('/name="cnic_back"[^>]*\srequired/');
    expect($createHtml)->not->toMatch('/name="signature_image"[^>]*\srequired/');
});
