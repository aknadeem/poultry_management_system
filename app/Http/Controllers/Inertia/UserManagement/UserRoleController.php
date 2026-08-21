<?php

namespace App\Http\Controllers\Inertia\UserManagement;

use App\Http\Controllers\Controller;
use App\Queries\UserRoleQuery;
use App\Models\UserRole;
use Inertia\Inertia;
use Inertia\Response;

class UserRoleController extends Controller
{
    public function index(UserRoleQuery $query): Response
    {
        $roles = $query->paginate();

        return Inertia::render('Users/Roles/Index', [
            'roles' => $roles->through(fn (UserRole $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ]),
            'filters' => $query->filters(),
        ]);
    }
}
