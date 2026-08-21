<?php

use App\Actions\FarmManagement\StoreEmployeeAction;
use App\Actions\FarmManagement\UpdateEmployeeAction;
use App\Models\Employee;
use App\Models\PersonalFarm;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

function seedEmployeeLookups(): array
{
    $timestamps = [
        'created_at' => '2026-08-01 00:00:00',
        'updated_at' => '2026-08-01 00:00:00',
    ];

    $farmTypeId = DB::table('farm_types')->insertGetId([
        'name' => 'Layer',
        'slug' => 'layer',
        ...$timestamps,
    ]);
    $farmSubtypeId = DB::table('farm_subtypes')->insertGetId([
        'name' => 'Commercial',
        'slug' => 'commercial',
        ...$timestamps,
    ]);
    $firstFarmId = PersonalFarm::query()->insertGetId([
        'farm_type_id' => $farmTypeId,
        'farm_subtype_id' => $farmSubtypeId,
        'farm_name' => 'First Farm',
        ...$timestamps,
    ]);
    $secondFarmId = PersonalFarm::query()->insertGetId([
        'farm_type_id' => $farmTypeId,
        'farm_subtype_id' => $farmSubtypeId,
        'farm_name' => 'Second Farm',
        ...$timestamps,
    ]);
    $employeeTypeId = DB::table('employee_types')->insertGetId([
        'name' => 'Worker',
        'slug' => 'worker',
        ...$timestamps,
    ]);
    $employeeLevelId = DB::table('employee_levels')->insertGetId([
        'name' => 'Senior',
        'slug' => 'senior',
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

    return compact('firstFarmId', 'secondFarmId', 'employeeTypeId', 'employeeLevelId');
}

function employeePayload(array $lookups, array $overrides = []): array
{
    return array_merge([
        'personal_farm_id' => $lookups['firstFarmId'],
        'employee_type_id' => $lookups['employeeTypeId'],
        'employee_level_id' => $lookups['employeeLevelId'],
        'name' => 'Farm Worker',
        'guardian_name' => 'Worker Guardian',
        'contact_no' => '03001234567',
        'other_number' => '03007654321',
        'email' => 'worker@example.com',
        'cnic_no' => '3520212345678',
        'father_cnic_no' => '3520212345679',
        'basic_salary' => 30000,
        'other_amount' => 2500,
        'net_salary' => 32500,
        'contract_period' => 12,
        'date_of_birth' => '1990-01-15',
        'joining_date' => '2026-08-01',
        'is_police_record' => 0,
        'address' => 'Employee Address',
        'blood_group' => 'A+',
        'description' => 'Farm employee',
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
    ], $overrides);
}

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());
    Storage::fake('public');
    $this->employeeLookups = seedEmployeeLookups();
});

test('employee create persists farm assignment and stores image and signature', function () {
    $this->post(route('employee.store'), employeePayload($this->employeeLookups, [
        'employee_image' => UploadedFile::fake()->image('employee.jpg'),
        'employee_signature' => UploadedFile::fake()->image('signature.png'),
    ]))->assertRedirect(route('employee.index'));

    $employee = Employee::query()->where('cnic_no', '3520212345678')->firstOrFail();

    expect($employee->personal_farm_id)->toBe($this->employeeLookups['firstFarmId'])
        ->and($employee->father_cnic_no)->toBe('3520212345679')
        ->and($employee->addedby)->toBe(User::query()->firstOrFail()->id);
    Storage::disk('public')->assertExists('employee/'.$employee->employee_image);
    Storage::disk('public')->assertExists('employee/'.$employee->employee_signature);
});

test('employee update changes farm assignment and replaces image and signature', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups, [
        'employee_image' => 'old-employee.jpg',
        'employee_signature' => 'old-signature.png',
    ]));
    Storage::disk('public')->put('employee/old-employee.jpg', 'old-image');
    Storage::disk('public')->put('employee/old-signature.png', 'old-signature');

    $this->put(route('employee.update', $employee), employeePayload($this->employeeLookups, [
        'personal_farm_id' => $this->employeeLookups['secondFarmId'],
        'name' => 'Updated Worker',
        'father_cnic_no' => '3520212345680',
        'employee_image' => UploadedFile::fake()->image('new-employee.jpg'),
        'employee_signature' => UploadedFile::fake()->image('new-signature.png'),
    ]))->assertRedirect(route('employee.index'));

    $employee->refresh();
    expect($employee->name)->toBe('Updated Worker')
        ->and($employee->personal_farm_id)->toBe($this->employeeLookups['secondFarmId'])
        ->and($employee->father_cnic_no)->toBe('3520212345680')
        ->and($employee->employee_image)->not->toBe('old-employee.jpg')
        ->and($employee->employee_signature)->not->toBe('old-signature.png');
    Storage::disk('public')->assertMissing('employee/old-employee.jpg');
    Storage::disk('public')->assertMissing('employee/old-signature.png');
    Storage::disk('public')->assertExists('employee/'.$employee->employee_image);
    Storage::disk('public')->assertExists('employee/'.$employee->employee_signature);
});

