<?php

namespace App\Http\Controllers\Inertia\UserManagement;

use App\Actions\UserManagement\DestroyUserAction;
use App\Actions\UserManagement\StoreUserAction;
use App\Actions\UserManagement\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\StoreUserRequest;
use App\Models\User;
use App\Models\UserRole;
use App\Queries\UserQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(UserQuery $query): Response
    {
        $this->authorize('viewAny', User::class);

            $users = $query->paginate();

        return Inertia::render('Users/Index', [
            'users' => $users->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'role' => $user->userRole?->name,
            ]),
            'filters' => $query->filters(),
            'roles' => UserRole::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'roles' => UserRole::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request, StoreUserAction $action): RedirectResponse
    {
        $this->authorize('create', User::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.users.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'New User created successfully!',
            ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);
        $user->load('userRole:id,name,slug');

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'role' => $user->userRole?->name,
                'picture' => $user->picture,
            ],
        ]);
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'user_role_id' => $user->user_role_id,
            ],
            'roles' => UserRole::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(StoreUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $this->authorize('update', $user);

        $action->execute(
            $user,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.users.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data Updated successfully!',
            ]);
    }

    public function destroy(User $user, DestroyUserAction $action): RedirectResponse
    {
        $this->authorize('delete', $user);
        $action->execute($user);

        return redirect()
            ->route('inertia.users.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
