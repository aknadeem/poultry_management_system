<?php

use App\Models\Employee;
use App\Models\Party;
use App\Models\PartyFarm;
use App\Models\PersonalFarm;
use App\Models\VaccinationSchedule;
use App\Queries\CustomerFarmQuery;
use App\Queries\EmployeeQuery;
use App\Queries\PersonalFarmQuery;
use App\Queries\VaccinationScheduleQuery;
use App\Support\FarmLookups;
use App\Support\FarmPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $timestamps = [
        'created_at' => '2026-08-01 00:00:00',
        'updated_at' => '2026-08-01 00:00:00',
    ];

    $this->farmTypeId = DB::table('farm_types')->insertGetId([
        'name' => 'Layer',
        'slug' => 'layer',
        ...$timestamps,
    ]);
    $this->farmSubtypeId = DB::table('farm_subtypes')->insertGetId([
        'name' => 'Commercial',
        'slug' => 'commercial',
        ...$timestamps,
    ]);
    $this->employeeTypeId = DB::table('employee_types')->insertGetId([
        'name' => 'Worker',
        'slug' => 'worker',
        ...$timestamps,
    ]);
    $this->employeeLevelId = DB::table('employee_levels')->insertGetId([
        'name' => 'Senior',
        'slug' => 'senior',
        ...$timestamps,
    ]);

    DB::table('countries')->insert([
        'id' => 1,
        'name' => 'Pakistan',
        'slug' => 'pakistan',
    ]);
    DB::table('provinces')->insert([
        'id' => 1,
        'name' => 'Punjab',
        'country_id' => 1,
    ]);
    DB::table('cities')->insert([
        'id' => 1,
        'name' => 'Lahore',
        'province_id' => 1,
    ]);

    $this->personalFarm = PersonalFarm::query()->create([
        'farm_type_id' => $this->farmTypeId,
        'farm_subtype_id' => $this->farmSubtypeId,
        'farm_name' => 'Alpha Personal Farm',
        'farm_noc' => 'PF-NOC',
        'farm_image' => 'personal.jpg',
        'farm_capacity' => 500,
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
    ]);

    $party = Party::query()->create([
        'is_customer' => 1,
        'is_vendor' => 0,
        'name' => 'Alpha Customer',
        'guardian_name' => 'Guardian',
        'cnic_no' => '3520212345678',
        'contact_no' => '03001234567',
        'manual_number' => 'MN-100',
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
    ]);
    $this->customerFarm = PartyFarm::query()->create([
        'party_id' => $party->id,
        'farm_type_id' => $this->farmTypeId,
        'farm_subtype_id' => $this->farmSubtypeId,
        'farm_name' => 'Alpha Customer Farm',
        'farm_noc' => 'CF-NOC',
        'farm_image' => 'customer.jpg',
        'farm_capacity' => 750,
    ]);

    $this->employee = Employee::query()->create([
        'personal_farm_id' => $this->personalFarm->id,
        'employee_type_id' => $this->employeeTypeId,
        'employee_level_id' => $this->employeeLevelId,
        'name' => 'Alpha Employee',
        'cnic_no' => '3520212345680',
        'contact_no' => '03007654321',
        'employee_image' => 'employee.jpg',
        'employee_signature' => 'signature.jpg',
        'country_id' => 1,
        'province_id' => 1,
        'city_id' => 1,
    ]);

    $this->productId = DB::table('products')->insertGetId([
        'product_number' => 1,
        'product_code' => 'VAC-001',
        'bar_code' => 'VAC-BAR-001',
        'product_name' => 'Alpha Vaccine',
        ...$timestamps,
    ]);
    $this->schedule = VaccinationSchedule::query()->create([
        'party_farm_id' => $this->customerFarm->id,
        'product_id' => $this->productId,
        'schedule_date' => '2026-08-22',
        'description' => 'Alpha schedule',
    ]);
});

test('personal farm query paginates searched data with allow-listed filters', function () {
    $query = new PersonalFarmQuery(Request::create('/app', 'GET', [
        'search' => 'Alpha',
        'sort' => 'unsafe_column',
        'direction' => 'asc',
        'per_page' => 999,
    ]));

    expect($query->paginate()->total())->toBe(1)
        ->and($query->paginate()->perPage())->toBe(10)
        ->and($query->filters())->toMatchArray([
            'search' => 'Alpha',
            'sort' => 'id',
            'direction' => 'asc',
        ]);
});

test('customer farm query is customer-scoped and allow-listed', function () {
    $query = new CustomerFarmQuery(Request::create('/app', 'GET', [
        'search' => 'Alpha Customer',
        'sort' => 'unsafe_column',
    ]));

    expect($query->paginate()->total())->toBe(1)
        ->and($query->paginate()->first()->id)->toBe($this->customerFarm->id)
        ->and($query->filters()['sort'])->toBe('id');
});

test('employee query searches farm employees and allow-lists pagination', function () {
    $query = new EmployeeQuery(Request::create('/app', 'GET', [
        'search' => 'Alpha Employee',
        'sort' => 'unsafe_column',
        'per_page' => 25,
    ]));

    expect($query->paginate()->total())->toBe(1)
        ->and($query->paginate()->perPage())->toBe(25)
        ->and($query->filters()['sort'])->toBe('id');
});

test('vaccination schedule query searches relations and allow-lists sorting', function () {
    $query = new VaccinationScheduleQuery(Request::create('/app', 'GET', [
        'search' => 'Alpha Vaccine',
        'sort' => 'unsafe_column',
    ]));

    expect($query->paginate()->total())->toBe(1)
        ->and($query->paginate()->first()->id)->toBe($this->schedule->id)
        ->and($query->filters()['sort'])->toBe('id');
});

test('farm presenter returns stable media and relationship payloads', function () {
    expect(FarmPresenter::personalFarm($this->personalFarm))
        ->toMatchArray([
            'id' => $this->personalFarm->id,
            'farm_name' => 'Alpha Personal Farm',
            'farm_image_url' => asset('storage/personalfarms/personal.jpg'),
            'farm_type_name' => 'Layer',
        ])
        ->and(FarmPresenter::customerFarm($this->customerFarm))
        ->toMatchArray([
            'id' => $this->customerFarm->id,
            'party_name' => 'Alpha Customer',
            'farm_image_url' => asset('storage/party/farm/customer.jpg'),
        ])
        ->and(FarmPresenter::employee($this->employee))
        ->toMatchArray([
            'id' => $this->employee->id,
            'farm_name' => 'Alpha Personal Farm',
            'employee_image_url' => asset('storage/employee/employee.jpg'),
        ])
        ->and(FarmPresenter::vaccinationSchedule($this->schedule))
        ->toMatchArray([
            'id' => $this->schedule->id,
            'farm_name' => 'Alpha Customer Farm',
            'product_name' => 'Alpha Vaccine',
        ]);
});

test('farm lookups expose the shared form options', function () {
    $options = FarmLookups::formOptions();

    expect($options)->toHaveKeys([
        'countries',
        'farmTypes',
        'farmSubtypes',
        'employeeTypes',
        'employeeLevels',
        'personalFarms',
        'customerFarms',
        'products',
    ]);
});
