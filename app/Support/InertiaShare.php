<?php

namespace App\Support;

use App\Models\User;

class InertiaShare
{
    /**
     * Named Inertia routes with `__id__` placeholders for parameterized URLs.
     *
     * @return array<string, string>
     */
    public static function routes(): array
    {
        return [
            'inertia.login' => route('inertia.login', [], false),
            'inertia.register' => route('inertia.register', [], false),
            'inertia.password.request' => route('inertia.password.request', [], false),
            'inertia.password.email' => route('inertia.password.email', [], false),
            'inertia.password.reset' => route('inertia.password.reset', ['token' => '__token__'], false),
            'inertia.password.update' => route('inertia.password.update', [], false),
            'inertia.password.confirm' => route('inertia.password.confirm', [], false),
            'inertia.dashboard' => route('inertia.dashboard', [], false),
            'inertia.logout' => route('inertia.logout', [], false),
            'inertia.users.index' => route('inertia.users.index', [], false),
            'inertia.users.create' => route('inertia.users.create', [], false),
            'inertia.users.store' => route('inertia.users.store', [], false),
            'inertia.users.show' => route('inertia.users.show', ['user' => '__id__'], false),
            'inertia.users.edit' => route('inertia.users.edit', ['user' => '__id__'], false),
            'inertia.users.update' => route('inertia.users.update', ['user' => '__id__'], false),
            'inertia.users.destroy' => route('inertia.users.destroy', ['user' => '__id__'], false),
            'inertia.user-roles.index' => route('inertia.user-roles.index', [], false),
        ];
    }

    /**
     * @return array<string, bool>
     */
    public static function abilities(?User $user): array
    {
        if ($user === null) {
            return [
                'users' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
            ];
        }

        $probe = new User;

        return [
            'users' => [
                'viewAny' => $user->can('viewAny', User::class),
                'view' => $user->can('viewAny', User::class),
                'create' => $user->can('create', User::class),
                'update' => $user->can('update', $probe),
                'delete' => $user->can('delete', $probe),
            ],
        ];
    }
}
