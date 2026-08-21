<?php

use App\Models\ChickPurchase;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Party;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Models\User;
use App\Models\VaccinationSchedule;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function seedCustomerFarmFixture(): array
{
    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    $farmTypeId = FarmType::query()->insertGetId([
        'name' => 'Layer',
        'slug' => 'layer',
        ...$timestamps,
    ]);

    $farmSubtypeId = FarmSubtype::query()->insertGetId([
        'name' => 'Commercial',
        'slug' => 'commercial',
        ...$timestamps,
    ]);

    $partyId = Party::query()->insertGetId([
        'is_vendor' => 0,
        'is_customer' => 1,
        'name' => 'Fixture Customer',
        'guardian_name' => 'Guardian',
        'cnic_no' => '3520212345678',
        'contact_no' => '03001234567',
        'manual_number' => 'MN-100',
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
        ...$timestamps,
    ]);

    $farmId = PartyFarm::query()->insertGetId([
        'party_id' => $partyId,
        'farm_type_id' => $farmTypeId,
        'farm_subtype_id' => $farmSubtypeId,
        'farm_name' => 'Fixture Farm',
        'farm_noc' => 'NOC-100',
        'farm_image' => 'existing-farm.jpg',
        'farm_address' => 'Farm address',
        'farm_area' => 1000,
        'feed_room_size' => 200,
        'farm_capacity' => 5000,
        'addedby' => 1,
        ...$timestamps,
    ]);

    return compact('farmTypeId', 'farmSubtypeId', 'partyId', 'farmId');
}

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());

    DB::table('countries')->insertOrIgnore([
        'id' => 1,
        'name' => 'Pakistan',
        'slug' => 'pakistan',
    ]);
    DB::table('provinces')->insertOrIgnore([
        'id' => 1,
        'name' => 'Punjab',
        'country_id' => 1,
    ]);
    DB::table('cities')->insertOrIgnore([
        'id' => 1,
        'name' => 'Lahore',
        'province_id' => 1,
    ]);
});

test('customer farm edit returns farm and party json', function () {
    $fixture = seedCustomerFarmFixture();

    $this->getJson(route('customerfarms.edit', $fixture['farmId']))
        ->assertOk()
        ->assertJsonPath('message', 'yes')
        ->assertJsonPath('farm.id', $fixture['farmId'])
        ->assertJsonPath('farm.farm_name', 'Fixture Farm')
        ->assertJsonPath('party.name', 'Fixture Customer')
        ->assertJsonPath('party.cnic_no', '3520212345678');
});

test('customer farm update persists fields via ajax json response', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/farm/existing-farm.jpg', 'farm-bytes');

    $fixture = seedCustomerFarmFixture();

    $response = $this->postJson(route('customerfarms.update', $fixture['farmId']), [
        '_method' => 'PUT',
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => 'Updated Farm',
        'farm_noc' => 'NOC-200',
        'farm_address' => 'Updated address',
        'farm_area' => 1200,
        'feed_room_size' => 250,
        'farm_capacity' => 6000,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', 'yes');

    $this->assertDatabaseHas('party_farms', [
        'id' => $fixture['farmId'],
        'farm_name' => 'Updated Farm',
        'farm_noc' => 'NOC-200',
        'farm_address' => 'Updated address',
        'farm_image' => 'existing-farm.jpg',
    ]);
});

test('customer farm update replaces image when a new file is uploaded', function () {
    Storage::fake('public');
    Storage::disk('public')->put('party/farm/existing-farm.jpg', 'farm-bytes');

    $fixture = seedCustomerFarmFixture();

    $this->post(route('customerfarms.update', $fixture['farmId']), [
        '_method' => 'PUT',
        'farm_type_id' => $fixture['farmTypeId'],
        'farm_subtype_id' => $fixture['farmSubtypeId'],
        'farm_name' => 'Updated Farm',
        'farm_noc' => 'NOC-200',
        'farm_address' => 'Updated address',
        'farm_image' => UploadedFile::fake()->image('new-farm.jpg'),
    ])->assertOk();

    $farm = PartyFarm::query()->findOrFail($fixture['farmId']);
    expect($farm->farm_image)->not->toBe('existing-farm.jpg');
    Storage::disk('public')->assertExists('party/farm/'.$farm->farm_image);
});

test('customer farm destroy soft deletes the farm', function () {
    $fixture = seedCustomerFarmFixture();

    $this->delete(route('customerfarms.destroy', $fixture['farmId']))
        ->assertRedirect(route('customerfarms.index'));

    $this->assertSoftDeleted('party_farms', ['id' => $fixture['farmId']]);
});

test('customer farm destroy is blocked when linked to vaccination schedules', function () {
    $fixture = seedCustomerFarmFixture();

    VaccinationSchedule::query()->create([
        'party_farm_id' => $fixture['farmId'],
        'schedule_date' => '2026-08-01',
        'is_vaccinated' => 0,
        'is_active' => 1,
    ]);

    $this->delete(route('customerfarms.destroy', $fixture['farmId']))
        ->assertRedirect(route('customerfarms.index'))
        ->assertSessionHas('swal_notification');

    expect(PartyFarm::query()->find($fixture['farmId']))->not->toBeNull();
});

test('customer farm destroy is blocked when linked to chick purchases', function () {
    $fixture = seedCustomerFarmFixture();

    ChickPurchase::query()->create([
        'party_farm_id' => $fixture['farmId'],
        'purchase_date' => '2026-08-01',
        'quantity' => 100,
    ]);

    $this->delete(route('customerfarms.destroy', $fixture['farmId']))
        ->assertRedirect(route('customerfarms.index'))
        ->assertSessionHas('swal_notification');

    expect(PartyFarm::query()->find($fixture['farmId']))->not->toBeNull();
});

test('customer farm destroy is blocked when linked to chick purchase history', function () {
    $fixture = seedCustomerFarmFixture();

    PartyFarmChickHistory::query()->create([
        'party_farm_id' => $fixture['farmId'],
        'quantity' => 100,
        'entry_date' => '2026-08-01',
    ]);

    $this->delete(route('customerfarms.destroy', $fixture['farmId']))
        ->assertRedirect(route('customerfarms.index'))
        ->assertSessionHas('swal_notification');

    expect(PartyFarm::query()->find($fixture['farmId']))->not->toBeNull();
});
