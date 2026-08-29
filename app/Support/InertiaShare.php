<?php

namespace App\Support;

use App\Models\AccountPayable;
use App\Models\BrokerBalance;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\CompanyBalance;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\Party;
use App\Models\PartyBalance;
use App\Models\PartyCompany;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductSale;
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
            'inertia.party-balances.store' => route('inertia.party-balances.store', [], false),
            'inertia.lookup-types.store' => route('inertia.lookup-types.store', [], false),
            'inertia.party-accounts.store' => route('inertia.party-accounts.store', [], false),
            'inertia.party-accounts.destroy' => route('inertia.party-accounts.destroy', ['partyaccount' => '__id__'], false),
            'inertia.party-documents.store' => route('inertia.party-documents.store', [], false),
            'inertia.party-documents.destroy' => route('inertia.party-documents.destroy', ['partydocument' => '__id__'], false),
            'inertia.party-balance-limits.store' => route('inertia.party-balance-limits.store', [], false),
            'inertia.party-balance-limits.destroy' => route('inertia.party-balance-limits.destroy', ['balancelimit' => '__id__'], false),
            'inertia.personal-farms.index' => route('inertia.personal-farms.index', [], false),
            'inertia.personal-farms.create' => route('inertia.personal-farms.create', [], false),
            'inertia.personal-farms.store' => route('inertia.personal-farms.store', [], false),
            'inertia.personal-farms.edit' => route('inertia.personal-farms.edit', ['personalFarm' => '__id__'], false),
            'inertia.personal-farms.update' => route('inertia.personal-farms.update', ['personalFarm' => '__id__'], false),
            'inertia.personal-farms.destroy' => route('inertia.personal-farms.destroy', ['personalFarm' => '__id__'], false),
            'inertia.customer-farms.index' => route('inertia.customer-farms.index', [], false),
            'inertia.customer-farms.update' => route('inertia.customer-farms.update', ['customerFarm' => '__id__'], false),
            'inertia.customer-farms.destroy' => route('inertia.customer-farms.destroy', ['customerFarm' => '__id__'], false),
            'inertia.employees.index' => route('inertia.employees.index', [], false),
            'inertia.employees.create' => route('inertia.employees.create', [], false),
            'inertia.employees.store' => route('inertia.employees.store', [], false),
            'inertia.employees.show' => route('inertia.employees.show', ['employee' => '__id__'], false),
            'inertia.employees.edit' => route('inertia.employees.edit', ['employee' => '__id__'], false),
            'inertia.employees.update' => route('inertia.employees.update', ['employee' => '__id__'], false),
            'inertia.employees.destroy' => route('inertia.employees.destroy', ['employee' => '__id__'], false),
            'inertia.vaccinations.index' => route('inertia.vaccinations.index', [], false),
            'inertia.vaccinations.store' => route('inertia.vaccinations.store', [], false),
            'inertia.vaccinations.record' => route('inertia.vaccinations.record', [], false),
            'inertia.vaccinations.toggle-status' => route('inertia.vaccinations.toggle-status', ['vaccination' => '__id__'], false),
            'inertia.farm-lookup-types.store' => route('inertia.farm-lookup-types.store', [], false),
            'inertia.products.index' => route('inertia.products.index', [], false),
            'inertia.products.create' => route('inertia.products.create', [], false),
            'inertia.products.store' => route('inertia.products.store', [], false),
            'inertia.products.show' => route('inertia.products.show', ['product' => '__id__'], false),
            'inertia.products.edit' => route('inertia.products.edit', ['product' => '__id__'], false),
            'inertia.products.update' => route('inertia.products.update', ['product' => '__id__'], false),
            'inertia.products.destroy' => route('inertia.products.destroy', ['product' => '__id__'], false),
            'inertia.products.toggle-status' => route('inertia.products.toggle-status', ['product' => '__id__'], false),
            'inertia.product-stores.index' => route('inertia.product-stores.index', [], false),
            'inertia.product-stores.create' => route('inertia.product-stores.create', [], false),
            'inertia.product-stores.store' => route('inertia.product-stores.store', [], false),
            'inertia.product-stores.show' => route('inertia.product-stores.show', ['product_store' => '__id__'], false),
            'inertia.product-stores.edit' => route('inertia.product-stores.edit', ['product_store' => '__id__'], false),
            'inertia.product-stores.update' => route('inertia.product-stores.update', ['product_store' => '__id__'], false),
            'inertia.product-stores.destroy' => route('inertia.product-stores.destroy', ['product_store' => '__id__'], false),
            'inertia.product-stores.toggle-status' => route('inertia.product-stores.toggle-status', ['productStore' => '__id__'], false),
            'inertia.product-purchases.index' => route('inertia.product-purchases.index', [], false),
            'inertia.product-purchases.create' => route('inertia.product-purchases.create', [], false),
            'inertia.product-purchases.store' => route('inertia.product-purchases.store', [], false),
            'inertia.product-purchases.show' => route('inertia.product-purchases.show', ['productPurchase' => '__id__'], false),
            'inertia.product-purchases.destroy' => route('inertia.product-purchases.destroy', ['productPurchase' => '__id__'], false),
            'inertia.product-purchases.rebates' => route('inertia.product-purchases.rebates', [], false),
            'inertia.product-purchases.rebate' => route('inertia.product-purchases.rebate', [], false),
            'inertia.product-purchases.toggle-status' => route('inertia.product-purchases.toggle-status', ['productPurchase' => '__id__'], false),
            'inertia.product-purchases.invoice' => route('inertia.product-purchases.invoice', ['productPurchase' => '__id__'], false),
            'productpurchases.invoice' => route('productpurchases.invoice', ['id' => '__id__'], false),
            'inertia.product-sales.index' => route('inertia.product-sales.index', [], false),
            'inertia.product-sales.create' => route('inertia.product-sales.create', [], false),
            'inertia.product-sales.store' => route('inertia.product-sales.store', [], false),
            'inertia.product-sales.show' => route('inertia.product-sales.show', ['productSale' => '__id__'], false),
            'inertia.product-sales.destroy' => route('inertia.product-sales.destroy', ['productSale' => '__id__'], false),
            'inertia.product-sales.rebates' => route('inertia.product-sales.rebates', [], false),
            'inertia.product-sales.rebate' => route('inertia.product-sales.rebate', [], false),
            'inertia.product-sales.toggle-status' => route('inertia.product-sales.toggle-status', ['productSale' => '__id__'], false),
            'inertia.product-sales.invoice' => route('inertia.product-sales.invoice', ['productSale' => '__id__'], false),
            'productsales.invoice' => route('productsales.invoice', ['id' => '__id__'], false),
            'productfilter' => url('/ProductManagement/productfilter'),
            'inertia.chick-sales.index' => route('inertia.chick-sales.index', [], false),
            'inertia.chick-sales.create' => route('inertia.chick-sales.create', [], false),
            'inertia.chick-sales.store' => route('inertia.chick-sales.store', [], false),
            'inertia.chick-sales.show' => route('inertia.chick-sales.show', ['chick_sale' => '__id__'], false),
            'inertia.chick-sales.edit' => route('inertia.chick-sales.edit', ['chick_sale' => '__id__'], false),
            'inertia.chick-sales.update' => route('inertia.chick-sales.update', ['chick_sale' => '__id__'], false),
            'inertia.chick-sales.destroy' => route('inertia.chick-sales.destroy', ['chick_sale' => '__id__'], false),
            'inertia.chick-purchases.index' => route('inertia.chick-purchases.index', [], false),
            'inertia.chick-purchases.create' => route('inertia.chick-purchases.create', [], false),
            'inertia.chick-purchases.store' => route('inertia.chick-purchases.store', [], false),
            'inertia.chick-purchases.show' => route('inertia.chick-purchases.show', ['chick_purchase' => '__id__'], false),
            'inertia.chick-purchases.edit' => route('inertia.chick-purchases.edit', ['chick_purchase' => '__id__'], false),
            'inertia.chick-purchases.update' => route('inertia.chick-purchases.update', ['chick_purchase' => '__id__'], false),
            'inertia.chick-purchases.destroy' => route('inertia.chick-purchases.destroy', ['chick_purchase' => '__id__'], false),
            'inertia.feeds.index' => route('inertia.feeds.index', [], false),
            'inertia.feeds.create' => route('inertia.feeds.create', [], false),
            'inertia.feeds.store' => route('inertia.feeds.store', [], false),
            'inertia.feeds.show' => route('inertia.feeds.show', ['feed' => '__id__'], false),
            'inertia.feeds.update' => route('inertia.feeds.update', ['feed' => '__id__'], false),
            'inertia.feeds.destroy' => route('inertia.feeds.destroy', ['feed' => '__id__'], false),
            'inertia.expenses.index' => route('inertia.expenses.index', [], false),
            'inertia.expenses.create' => route('inertia.expenses.create', [], false),
            'inertia.expenses.store' => route('inertia.expenses.store', [], false),
            'inertia.expenses.categories.store' => route('inertia.expenses.categories.store', [], false),
            'inertia.expenses.show' => route('inertia.expenses.show', ['expense' => '__id__'], false),
            'inertia.expenses.edit' => route('inertia.expenses.edit', ['expense' => '__id__'], false),
            'inertia.expenses.update' => route('inertia.expenses.update', ['expense' => '__id__'], false),
            'inertia.expenses.destroy' => route('inertia.expenses.destroy', ['expense' => '__id__'], false),
            'inertia.companies.index' => route('inertia.companies.index', [], false),
            'inertia.companies.create' => route('inertia.companies.create', [], false),
            'inertia.companies.store' => route('inertia.companies.store', [], false),
            'inertia.companies.show' => route('inertia.companies.show', ['company' => '__id__'], false),
            'inertia.companies.edit' => route('inertia.companies.edit', ['company' => '__id__'], false),
            'inertia.companies.update' => route('inertia.companies.update', ['company' => '__id__'], false),
            'inertia.companies.destroy' => route('inertia.companies.destroy', ['company' => '__id__'], false),
            'inertia.companies.toggle-status' => route('inertia.companies.toggle-status', ['company' => '__id__'], false),
            'inertia.company-balances.index' => route('inertia.company-balances.index', [], false),
            'inertia.company-balances.store' => route('inertia.company-balances.store', [], false),
            'inertia.company-balances.show' => route('inertia.company-balances.show', ['companyBalance' => '__id__'], false),
            'inertia.broker-balances.index' => route('inertia.broker-balances.index', [], false),
            'inertia.payables.index' => route('inertia.payables.index', [], false),
            'inertia.reports.chick-sale' => route('inertia.reports.chick-sale', [], false),
            'inertia.reports.chick-purchase' => route('inertia.reports.chick-purchase', [], false),
            'inertia.reports.product' => route('inertia.reports.product', [], false),
            'inertia.reports.product-purchase' => route('inertia.reports.product-purchase', [], false),
            'inertia.reports.product-sale' => route('inertia.reports.product-sale', [], false),
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
                    'create' => false,
                ],
                'brokerBalances' => [
                    'viewAny' => false,
                    'view' => false,
                ],
                'payables' => [
                    'viewAny' => false,
                    'view' => false,
                ],
                'products' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'productPurchases' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'productSales' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'chickSales' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'chickPurchases' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'feeds' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'expenses' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'companies' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ],
                'companyBalances' => [
                    'viewAny' => false,
                    'view' => false,
                    'create' => false,
                ],
                'brokerBalances' => [
                    'viewAny' => false,
                    'view' => false,
                ],
                'payables' => [
                    'viewAny' => false,
                    'view' => false,
                ],
            ];
        }

        $userProbe = new User;
        $partyProbe = new Party;
        $productProbe = new Product;
        $purchaseProbe = new ProductPurchase;
        $productSaleProbe = new ProductSale;
        $saleProbe = new ChickenSale;
        $chickPurchaseProbe = new ChickPurchase;
        $feedProbe = new Feed;
        $expenseProbe = new Expense;

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
                'create' => $user->can('create', PartyBalance::class),
            ],
            'products' => [
                'viewAny' => $user->can('viewAny', Product::class),
                'view' => $user->can('viewAny', Product::class),
                'create' => $user->can('create', Product::class),
                'update' => $user->can('update', $productProbe),
                'delete' => $user->can('delete', $productProbe),
            ],
            'productPurchases' => [
                'viewAny' => $user->can('viewAny', ProductPurchase::class),
                'view' => $user->can('viewAny', ProductPurchase::class),
                'create' => $user->can('create', ProductPurchase::class),
                'update' => $user->can('update', $purchaseProbe),
                'delete' => $user->can('delete', $purchaseProbe),
            ],
            'productSales' => [
                'viewAny' => $user->can('viewAny', ProductSale::class),
                'view' => $user->can('viewAny', ProductSale::class),
                'create' => $user->can('create', ProductSale::class),
                'update' => $user->can('update', $productSaleProbe),
                'delete' => $user->can('delete', $productSaleProbe),
            ],
            'chickSales' => [
                'viewAny' => $user->can('viewAny', ChickenSale::class),
                'view' => $user->can('viewAny', ChickenSale::class),
                'create' => $user->can('create', ChickenSale::class),
                'update' => $user->can('update', $saleProbe),
                'delete' => $user->can('delete', $saleProbe),
            ],
            'chickPurchases' => [
                'viewAny' => $user->can('viewAny', ChickPurchase::class),
                'view' => $user->can('viewAny', ChickPurchase::class),
                'create' => $user->can('create', ChickPurchase::class),
                'update' => $user->can('update', $chickPurchaseProbe),
                'delete' => $user->can('delete', $chickPurchaseProbe),
            ],
            'feeds' => [
                'viewAny' => $user->can('viewAny', Feed::class),
                'view' => $user->can('viewAny', Feed::class),
                'create' => $user->can('create', Feed::class),
                'update' => $user->can('update', $feedProbe),
                'delete' => $user->can('delete', $feedProbe),
            ],
            'expenses' => [
                'viewAny' => $user->can('viewAny', Expense::class),
                'view' => $user->can('viewAny', Expense::class),
                'create' => $user->can('create', Expense::class),
                'update' => $user->can('update', $expenseProbe),
                'delete' => $user->can('delete', $expenseProbe),
            ],
            'companies' => [
                'viewAny' => $user->can('viewAny', PartyCompany::class),
                'view' => $user->can('viewAny', PartyCompany::class),
                'create' => $user->can('create', PartyCompany::class),
                'update' => $user->can('update', new PartyCompany),
                'delete' => $user->can('delete', new PartyCompany),
            ],
            'companyBalances' => [
                'viewAny' => $user->can('viewAny', CompanyBalance::class),
                'view' => $user->can('viewAny', CompanyBalance::class),
                'create' => $user->can('create', CompanyBalance::class),
            ],
            'brokerBalances' => [
                'viewAny' => $user->can('viewAny', BrokerBalance::class),
                'view' => $user->can('viewAny', BrokerBalance::class),
            ],
            'payables' => [
                'viewAny' => $user->can('viewAny', AccountPayable::class),
                'view' => $user->can('viewAny', AccountPayable::class),
            ],
        ];
    }
}
