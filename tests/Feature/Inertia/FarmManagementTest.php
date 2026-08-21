<?php

use App\Models\Employee;
use App\Models\PartyFarm;
use App\Models\PersonalFarm;
use App\Models\User;
use App\Models\VaccinationSchedule;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
    Storage::fake('public');
});

function inertiaFarmUser(): User
{
    return User::query()->firstOrFail();
}

it('lists personal farms through inertia', function () {
    $lookups = seedPersonalFarmLookups();
    PersonalFarm::query()->create(personalFarmPayload($lookups, [
        'farm_name' => 'Inertia Personal Farm',
        'farm_image' => 'farm.jpg',
    ]));

    $this->actingAs(inertiaFarmUser())
        ->get(route('inertia.personal-farms.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PersonalFarms/Index')
            ->has('farms.data')
            ->has('filters')
            ->where('farms.data.0.farm_name', 'Inertia Personal Farm')
        );
});

it('creates a personal farm through inertia using the shared action', function () {
    $lookups = seedPersonalFarmLookups();

    $this->actingAs(inertiaFarmUser())
        ->post(route('inertia.personal-farms.store'), personalFarmPayload($lookups, [
            'farm_name' => 'Created Inertia Farm',
            'farm_image' => UploadedFile::fake()->image('farm.jpg'),
        ]))
        ->assertRedirect(route('inertia.personal-farms.index'));

    $farm = PersonalFarm::query()->where('farm_name', 'Created Inertia Farm')->firstOrFail();
    expect($farm->farm_code)->toStartWith('PF-');
    Storage::disk('public')->assertExists('personalfarms/'.$farm->farm_image);
});

it('updates and deletes a personal farm through inertia', function () {
    $lookups = seedPersonalFarmLookups();
    $farm = PersonalFarm::query()->create(personalFarmPayload($lookups, [
        'farm_image' => 'old-farm.jpg',
    ]));
    Storage::disk('public')->put('personalfarms/old-farm.jpg', 'old');

    $this->actingAs(inertiaFarmUser())
        ->put(route('inertia.personal-farms.update', $farm), personalFarmPayload($lookups, [
            'farm_name' => 'Updated Inertia Farm',
        ]))
        ->assertRedirect(route('inertia.personal-farms.index'));

    expect($farm->refresh()->farm_name)->toBe('Updated Inertia Farm')
        ->and($farm->farm_image)->toBe('old-farm.jpg');

    $this->actingAs(inertiaFarmUser())
        ->delete(route('inertia.personal-farms.destroy', $farm))
        ->assertRedirect(route('inertia.personal-farms.index'));

    $this->assertSoftDeleted('personal_farms', ['id' => $farm->id]);
});

it('still stores personal farms through the blade route', function () {
    $lookups = seedPersonalFarmLookups();

    $this->actingAs(inertiaFarmUser())
        ->post(route('personalfarms.store'), personalFarmPayload($lookups, [
            'farm_name' => 'Blade Personal Farm',
            'farm_image' => UploadedFile::fake()->image('farm.jpg'),
        ]))
        ->assertRedirect(route('personalfarms.index'));

    expect(PersonalFarm::query()->where('farm_name', 'Blade Personal Farm')->exists())->toBeTrue();
});

it('lists and updates customer farms through inertia without a create route', function () {
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
    $fixture = seedCustomerFarmFixture();

    $this->actingAs(inertiaFarmUser())
        ->get(route('inertia.customer-farms.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CustomerFarms/Index')
            ->has('farms.data')
            ->has('farmTypes')
            ->where('farms.data.0.farm_name', 'Fixture Farm')
        );

    expect(fn () => route('inertia.customer-farms.create'))->toThrow(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);

    $this->actingAs(inertiaFarmUser())
        ->put(route('inertia.customer-farms.update', $fixture['farmId']), [
            'farm_type_id' => $fixture['farmTypeId'],
            'farm_subtype_id' => $fixture['farmSubtypeId'],
            'farm_name' => 'Updated Customer Farm',
            'farm_noc' => 'NOC-200',
            'farm_address' => 'Updated address',
            'farm_area' => 1100,
            'feed_room_size' => 220,
            'farm_capacity' => 5500,
        ])
        ->assertRedirect(route('inertia.customer-farms.index'));

    expect(PartyFarm::query()->findOrFail($fixture['farmId'])->farm_name)->toBe('Updated Customer Farm');
});