test('employee destroy soft deletes the employee and preserves uploaded files', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups, [
        'employee_image' => 'employee.jpg',
        'employee_signature' => 'signature.png',
    ]));
    Storage::disk('public')->put('employee/employee.jpg', 'image');
    Storage::disk('public')->put('employee/signature.png', 'signature');

    $this->delete(route('employee.destroy', $employee))
        ->assertRedirect(route('employee.index'));

    $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    Storage::disk('public')->assertExists('employee/employee.jpg');
    Storage::disk('public')->assertExists('employee/signature.png');
});

test('employee schema includes persistent father cnic data', function () {
    expect(Schema::hasColumn('employees', 'father_cnic_no'))->toBeTrue();
});

test('employee create and edit forms render the personal farm selector with its selected value', function () {
    $this->get(route('employee.create'))
        ->assertOk()
        ->assertSee('name="personal_farm_id"', false)
        ->assertSee('First Farm');

    $employee = Employee::query()->create(employeePayload($this->employeeLookups));

    $this->get(route('employee.edit', $employee))
        ->assertOk()
        ->assertSee('name="personal_farm_id"', false)
        ->assertSee('value="'.$this->employeeLookups['firstFarmId'].'"', false)
        ->assertSee('selected', false);
});

test('employee listing and detail use the employee storage directory', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups, [
        'employee_image' => 'employee-photo.jpg',
    ]));

    $this->getJson(route('getEmployeeList'))
        ->assertOk()
        ->assertJsonPath('data.0.employee_image', fn (string $html): bool => str_contains(
            $html,
            asset('storage/employee/employee-photo.jpg'),
        ));

    $this->get(route('employee.show', $employee))
        ->assertStatus(201)
        ->assertJsonPath('success', 'yes')
        ->assertJsonPath('html_data', fn (string $html): bool => str_contains(
            $html,
            asset('storage/employee/employee-photo.jpg'),
        ));
});

test('employee routes preserve guest redirects and authenticated compatibility', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups));

    session()->flush();
    app('auth')->forgetGuards();
    foreach ([
        route('employee.index'),
        route('employee.create'),
        route('employee.edit', $employee),
        route('employee.show', $employee),
    ] as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }

    $this->actingAs(User::query()->firstOrFail());
    $this->get(route('employee.index'))->assertOk();
    $this->get(route('employee.create'))->assertOk();
    $this->get(route('employee.edit', $employee))->assertOk();
    $this->get(route('employee.show', $employee))->assertStatus(201);
});

test('employee store cleans up staged media when persistence fails', function () {
    $payload = employeePayload($this->employeeLookups, [
        'personal_farm_id' => 999999,
    ]);

    expect(fn () => app(StoreEmployeeAction::class)->execute(
        $payload,
        UploadedFile::fake()->image('employee.jpg'),
        UploadedFile::fake()->image('signature.png'),
        User::query()->firstOrFail()->id,
    ))->toThrow(QueryException::class);

    expect(Storage::disk('public')->allFiles('employee'))->toBe([]);
});

test('employee update preserves old media and removes staged media when persistence fails', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups, [
        'employee_image' => 'old-employee.jpg',
        'employee_signature' => 'old-signature.png',
    ]));
    Storage::disk('public')->put('employee/old-employee.jpg', 'old-image');
    Storage::disk('public')->put('employee/old-signature.png', 'old-signature');

    $payload = employeePayload($this->employeeLookups, [
        'personal_farm_id' => 999999,
    ]);

    expect(fn () => app(UpdateEmployeeAction::class)->execute(
        $employee,
        $payload,
        UploadedFile::fake()->image('new-employee.jpg'),
        UploadedFile::fake()->image('new-signature.png'),
        User::query()->firstOrFail()->id,
    ))->toThrow(QueryException::class);

    expect(Storage::disk('public')->allFiles('employee'))->toEqualCanonicalizing([
        'employee/old-employee.jpg',
        'employee/old-signature.png',
    ]);
});

test('employee update preserves existing media when a replacement upload is rejected', function () {
    $employee = Employee::query()->create(employeePayload($this->employeeLookups, [
        'employee_image' => 'old-employee.jpg',
        'employee_signature' => 'old-signature.png',
    ]));
    Storage::disk('public')->put('employee/old-employee.jpg', 'old-image');
    Storage::disk('public')->put('employee/old-signature.png', 'old-signature');

    expect(fn () => app(UpdateEmployeeAction::class)->execute(
        $employee,
        employeePayload($this->employeeLookups),
        UploadedFile::fake()->create('evil.php', 20),
        UploadedFile::fake()->image('new-signature.png'),
        User::query()->firstOrFail()->id,
    ))->toThrow(InvalidArgumentException::class);

    expect($employee->refresh()->employee_image)->toBe('old-employee.jpg')
        ->and($employee->employee_signature)->toBe('old-signature.png');
    expect(Storage::disk('public')->allFiles('employee'))->toEqualCanonicalizing([
        'employee/old-employee.jpg',
        'employee/old-signature.png',
    ]);
});
