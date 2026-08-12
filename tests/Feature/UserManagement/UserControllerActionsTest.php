<?php

use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());
});

it('creates a user via the actions store endpoint', function () {
    $roleId = UserRole::query()->value('id');

    $this->post(route('users.store'), [
        'user_id_modal' => 0,
        'name' => 'Demo Operator',
        'email' => 'operator@example.com',
        'user_role_id' => $roleId,
        'password' => '1234',
        'contact_no' => '03001234567',
    ])
        ->assertOk()
        ->assertJson([
            'success' => 'yes',
            'message' => 'New User created successfully!',
        ]);

    $user = User::where('email', 'operator@example.com')->first();
    expect($user)->not->toBeNull();
    expect(Hash::check('1234', $user->password))->toBeTrue();
});

it('updates a user via user_id_modal', function () {
    $roleId = UserRole::query()->value('id');
    $user = User::factory()->create([
        'user_role_id' => $roleId,
        'password' => Hash::make('oldpass'),
    ]);

    $this->post(route('users.store'), [
        'user_id_modal' => $user->id,
        'name' => 'Updated Name',
        'email' => $user->email,
        'user_role_id' => $roleId,
        'contact_no' => '03009999999',
    ])
        ->assertOk()
        ->assertJson([
            'success' => 'yes',
            'message' => 'Data Updated successfully!',
        ]);

    expect($user->fresh()->name)->toBe('Updated Name');
    expect($user->fresh()->contact_no)->toBe('03009999999');
    expect(Hash::check('oldpass', $user->fresh()->password))->toBeTrue();
});

it('deletes a user through DestroyUserAction', function () {
    $roleId = UserRole::query()->value('id');
    $user = User::factory()->create([
        'user_role_id' => $roleId,
        'password' => Hash::make('1234'),
    ]);

    $this->delete(route('users.destroy', $user->id))
        ->assertRedirect(route('users.index'));

    expect(User::find($user->id))->toBeNull();
});
