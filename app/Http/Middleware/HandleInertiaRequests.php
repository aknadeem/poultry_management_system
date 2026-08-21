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
                'brokerBalance' => route('brokerbalance.index'),
                'customerFarms' => route('inertia.customer-farms.index'),
                'personalFarms' => route('inertia.personal-farms.index'),
                'productStores' => route('productstores.index'),
                'employees' => route('inertia.employees.index'),
                'products' => route('inertia.products.index'),
                'productPurchases' => route('inertia.product-purchases.index'),
                'productSales' => route('productsales.index'),
                'vaccination' => route('inertia.vaccinations.index'),
                'companies' => route('company.index'),
                'companyBalance' => route('companybalance.index'),
                'expenses' => route('expense.index'),
                'chickSales' => route('sale.index'),
                'chickPurchases' => route('purchase.index'),
                'feed' => route('feed.index'),
                'payables' => route('payables.index'),
                'chickSaleReport' => route('chickreport.index'),
                'chickPurchaseReport' => route('chickreport.purchases'),
                'productPurchaseReport' => route('productreport.purchase'),
                'productSaleReport' => route('productreport.sale'),
                'productReport' => route('productreport.index'),
            ],
        ];
    }
}
