<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\ChickenSale::class => \App\Policies\ChickenSalePolicy::class,
        \App\Models\ChickenPurchase::class => \App\Policies\ChickenPurchasePolicy::class,
        \App\Models\ChickPurchase::class => \App\Policies\ChickPurchasePolicy::class,
        \App\Models\ProductSale::class => \App\Policies\ProductSalePolicy::class,
        \App\Models\ProductPurchase::class => \App\Policies\ProductPurchasePolicy::class,
        \App\Models\Party::class => \App\Policies\PartyPolicy::class,
        \App\Models\PartyCompany::class => \App\Policies\PartyCompanyPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\Feed::class => \App\Policies\FeedPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\AccountPayable::class => \App\Policies\AccountPayablePolicy::class,
        \App\Models\PartyBalance::class => \App\Policies\PartyBalancePolicy::class,
        \App\Models\CompanyBalance::class => \App\Policies\CompanyBalancePolicy::class,
        \App\Models\BrokerBalance::class => \App\Policies\BrokerBalancePolicy::class,
        \App\Models\VaccinationSchedule::class => \App\Policies\VaccinationSchedulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
