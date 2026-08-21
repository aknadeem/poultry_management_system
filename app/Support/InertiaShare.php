<?php

namespace App\Support;

use App\Models\Party;
use App\Models\PartyBalance;
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
            'inertia.parties.index' => route('inertia.parties.index', [], false),
            'inertia.parties.create' => route('inertia.parties.create', [], false),
            'inertia.parties.store' => route('inertia.parties.store', [], false),
            'inertia.parties.show' => route('inertia.parties.show', ['party' => '__id__'], false),
            'inertia.parties.edit' => route('inertia.parties.edit', ['party' => '__id__'], false),
            'inertia.parties.update' => route('inertia.parties.update', ['party' => '__id__'], false),
            'inertia.parties.destroy' => route('inertia.parties.destroy', ['party' => '__id__'], false),
            'inertia.customers.index' => route('inertia.customers.index', [], false),
            'inertia.customers.create' => route('inertia.customers.create', [], false),
            'inertia.customers.store' => route('inertia.customers.store', [], false),
            'inertia.customers.show' => route('inertia.customers.show', ['customer' => '__id__'], false),
            'inertia.customers.edit' => route('inertia.customers.edit', ['customer' => '__id__'], false),
            'inertia.customers.update' => route('inertia.customers.update', ['customer' => '__id__'], false),
            'inertia.customers.destroy' => route('inertia.customers.destroy', ['customer' => '__id__'], false),
            'inertia.vendors.index' => route('inertia.vendors.index', [], false),
            'inertia.vendors.create' => route('inertia.vendors.create', [], false),
            'inertia.vendors.store' => route('inertia.vendors.store', [], false),
            'inertia.vendors.show' => route('inertia.vendors.show', ['vendor' => '__id__'], false),
            'inertia.vendors.edit' => route('inertia.vendors.edit', ['vendor' => '__id__'], false),
            'inertia.vendors.update' => route('inertia.vendors.update', ['vendor' => '__id__'], false),
            'inertia.vendors.destroy' => route('inertia.vendors.destroy', ['vendor' => '__id__'], false),
            'inertia.conduct-persons.index' => route('inertia.conduct-persons.index', [], false),
            'inertia.conduct-persons.create' => route('inertia.conduct-persons.create', [], false),
            'inertia.conduct-persons.store' => route('inertia.conduct-persons.store', [], false),
            'inertia.conduct-persons.show' => route('inertia.conduct-persons.show', ['conductperson' => '__id__'], false),
            'inertia.conduct-persons.edit' => route('inertia.conduct-persons.edit', ['conductperson' => '__id__'], false),
            'inertia.conduct-persons.update' => route('inertia.conduct-persons.update', ['conductperson' => '__id__'], false),
            'inertia.conduct-persons.destroy' => route('inertia.conduct-persons.destroy', ['conductperson' => '__id__'], false),
            'inertia.brokers.index' => route('inertia.brokers.index', [], false),
            'inertia.brokers.create' => route('inertia.brokers.create', [], false),
            'inertia.brokers.store' => route('inertia.brokers.store', [], false),
            'inertia.brokers.show' => route('inertia.brokers.show', ['broker' => '__id__'], false),
            'inertia.brokers.edit' => route('inertia.brokers.edit', ['broker' => '__id__'], false),
            'inertia.brokers.update' => route('inertia.brokers.update', ['broker' => '__id__'], false),
            'inertia.brokers.destroy' => route('inertia.brokers.destroy', ['broker' => '__id__'], false),
            'inertia.party-balances.index' => route('inertia.party-balances.index', [], false),
            'inertia.party-balances.show' => route('inertia.party-balances.show', ['partybalance' => '__id__'], false),
            'inertia.lookup-types.store' => route('inertia.lookup-types.store', [], false),
            'inertia.party-accounts.store' => route('inertia.party-accounts.store', [], false),
            'inertia.party-accounts.destroy' => route('inertia.party-accounts.destroy', ['partyaccount' => '__id__'], false),
            'inertia.party-documents.store' => route('inertia.party-documents.store', [], false),
            'inertia.party-documents.destroy' => route('inertia.party-documents.destroy', ['partydocument' => '__id__'], false),
            'inertia.party-balance-limits.store' => route('inertia.party-balance-limits.store', [], false),
            'inertia.party-balance-limits.destroy' => route('inertia.party-balance-limits.destroy', ['balancelimit' => '__id__'], false),
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
                'parties' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'partyBalances' => [
                    'viewAny' => false,
                    'view' => false,
                ],
            ];
        }

        $userProbe = new User;
        $partyProbe = new Party;

        return [
            'users' => [
                'viewAny' => $user->can('viewAny', User::class),
                'view' => $user->can('viewAny', User::class),
                'create' => $user->can('create', User::class),
                'update' => $user->can('update', $userProbe),
                'delete' => $user->can('delete', $userProbe),
            ],
            'parties' => [
                'viewAny' => $user->can('viewAny', Party::class),
                'view' => $user->can('viewAny', Party::class),
                'create' => $user->can('create', Party::class),
                'update' => $user->can('update', $partyProbe),
                'delete' => $user->can('delete', $partyProbe),
            ],
            'partyBalances' => [
                'viewAny' => $user->can('viewAny', PartyBalance::class),
                'view' => $user->can('viewAny', PartyBalance::class),
            ],
        ];
    }
}
