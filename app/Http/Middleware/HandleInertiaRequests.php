<?php

namespace App\Http\Middleware;

use App\Support\InertiaShare;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user !== null) {
            $user->loadMissing('userRole');
        }

        $notification = $request->session()->get('swal_notification');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user === null ? null : [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->userRole?->slug,
                ],
            ],
            'flash' => [
                'title' => is_array($notification) ? ($notification['title'] ?? null) : null,
                'message' => is_array($notification) ? ($notification['message'] ?? null) : null,
                'type' => is_array($notification) ? ($notification['icon_type'] ?? null) : null,
                'status' => $request->session()->get('status'),
                'createdLookup' => $request->session()->get('created_lookup'),
            ],
            'theme' => [
                'logo' => asset('assets/images/logo/poultryLogo.png'),
                'year' => now()->year,
                'appName' => config('app.name', 'Poultry Management System'),
            ],
            'can' => InertiaShare::abilities($user),
            'routes' => InertiaShare::routes(),
            'urls' => [
                'home' => url('/'),
                'dashboard' => route('inertia.dashboard'),
                'logout' => route('inertia.logout'),
                'users' => route('inertia.users.index'),
                'userRoles' => route('inertia.user-roles.index'),
                'parties' => route('inertia.parties.index'),
                'customers' => route('inertia.customers.index'),
                'vendors' => route('inertia.vendors.index'),
                'conductPersons' => route('inertia.conduct-persons.index'),
                'brokers' => route('inertia.brokers.index'),
                'partyBalance' => route('inertia.party-balances.index'),
                'brokerBalance' => route('inertia.broker-balances.index'),
                'customerFarms' => route('inertia.customer-farms.index'),
                'personalFarms' => route('inertia.personal-farms.index'),
                'productStores' => route('inertia.product-stores.index'),
                'employees' => route('inertia.employees.index'),
                'products' => route('inertia.products.index'),
                'productPurchases' => route('inertia.product-purchases.index'),
                'productSales' => route('inertia.product-sales.index'),
                'vaccination' => route('inertia.vaccinations.index'),
                'companies' => route('inertia.companies.index'),
                'companyBalance' => route('inertia.company-balances.index'),
                'expenses' => route('inertia.expenses.index'),
                'chickSales' => route('inertia.chick-sales.index'),
                'chickPurchases' => route('inertia.chick-purchases.index'),
                'feed' => route('inertia.feeds.index'),
                'payables' => route('inertia.payables.index'),
                'chickSaleReport' => route('inertia.reports.chick-sale'),
                'chickPurchaseReport' => route('inertia.reports.chick-purchase'),
                'productPurchaseReport' => route('inertia.reports.product-purchase'),
                'productSaleReport' => route('inertia.reports.product-sale'),
                'productReport' => route('inertia.reports.product'),
            ],
        ];
    }
}
