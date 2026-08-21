<?php

use App\Actions\FarmManagement\StorePersonalFarmAction;
use App\Actions\FarmManagement\UpdatePersonalFarmAction;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\PersonalFarm;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function seedPersonalFarmLookups(): array
{
    $timestamps = [
        'created_at' => '2026-08-01 00:00:00',
        'updated_at' => '2026-08-01 00:00:00',
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

    return compact('farmTypeId', 'farmSubtypeId');
}

function personalFarmPayload(array $lookups, array $overrides = []): array
{
    return array_merge([
        'farm_type_id' => $lookups['farmTypeId'],
        'farm_subtype_id' => $lookups['farmSubtypeId'],
        'farm_name' => 'Main Farm',
        'farm_noc' => 'NOC-100',
        'farm_area' => 1200,
        'farm_capacity' => 5000,
        'feed_room_size' => 250,
        'farm_address' => 'Farm Road',
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
    ], $overrides);
}

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());
    Storage::fake('public');
    $this->personalFarmLookups = seedPersonalFarmLookups();
});

test('personal farm create requires an image', function () {
    $this->post(route('personalfarms.store'), personalFarmPayload($this->personalFarmLookups))
        ->assertSessionHasErrors('farm_image');
});

test('personal farm create persists fields and stores its image', function () {
    $this->post(route('personalfarms.store'), personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => UploadedFile::fake()->image('farm.jpg'),
    ]))->assertRedirect(route('personalfarms.index'));

    $farm = PersonalFarm::query()->where('farm_name', 'Main Farm')->firstOrFail();

    expect($farm->farm_code)->toBe('PF-001')
        ->and($farm->addedby)->toBe(User::query()->firstOrFail()->id);
    Storage::disk('public')->assertExists('personalfarms/'.$farm->farm_image);
});

test('personal farm update replaces the existing image and removes the old file', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'old-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/old-farm.jpg', 'old-image');

    $this->put(route('personalfarms.update', $farm), personalFarmPayload($this->personalFarmLookups, [
        'farm_name' => 'Updated Farm',
        'farm_image' => UploadedFile::fake()->image('new-farm.png'),
    ]))->assertRedirect(route('personalfarms.index'))
        ->assertSessionDoesntHaveErrors();

    $farm->refresh();
    expect($farm->farm_name)->toBe('Updated Farm')
        ->and($farm->farm_image)->not->toBe('old-farm.jpg');
    Storage::disk('public')->assertMissing('personalfarms/old-farm.jpg');
    Storage::disk('public')->assertExists('personalfarms/'.$farm->farm_image);
});

test('personal farm update preserves the existing image when no replacement is uploaded', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'existing-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/existing-farm.jpg', 'existing-image');

    $this->put(route('personalfarms.update', $farm), personalFarmPayload($this->personalFarmLookups, [
        'farm_name' => 'Updated Farm',
    ]))->assertRedirect(route('personalfarms.index'))
        ->assertSessionDoesntHaveErrors();

    expect($farm->refresh()->farm_name)->toBe('Updated Farm')
        ->and($farm->farm_image)->toBe('existing-farm.jpg');
    Storage::disk('public')->assertExists('personalfarms/existing-farm.jpg');
});

test('personal farm destroy soft deletes the farm and preserves its image', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'existing-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/existing-farm.jpg', 'existing-image');

    $this->delete(route('personalfarms.destroy', $farm))
        ->assertRedirect(route('personalfarms.index'));

    $this->assertSoftDeleted('personal_farms', ['id' => $farm->id]);
    Storage::disk('public')->assertExists('personalfarms/existing-farm.jpg');
});

test('personal farm routes preserve guest redirects and authenticated compatibility', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'farm.jpg',
    ]));

    session()->flush();
    app('auth')->forgetGuards();
    foreach ([
        route('personalfarms.index'),
        route('personalfarms.create'),
        route('personalfarms.edit', $farm),
        route('personalfarms.show', $farm),
    ] as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }

    $this->actingAs(User::query()->firstOrFail());
    $this->get(route('personalfarms.index'))->assertOk();
    $this->get(route('personalfarms.create'))->assertOk();
    $this->get(route('personalfarms.edit', $farm))->assertOk();
    $this->get(route('personalfarms.show', $farm))->assertOk();
});

test('personal farm update preserves the existing image when the replacement upload is rejected', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'old-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/old-farm.jpg', 'old-image');

    expect(fn () => app(UpdatePersonalFarmAction::class)->execute(
        $farm,
        personalFarmPayload($this->personalFarmLookups),
        UploadedFile::fake()->create('evil.php', 20),
        User::query()->firstOrFail()->id,
    ))->toThrow(InvalidArgumentException::class);

    expect($farm->refresh()->farm_image)->toBe('old-farm.jpg');
    Storage::disk('public')->assertExists('personalfarms/old-farm.jpg');
    expect(Storage::disk('public')->allFiles('personalfarms'))->toBe([
        'personalfarms/old-farm.jpg',
    ]);
});

test('personal farm store cleans up its staged image when persistence fails', function () {
    $payload = personalFarmPayload($this->personalFarmLookups, [
        'farm_type_id' => 999999,
    ]);

    expect(fn () => app(StorePersonalFarmAction::class)->execute(
        $payload,
        UploadedFile::fake()->image('farm.jpg'),
        User::query()->firstOrFail()->id,
    ))->toThrow(QueryException::class);

    expect(Storage::disk('public')->allFiles('personalfarms'))->toBe([]);
});

test('personal farm update preserves the old image and removes the staged image when persistence fails', function () {
    $farm = PersonalFarm::query()->create(personalFarmPayload($this->personalFarmLookups, [
        'farm_image' => 'old-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/old-farm.jpg', 'old-image');
    $payload = personalFarmPayload($this->personalFarmLookups, [
        'farm_type_id' => 999999,
    ]);

    expect(fn () => app(UpdatePersonalFarmAction::class)->execute(
        $farm,
        $payload,
        UploadedFile::fake()->image('new-farm.jpg'),
        User::query()->firstOrFail()->id,
    ))->toThrow(QueryException::class);

    expect(Storage::disk('public')->allFiles('personalfarms'))->toBe([
        'personalfarms/old-farm.jpg',
    ]);
});
