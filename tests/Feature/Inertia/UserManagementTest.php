<?php

use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
});

function inertiaSuperAdmin(): User
{
    return User::query()->where('email', 'admin@admin.com')->firstOrFail();
}

it('lists users in an inertia datatable for a super admin', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');
    User::factory()->create([
        'name' => 'Listed Operator',
        'email' => 'listed@example.com',
        'user_role_id' => $roleId,
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data')
            ->has('users.links')
            ->has('roles')
            ->has('filters')
            ->where('users.data.0.name', 'Listed Operator')
            ->where('can.users.viewAny', true)
            ->where('can.users.create', true)
            ->has('routes')
        );
});

it('searches users through the query allow-list', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');
    User::factory()->create([
        'name' => 'Alpha User',
        'email' => 'alpha@example.com',
        'user_role_id' => $roleId,
    ]);
    User::factory()->create([
        'name' => 'Beta User',
        'email' => 'beta@example.com',
        'user_role_id' => $roleId,
    ]);

    $this->actingAs($admin)
        ->get(route('inertia.users.index', ['search' => 'Alpha']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->where('users.data.0.name', 'Alpha User')
            ->where('users.total', 1)
        );
});

it('ignores unknown sort columns on the users list', function () {
    $admin = inertiaSuperAdmin();

    $this->actingAs($admin)
        ->get(route('inertia.users.index', ['sort' => 'drop table users', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->where('filters.sort', 'id')
        );
});

it('forbids the users list for a hod', function () {
    $hodRole = UserRole::query()->where('slug', 'hod')->firstOrFail();
    $hod = User::factory()->create(['user_role_id' => $hodRole->id]);

    $this->actingAs($hod)
        ->get(route('inertia.users.index'))
        ->assertForbidden();
});

it('creates a user through inertia using the existing action', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');

    $this->actingAs($admin)
        ->post(route('inertia.users.store'), [
            'name' => 'Inertia Operator',
            'email' => 'inertia-operator@example.com',
            'user_role_id' => $roleId,
            'password' => '1234',
            'contact_no' => '03001234567',
        ])
        ->assertRedirect(route('inertia.users.index'));

    $user = User::query()->where('email', 'inertia-operator@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Inertia Operator')
        ->and(Hash::check('1234', $user->password))->toBeTrue();
});

it('returns inertia validation errors instead of json 201', function () {
    $admin = inertiaSuperAdmin();

    $this->actingAs($admin)
        ->from(route('inertia.users.create'))
        ->withHeaders(['X-Inertia' => 'true'])
        ->post(route('inertia.users.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'user_role_id', 'password', 'contact_no']);
});

it('still returns json 201 validation errors for the blade user store', function () {
    $admin = inertiaSuperAdmin();

    $this->actingAs($admin)
        ->post(route('users.store'), [])
        ->assertStatus(201)
        ->assertJson(['success' => 'no']);
});

it('updates a user through inertia', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');
    $user = User::factory()->create([
        'user_role_id' => $roleId,
        'password' => Hash::make('oldpass'),
        'contact_no' => '03001111111',
    ]);

    $this->actingAs($admin)
        ->put(route('inertia.users.update', $user), [
            'name' => 'Updated Inertia User',
            'email' => $user->email,
            'user_role_id' => $roleId,
            'contact_no' => '03009999999',
        ])
        ->assertRedirect(route('inertia.users.index'));

    expect($user->fresh()->name)->toBe('Updated Inertia User')
        ->and($user->fresh()->contact_no)->toBe('03009999999')
        ->and(Hash::check('oldpass', $user->fresh()->password))->toBeTrue();
});

it('deletes a user through inertia using DestroyUserAction', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');
    $user = User::factory()->create([
        'user_role_id' => $roleId,
        'password' => Hash::make('1234'),
    ]);

    $this->actingAs($admin)
        ->delete(route('inertia.users.destroy', $user))
        ->assertRedirect(route('inertia.users.index'));

    expect(User::find($user->id))->toBeNull();
});

it('uses compact paginator links instead of one link per page', function () {
    $admin = inertiaSuperAdmin();
    $roleId = UserRole::query()->value('id');
    User::factory()->count(25)->create(['user_role_id' => $roleId]);

    $this->actingAs($admin)
        ->get(route('inertia.users.index', ['per_page' => 10]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->where('users.last_page', fn ($lastPage): bool => $lastPage >= 2)
            ->has('users.links')
            ->where('users.links', fn ($links): bool => count($links) < 20)
        );
});

it('lists user roles in an inertia datatable', function () {
    $admin = inertiaSuperAdmin();

    $this->actingAs($admin)
        ->get(route('inertia.user-roles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Roles/Index')
            ->has('roles.data')
            ->has('roles.links')
        );
});
