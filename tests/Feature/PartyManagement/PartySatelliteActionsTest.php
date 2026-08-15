<?php

use App\Models\Broker;
use App\Models\City;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\Party;
use App\Models\Province;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());

    $countryId = Country::query()->insertGetId(['name' => 'Pakistan', 'slug' => 'pakistan']);
    $provinceId = Province::query()->insertGetId(['name' => 'Punjab', 'country_id' => $countryId]);
    $cityId = City::query()->insertGetId(['name' => 'Lahore', 'province_id' => $provinceId]);

    $this->geo = compact('countryId', 'provinceId', 'cityId');
});

function personPayload(array $geo, array $overrides = []): array
{
    return array_merge([
        'name' => 'Ali Khan',
        'guardian_name' => 'Ahmed Khan',
        'cnic_no' => '3520212345671',
        'email' => 'ali@example.com',
        'contact_number' => '03001234567',
        'country_id' => $geo['countryId'],
        'province_id' => $geo['provinceId'],
        'city_id' => $geo['cityId'],
        'address' => 'Lahore',
    ], $overrides);
}

test('conduct person store update and destroy via actions', function () {
    $this->withoutExceptionHandling();

    $this->post(route('conductpersons.store'), personPayload($this->geo))
        ->assertRedirect(route('conductpersons.index'));

    $person = ConductPerson::firstOrFail();
    expect($person->name)->toBe('Ali Khan');
    expect($person->contact_number)->toBe('03001234567');

    $this->put(route('conductpersons.update', $person->id), personPayload($this->geo, [
        'name' => 'Ali Updated',
        'cnic_no' => '3520212345671',
    ]))->assertRedirect(route('conductpersons.index'));

    expect($person->fresh()->name)->toBe('Ali Updated');

    $this->delete(route('conductpersons.destroy', $person->id))
        ->assertRedirect(route('conductpersons.index'));

    expect(ConductPerson::find($person->id))->toBeNull();
});

test('broker store update and destroy via actions with contact_no column', function () {
    $this->withoutExceptionHandling();

    $this->post(route('brokers.store'), personPayload($this->geo, [
        'cnic_no' => '3520212345672',
        'email' => 'broker@example.com',
    ]))->assertRedirect(route('brokers.index'));

    $broker = Broker::firstOrFail();
    expect($broker->contact_no)->toBe('03001234567');

    $this->put(route('brokers.update', $broker->id), personPayload($this->geo, [
        'name' => 'Broker Updated',
        'cnic_no' => '3520212345672',
        'email' => 'broker@example.com',
    ]))->assertRedirect(route('brokers.index'));

    expect($broker->fresh()->name)->toBe('Broker Updated');
    expect($broker->fresh()->contact_no)->toBe('03001234567');

    $this->delete(route('brokers.destroy', $broker->id))
        ->assertRedirect(route('brokers.index'));

    expect(Broker::find($broker->id))->toBeNull();
});

test('customer destroy soft-deletes party', function () {
    $this->withoutExceptionHandling();

    $party = Party::create([
        'is_customer' => 1,
        'name' => 'Quick Customer',
        'guardian_name' => 'Guardian',
        'cnic_no' => '3520212345678',
        'email' => 'quick@example.com',
        'contact_no' => '03001112233',
        'address' => 'Multan',
        'manual_number' => 'Q-1',
        'addedby' => User::query()->value('id'),
    ]);

    $this->delete(route('customers.destroy', $party->id))
        ->assertRedirect(route('customers.index'));

    expect(Party::find($party->id))->toBeNull();
});

test('lookup type store creates allowed table row', function () {
    $this->withoutExceptionHandling();

    DB::table('customer_types')->delete();

    $this->post(route('addalltypes'), [
        'tag_name' => 'customer_types',
        'name' => 'Wholesale',
    ])
        ->assertStatus(201)
        ->assertJson([
            'success' => 'yes',
            'data' => ['name' => 'Wholesale'],
        ]);

    expect(DB::table('customer_types')->where('name', 'Wholesale')->exists())->toBeTrue();
});
