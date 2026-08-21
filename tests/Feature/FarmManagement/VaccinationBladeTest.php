<?php

use App\Models\User;
use App\Models\VaccinationSchedule;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

function seedVaccinationLookups(): array
{
    $timestamps = [
        'created_at' => '2026-08-01 00:00:00',
        'updated_at' => '2026-08-01 00:00:00',
    ];

    $farmId = DB::table('party_farms')->insertGetId([
        'farm_name' => 'Customer Farm',
        ...$timestamps,
    ]);
    $productId = DB::table('products')->insertGetId([
        'product_number' => 1,
        'product_code' => 'VAC-001',
        'bar_code' => 'VAC-BAR-001',
        'product_name' => 'Newcastle Vaccine',
        ...$timestamps,
    ]);

    return compact('farmId', 'productId');
}

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());
    $this->vaccinationLookups = seedVaccinationLookups();
});

test('vaccination schedule create rejects a date before today with the legacy ajax shape', function () {
    $this->postJson(route('vaccination.store'), [
        'farm_id' => $this->vaccinationLookups['farmId'],
        'product_id' => $this->vaccinationLookups['productId'],
        'schedule_date' => today()->subDay()->format('Y-m-d'),
        'description' => 'Past schedule',
    ])->assertStatus(201)
        ->assertJsonPath('success', 'no')
        ->assertJsonStructure(['error' => ['schedule_date']]);
});

test('vaccination schedule create requires existing farm and product ids', function () {
    $this->postJson(route('vaccination.store'), [
        'schedule_date' => today()->format('Y-m-d'),
        'description' => 'Missing relations',
    ])->assertStatus(201)
        ->assertJsonPath('success', 'no')
        ->assertJsonStructure(['error' => ['farm_id', 'product_id']]);
});

test('vaccination schedule create persists the description and audit fields', function () {
    $this->postJson(route('vaccination.store'), [
        'farm_id' => $this->vaccinationLookups['farmId'],
        'product_id' => $this->vaccinationLookups['productId'],
        'schedule_date' => today()->addDay()->format('Y-m-d'),
        'description' => 'First dose',
    ])->assertOk()
        ->assertJsonPath('title', 'Success')
        ->assertJsonPath('message', 'Vaccination schedule added successfully!');

    $schedule = VaccinationSchedule::query()->firstOrFail();
    expect($schedule->party_farm_id)->toBe($this->vaccinationLookups['farmId'])
        ->and($schedule->product_id)->toBe($this->vaccinationLookups['productId'])
        ->and($schedule->schedule_date->format('Y-m-d'))->toBe(today()->addDay()->format('Y-m-d'))
        ->and($schedule->description)->toBe('First dose')
        ->and($schedule->addedby)->toBe(User::query()->firstOrFail()->id);
});

test('record vaccination rejects a date before its schedule with the legacy ajax shape', function () {
    $schedule = VaccinationSchedule::query()->create([
        'party_farm_id' => $this->vaccinationLookups['farmId'],
        'product_id' => $this->vaccinationLookups['productId'],
        'schedule_date' => today()->addDays(2),
        'description' => 'Scheduled dose',
        'addedby' => User::query()->firstOrFail()->id,
    ]);

    $this->postJson(route('addVaccination'), [
        'schedule_id' => $schedule->id,
        'vaccination_date' => today()->addDay()->format('Y-m-d'),
        'remarks' => 'Too early',
    ])->assertStatus(201)
        ->assertJsonPath('success', 'no')
        ->assertJsonStructure(['error' => ['vaccination_date']]);
});

test('record vaccination marks the schedule vaccinated and persists audit fields', function () {
    $schedule = VaccinationSchedule::query()->create([
        'party_farm_id' => $this->vaccinationLookups['farmId'],
        'product_id' => $this->vaccinationLookups['productId'],
        'schedule_date' => today()->addDay(),
        'description' => 'Scheduled dose',
        'addedby' => User::query()->firstOrFail()->id,
    ]);

    $this->postJson(route('addVaccination'), [
        'schedule_id' => $schedule->id,
        'vaccination_date' => today()->addDays(2)->format('Y-m-d'),
        'remarks' => 'Completed',
    ])->assertOk()
        ->assertJsonPath('title', 'Success')
        ->assertJsonPath('message', 'Vaccine has been  added successfully!');

    $schedule->refresh();
    expect($schedule->is_vaccinated)->toBe(1)
        ->and($schedule->vaccination_date->format('Y-m-d'))->toBe(today()->addDays(2)->format('Y-m-d'))
        ->and($schedule->vaccinated_remarks)->toBe('Completed')
        ->and($schedule->updatedby)->toBe(User::query()->firstOrFail()->id);
});