it('creates an employee through inertia with farm assignment', function () {
    $lookups = seedEmployeeLookups();

    $this->actingAs(inertiaFarmUser())
        ->post(route('inertia.employees.store'), employeePayload($lookups, [
            'name' => 'Inertia Employee',
            'cnic_no' => '3520212345699',
            'employee_image' => UploadedFile::fake()->image('employee.jpg'),
        ]))
        ->assertRedirect(route('inertia.employees.index'));

    $employee = Employee::query()->where('cnic_no', '3520212345699')->firstOrFail();
    expect($employee->personal_farm_id)->toBe($lookups['firstFarmId'])
        ->and($employee->father_cnic_no)->toBe('3520212345679');
    Storage::disk('public')->assertExists('employee/'.$employee->employee_image);
});

it('shows and lists employees through inertia with employee media urls', function () {
    $lookups = seedEmployeeLookups();
    $employee = Employee::query()->create(employeePayload($lookups, [
        'employee_image' => 'photo.jpg',
    ]));

    $this->actingAs(inertiaFarmUser())
        ->get(route('inertia.employees.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Employees/Index')
            ->where('employees.data.0.employee_image_url', asset('storage/employee/photo.jpg'))
        );

    $this->actingAs(inertiaFarmUser())
        ->get(route('inertia.employees.show', $employee))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Employees/Show')
            ->where('employee.personal_farm_id', $lookups['firstFarmId'])
        );
});

it('creates a vaccination schedule and records vaccination through inertia', function () {
    $lookups = seedVaccinationLookups();

    $this->actingAs(inertiaFarmUser())
        ->post(route('inertia.vaccinations.store'), [
            'farm_id' => $lookups['farmId'],
            'product_id' => $lookups['productId'],
            'schedule_date' => today()->addDay()->format('Y-m-d'),
            'description' => 'Inertia schedule',
        ])
        ->assertRedirect(route('inertia.vaccinations.index'));

    $schedule = VaccinationSchedule::query()->firstOrFail();
    expect($schedule->description)->toBe('Inertia schedule');

    $this->actingAs(inertiaFarmUser())
        ->post(route('inertia.vaccinations.record'), [
            'schedule_id' => $schedule->id,
            'vaccination_date' => today()->addDays(2)->format('Y-m-d'),
            'remarks' => 'Completed',
        ])
        ->assertRedirect(route('inertia.vaccinations.index'));

    expect($schedule->refresh()->is_vaccinated)->toBe(1)
        ->and($schedule->vaccinated_remarks)->toBe('Completed');
});

it('rejects an inertia vaccination schedule date before today', function () {
    $lookups = seedVaccinationLookups();

    $this->actingAs(inertiaFarmUser())
        ->from(route('inertia.vaccinations.index'))
        ->post(route('inertia.vaccinations.store'), [
            'farm_id' => $lookups['farmId'],
            'product_id' => $lookups['productId'],
            'schedule_date' => today()->subDay()->format('Y-m-d'),
        ], [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertRedirect(route('inertia.vaccinations.index'))
        ->assertSessionHasErrors('schedule_date');
});

it('toggles vaccination status through inertia', function () {
    $lookups = seedVaccinationLookups();
    $schedule = VaccinationSchedule::query()->create([
        'party_farm_id' => $lookups['farmId'],
        'product_id' => $lookups['productId'],
        'schedule_date' => today()->addDay(),
        'is_active' => 1,
    ]);

    $this->actingAs(inertiaFarmUser())
        ->put(route('inertia.vaccinations.toggle-status', $schedule))
        ->assertRedirect(route('inertia.vaccinations.index'));

    expect((int) $schedule->refresh()->is_active)->toBe(0);
});

it('redirects guests away from inertia farm management pages', function () {
    $this->get(route('inertia.personal-farms.index'))->assertRedirect();
    $this->get(route('inertia.customer-farms.index'))->assertRedirect();
    $this->get(route('inertia.employees.index'))->assertRedirect();
    $this->get(route('inertia.vaccinations.index'))->assertRedirect();
});
