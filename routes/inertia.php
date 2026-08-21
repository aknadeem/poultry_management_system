<?php

use App\Http\Controllers\Inertia\Auth\ConfirmPasswordController;
use App\Http\Controllers\Inertia\Auth\ForgotPasswordController;
use App\Http\Controllers\Inertia\Auth\LoginController;
use App\Http\Controllers\Inertia\Auth\RegisterController;
use App\Http\Controllers\Inertia\Auth\ResetPasswordController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\PartyManagement\BrokerController as InertiaBrokerController;
use App\Http\Controllers\Inertia\PartyManagement\ConductPersonController as InertiaConductPersonController;
use App\Http\Controllers\Inertia\PartyManagement\CustomerController as InertiaCustomerController;
use App\Http\Controllers\Inertia\PartyManagement\PartyBalanceController as InertiaPartyBalanceController;
use App\Http\Controllers\Inertia\PartyManagement\LookupTypeController as InertiaLookupTypeController;
use App\Http\Controllers\Inertia\PartyManagement\PartyAccountController as InertiaPartyAccountController;
use App\Http\Controllers\Inertia\PartyManagement\PartyBalanceLimitController as InertiaPartyBalanceLimitController;
use App\Http\Controllers\Inertia\PartyManagement\PartyController as InertiaPartyController;
use App\Http\Controllers\Inertia\PartyManagement\PartyDocumentController as InertiaPartyDocumentController;
use App\Http\Controllers\Inertia\PartyManagement\VendorController as InertiaVendorController;
use App\Http\Controllers\Inertia\FarmManagement\CustomerFarmController as InertiaCustomerFarmController;
use App\Http\Controllers\Inertia\FarmManagement\EmployeeController as InertiaFarmEmployeeController;
use App\Http\Controllers\Inertia\FarmManagement\LookupTypeController as InertiaFarmLookupTypeController;
use App\Http\Controllers\Inertia\FarmManagement\PersonalFarmController as InertiaPersonalFarmController;
use App\Http\Controllers\Inertia\FarmManagement\VaccinationController as InertiaVaccinationController;
use App\Http\Controllers\Inertia\ProductManagement\ProductController as InertiaProductController;
use App\Http\Controllers\Inertia\ProductManagement\ProductPurchaseController as InertiaProductPurchaseController;
use App\Http\Controllers\Inertia\InventoryManagement\ChickSaleController as InertiaChickSaleController;
use App\Http\Controllers\Inertia\InventoryManagement\ChickPurchaseController as InertiaChickPurchaseController;
use App\Http\Controllers\Inertia\InventoryManagement\FeedController as InertiaFeedController;
use App\Http\Controllers\Inertia\UserManagement\UserController as InertiaUserController;
use App\Http\Controllers\Inertia\UserManagement\UserRoleController as InertiaUserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('inertia.login');
        Route::post('login', [LoginController::class, 'login']);

        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('inertia.register');
        Route::post('register', [RegisterController::class, 'register']);

        Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('inertia.password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('inertia.password.email');

        Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('inertia.password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('inertia.password.update');
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('inertia.dashboard');
        });
        Route::get('dashboard', DashboardController::class)->name('inertia.dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('inertia.logout');

        Route::get('confirm-password', [ConfirmPasswordController::class, 'showConfirmForm'])->name('inertia.password.confirm');
        Route::post('confirm-password', [ConfirmPasswordController::class, 'confirm']);

        Route::prefix('usermanagement')->group(function (): void {
            Route::resource('users', InertiaUserController::class)->names([
                'index' => 'inertia.users.index',
                'create' => 'inertia.users.create',
                'store' => 'inertia.users.store',
                'show' => 'inertia.users.show',
                'edit' => 'inertia.users.edit',
                'update' => 'inertia.users.update',
                'destroy' => 'inertia.users.destroy',
            ]);
            Route::get('userrole', [InertiaUserRoleController::class, 'index'])->name('inertia.user-roles.index');
        });

        Route::prefix('partymanagement')->group(function (): void {
            Route::resource('parties', InertiaPartyController::class)->names([
                'index' => 'inertia.parties.index',
                'create' => 'inertia.parties.create',
                'store' => 'inertia.parties.store',
                'show' => 'inertia.parties.show',
                'edit' => 'inertia.parties.edit',
                'update' => 'inertia.parties.update',
                'destroy' => 'inertia.parties.destroy',
            ]);
            Route::resource('customers', InertiaCustomerController::class)->names([
                'index' => 'inertia.customers.index',
                'create' => 'inertia.customers.create',
                'store' => 'inertia.customers.store',
                'show' => 'inertia.customers.show',
                'edit' => 'inertia.customers.edit',
                'update' => 'inertia.customers.update',
                'destroy' => 'inertia.customers.destroy',
            ]);
            Route::resource('vendors', InertiaVendorController::class)->names([
                'index' => 'inertia.vendors.index',
                'create' => 'inertia.vendors.create',
                'store' => 'inertia.vendors.store',
                'show' => 'inertia.vendors.show',
                'edit' => 'inertia.vendors.edit',
                'update' => 'inertia.vendors.update',
                'destroy' => 'inertia.vendors.destroy',
            ]);
            Route::resource('conductpersons', InertiaConductPersonController::class)->names([
                'index' => 'inertia.conduct-persons.index',
                'create' => 'inertia.conduct-persons.create',
                'store' => 'inertia.conduct-persons.store',
                'show' => 'inertia.conduct-persons.show',
                'edit' => 'inertia.conduct-persons.edit',
                'update' => 'inertia.conduct-persons.update',
                'destroy' => 'inertia.conduct-persons.destroy',
            ]);
            Route::resource('brokers', InertiaBrokerController::class)->names([
                'index' => 'inertia.brokers.index',
                'create' => 'inertia.brokers.create',
                'store' => 'inertia.brokers.store',
                'show' => 'inertia.brokers.show',
                'edit' => 'inertia.brokers.edit',
                'update' => 'inertia.brokers.update',
                'destroy' => 'inertia.brokers.destroy',
            ]);
            Route::resource('partybalances', InertiaPartyBalanceController::class)->only(['index', 'show'])->names([
                'index' => 'inertia.party-balances.index',
                'show' => 'inertia.party-balances.show',
            ]);
            Route::post('lookup-types', [InertiaLookupTypeController::class, 'store'])->name('inertia.lookup-types.store');
            Route::post('partyaccounts', [InertiaPartyAccountController::class, 'store'])->name('inertia.party-accounts.store');
            Route::delete('partyaccounts/{partyaccount}', [InertiaPartyAccountController::class, 'destroy'])->name('inertia.party-accounts.destroy');
            Route::post('partydocuments', [InertiaPartyDocumentController::class, 'store'])->name('inertia.party-documents.store');
            Route::delete('partydocuments/{partydocument}', [InertiaPartyDocumentController::class, 'destroy'])->name('inertia.party-documents.destroy');
            Route::post('balancelimits', [InertiaPartyBalanceLimitController::class, 'store'])->name('inertia.party-balance-limits.store');
            Route::delete('balancelimits/{balancelimit}', [InertiaPartyBalanceLimitController::class, 'destroy'])->name('inertia.party-balance-limits.destroy');
        });

        Route::prefix('farmmanagement')->group(function (): void {
            Route::resource('personal-farms', InertiaPersonalFarmController::class)
                ->except(['show'])
                ->parameters(['personal-farms' => 'personalFarm'])
                ->names([
                    'index' => 'inertia.personal-farms.index',
                    'create' => 'inertia.personal-farms.create',
                    'store' => 'inertia.personal-farms.store',
                    'edit' => 'inertia.personal-farms.edit',
                    'update' => 'inertia.personal-farms.update',
                    'destroy' => 'inertia.personal-farms.destroy',
                ]);
            Route::resource('customer-farms', InertiaCustomerFarmController::class)
                ->only(['index', 'update', 'destroy'])
                ->parameters(['customer-farms' => 'customerFarm'])
                ->names([
                    'index' => 'inertia.customer-farms.index',
                    'update' => 'inertia.customer-farms.update',
                    'destroy' => 'inertia.customer-farms.destroy',
                ]);
            Route::resource('employees', InertiaFarmEmployeeController::class)->names([
                'index' => 'inertia.employees.index',
                'create' => 'inertia.employees.create',
                'store' => 'inertia.employees.store',
                'show' => 'inertia.employees.show',
                'edit' => 'inertia.employees.edit',
                'update' => 'inertia.employees.update',
                'destroy' => 'inertia.employees.destroy',
            ]);
            Route::get('vaccinations', [InertiaVaccinationController::class, 'index'])->name('inertia.vaccinations.index');
            Route::post('vaccinations', [InertiaVaccinationController::class, 'store'])->name('inertia.vaccinations.store');
            Route::post('vaccinations/record', [InertiaVaccinationController::class, 'record'])->name('inertia.vaccinations.record');
            Route::put('vaccinations/{vaccination}/status', [InertiaVaccinationController::class, 'toggleStatus'])->name('inertia.vaccinations.toggle-status');
            Route::post('lookup-types', [InertiaFarmLookupTypeController::class, 'store'])->name('inertia.farm-lookup-types.store');
        });

        Route::prefix('productmanagement')->group(function (): void {
            Route::resource('products', InertiaProductController::class)->names([
                'index' => 'inertia.products.index',
                'create' => 'inertia.products.create',
                'store' => 'inertia.products.store',
                'show' => 'inertia.products.show',
                'edit' => 'inertia.products.edit',
                'update' => 'inertia.products.update',
                'destroy' => 'inertia.products.destroy',
            ]);
            Route::put('products/{product}/status', [InertiaProductController::class, 'toggleStatus'])
                ->name('inertia.products.toggle-status');

            Route::get('product-purchases', [InertiaProductPurchaseController::class, 'index'])->name('inertia.product-purchases.index');
            Route::get('product-purchases/create', [InertiaProductPurchaseController::class, 'create'])->name('inertia.product-purchases.create');
            Route::post('product-purchases', [InertiaProductPurchaseController::class, 'store'])->name('inertia.product-purchases.store');
            Route::get('product-purchases/rebates', [InertiaProductPurchaseController::class, 'rebates'])->name('inertia.product-purchases.rebates');
            Route::post('product-purchases/rebate', [InertiaProductPurchaseController::class, 'rebate'])->name('inertia.product-purchases.rebate');
            Route::get('product-purchases/{productPurchase}', [InertiaProductPurchaseController::class, 'show'])->name('inertia.product-purchases.show');
            Route::delete('product-purchases/{productPurchase}', [InertiaProductPurchaseController::class, 'destroy'])->name('inertia.product-purchases.destroy');
            Route::put('product-purchases/{productPurchase}/status', [InertiaProductPurchaseController::class, 'toggleStatus'])
                ->name('inertia.product-purchases.toggle-status');
        });

        Route::prefix('inventory')->group(function (): void {
            Route::resource('chick-sales', InertiaChickSaleController::class)->names([
                'index' => 'inertia.chick-sales.index',
                'create' => 'inertia.chick-sales.create',
                'store' => 'inertia.chick-sales.store',
                'show' => 'inertia.chick-sales.show',
                'edit' => 'inertia.chick-sales.edit',
                'update' => 'inertia.chick-sales.update',
                'destroy' => 'inertia.chick-sales.destroy',
            ]);

            Route::resource('chick-purchases', InertiaChickPurchaseController::class)->names([
                'index' => 'inertia.chick-purchases.index',
                'create' => 'inertia.chick-purchases.create',
                'store' => 'inertia.chick-purchases.store',
                'show' => 'inertia.chick-purchases.show',
                'edit' => 'inertia.chick-purchases.edit',
                'update' => 'inertia.chick-purchases.update',
                'destroy' => 'inertia.chick-purchases.destroy',
            ]);

            Route::get('feeds', [InertiaFeedController::class, 'index'])->name('inertia.feeds.index');
            Route::get('feeds/create', [InertiaFeedController::class, 'create'])->name('inertia.feeds.create');
            Route::post('feeds', [InertiaFeedController::class, 'store'])->name('inertia.feeds.store');
            Route::get('feeds/{feed}', [InertiaFeedController::class, 'show'])->name('inertia.feeds.show');
            Route::put('feeds/{feed}', [InertiaFeedController::class, 'update'])->name('inertia.feeds.update');
            Route::delete('feeds/{feed}', [InertiaFeedController::class, 'destroy'])->name('inertia.feeds.destroy');
        });
    });
});
