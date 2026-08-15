<?php

use App\Models\User;
use App\Models\UserRole;
use App\Models\ProductSale;
use App\Models\ChickenSale;

beforeEach(function () {
    // Seed user roles
    UserRole::create(['id' => 1, 'name' => 'Super Admin', 'slug' => 'super-admin']);
    UserRole::create(['id' => 2, 'name' => 'Admin', 'slug' => 'admin']);
    UserRole::create(['id' => 3, 'name' => 'HOD', 'slug' => 'hod']);
});

test('super admin can view and create resources', function () {
    $superAdmin = User::factory()->create(['user_role_id' => 1]);

    $this->actingAs($superAdmin);

    // Verify view permissions
    expect($superAdmin->can('viewAny', ProductSale::class))->toBeTrue();
    expect($superAdmin->can('viewAny', ChickenSale::class))->toBeTrue();
    expect($superAdmin->can('create', ProductSale::class))->toBeTrue();
    expect($superAdmin->can('create', ChickenSale::class))->toBeTrue();
});

test('hod can view resources but cannot create or delete', function () {
    $hod = User::factory()->create(['user_role_id' => 3]);

    $this->actingAs($hod);

    // Read permissions allowed
    expect($hod->can('viewAny', ProductSale::class))->toBeTrue();
    expect($hod->can('viewAny', ChickenSale::class))->toBeTrue();

    // Mutation permissions denied
    expect($hod->can('create', ProductSale::class))->toBeFalse();
    expect($hod->can('create', ChickenSale::class))->toBeFalse();
    
    $sale = new ProductSale();
    expect($hod->can('update', $sale))->toBeFalse();
    expect($hod->can('delete', $sale))->toBeFalse();
});

test('user policy restricts user management to super admin only', function () {
    $superAdmin = User::factory()->create(['user_role_id' => 1]);
    $admin = User::factory()->create(['user_role_id' => 2]);
    $hod = User::factory()->create(['user_role_id' => 3]);

    // Super Admin check
    $this->actingAs($superAdmin);
    expect($superAdmin->can('viewAny', User::class))->toBeTrue();

    // Admin check (UserPolicy blocks admin as well from User management access)
    $this->actingAs($admin);
    expect($admin->can('viewAny', User::class))->toBeFalse();

    // HOD check
    $this->actingAs($hod);
    expect($hod->can('viewAny', User::class))->toBeFalse();
});
