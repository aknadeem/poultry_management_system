# Laravel 13 Page Compatibility Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore all real user-facing pages and AJAX endpoints after the Laravel 13 upgrade and protect Pest tests with an isolated SQLite database.

**Architecture:** Repair and validate the route table first, then harden the test environment, restore the missing Laravel 13 DataTables integration, and add explicit HTTP smoke suites for public, authenticated, parameterized, and AJAX routes. Tests run in-process through Laravel and use deterministic SQLite fixtures; they never crawl or mutate the live application.

**Tech Stack:** Laravel 13.24, PHP 8.4, Pest 5, PHPUnit 13, SQLite `:memory:`, Yajra DataTables 13, Laravel Mix 6.

## Global Constraints

- Tests must use SQLite `:memory:` and must abort if another database is active.
- Cover real linked pages and AJAX endpoints; remove unused invalid routes instead of inventing controller behavior.
- Do not invoke POST, PUT, PATCH, or DELETE routes from page smoke tests.
- Do not add browser automation.
- Preserve production HTTPS forcing and existing business behavior.

---

### Task 1: Repair and validate the route table

**Files:**
- Modify: `routes/web.php`
- Create: `tests/Feature/Routing/RouteIntegrityTest.php`

**Interfaces:**
- Produces unique route names, controller-backed routes with existing methods, and a parameterless `chickreport.purchases` page URL.

- [ ] **Step 1: Write failing route-integrity tests**

```php
<?php

use Illuminate\Support\Facades\Route;

it('has unique named routes', function () {
    $names = collect(Route::getRoutes())
        ->map(fn ($route) => $route->getName())
        ->filter();

    expect($names->duplicates()->values()->all())->toBe([]);
});

it('only registers existing controller methods', function () {
    $missing = collect(Route::getRoutes())
        ->map(fn ($route) => $route->getActionName())
        ->filter(fn ($action) => str_contains($action, '@'))
        ->reject(function ($action) {
            [$controller, $method] = explode('@', $action);
            return method_exists($controller, $method);
        })
        ->values()
        ->all();

    expect($missing)->toBe([]);
});

it('generates the chick purchase report page without dates', function () {
    expect(route('chickreport.purchases', absolute: false))
        ->toBe('/reportmanagement/chick-purchases');
});
```

- [ ] **Step 2: Run the test and verify duplicate names, missing methods, and URL generation fail**

Run: `php artisan test tests/Feature/Routing/RouteIntegrityTest.php`

- [ ] **Step 3: Repair route definitions**

In `routes/web.php`:

```php
Route::get('/chick-purchase-report/{from_date}/{to_date}', [
    ChickReportController::class,
    'makePurchaseReport',
])->name('chickreport.purchase.data');

Route::get('/chick-purchases', [
    ChickReportController::class,
    'purchase_index',
])->name('chickreport.purchases');

Route::resource('chickreport', ChickReportController::class)->only(['index']);
```

Remove the duplicate `storelist` declaration. Add `only([...])` or `except([...])` to every resource whose generated actions are absent, preserving only methods that reflection confirms exist. This includes `users`, `userlevel`, `parties`, `conductpersons`, `partydocuments`, `partyaccounts`, `balancelimits`, `brokers`, `brokerbalance`, `partybalance`, `productsales`, and `payables`.

- [ ] **Step 4: Clear route caches and verify**

Run:

```bash
php artisan route:clear
php artisan test tests/Feature/Routing/RouteIntegrityTest.php
php artisan route:list
```

Expected: route-integrity tests pass and route listing completes without duplicate names or missing action methods.

---

### Task 2: Enforce isolated SQLite Pest execution

**Files:**
- Modify: `phpunit.xml`
- Modify: `.env.testing`
- Modify: `tests/TestCase.php`
- Modify: `tests/Pest.php`
- Modify: `tests/Feature/UpgradeTest.php`
- Modify: `database/migrations/2021_08_20_221410_create_customer_types_table.php`
- Create: `tests/Feature/Testing/DatabaseIsolationTest.php`

**Interfaces:**
- Produces a test bootstrap that permits only SQLite `:memory:` and automatically refreshes the database for every Feature test.

- [ ] **Step 1: Write the isolation test**

```php
<?php

it('uses only an in-memory sqlite database', function () {
    $connection = config('database.default');

    expect(config("database.connections.{$connection}.driver"))->toBe('sqlite')
        ->and(config("database.connections.{$connection}.database"))->toBe(':memory:');
});
```

- [ ] **Step 2: Harden environment configuration**

Add to both `phpunit.xml` and `.env.testing`:

```text
DATABASE_URL=
DB_FOREIGN_KEYS=true
```

Keep `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:`.

- [ ] **Step 3: Add the fail-closed test bootstrap**

In `tests/TestCase.php`, after `parent::setUp()`:

```php
$connection = config('database.default');
$driver = config("database.connections.{$connection}.driver");
$database = config("database.connections.{$connection}.database");

if ($driver !== 'sqlite' || $database !== ':memory:') {
    $this->fail(
        "Feature tests require sqlite/:memory:, got [{$connection}/{$driver}/{$database}]."
    );
}
```

Apply `RefreshDatabase` globally in `tests/Pest.php`:

```php
uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');
```

Remove the duplicate file-level `uses(RefreshDatabase::class)` declaration from `UpgradeTest.php`.

- [ ] **Step 4: Correct the migration typo**

Change `->nullabe()` to `->nullable()` in the customer types migration.

- [ ] **Step 5: Verify migrations, seeding, and isolation**

Run:

```bash
php artisan migrate:fresh --seed --env=testing
php artisan test tests/Feature/Testing/DatabaseIsolationTest.php
php artisan test
```

Expected: migrations and seeders complete on SQLite and all tests pass.

---

### Task 3: Restore Laravel 13 DataTables support

**Files:**
- Modify: `composer.json`
- Modify: `composer.lock`
- Inspect/modify only if necessary: `config/app.php`
- Create: `tests/Feature/Ajax/DataTablesEndpointsTest.php`

**Interfaces:**
- Consumes existing controller calls to the `DataTables` facade.
- Produces Laravel 13-compatible DataTables JSON through `yajra/laravel-datatables-oracle:^13.1`.

- [ ] **Step 1: Add a failing facade/integration test**

```php
<?php

it('loads the DataTables facade', function () {
    expect(class_exists(Yajra\DataTables\Facades\DataTables::class))->toBeTrue();
});
```

- [ ] **Step 2: Install the documented Laravel 13 package**

Run:

```bash
composer require yajra/laravel-datatables-oracle:^13.1
php artisan package:discover
```

Use package auto-discovery. Do not manually register a provider or alias unless runtime verification proves the legacy controllers need it.

- [ ] **Step 3: Add authenticated JSON smoke datasets**

Cover the existing list/report endpoints, including:

```php
dataset('datatable endpoints', [
    'employees' => ['getEmployeeList', []],
    'companies' => ['getCompaniesList', []],
    'company balances' => ['getCompaniesBalanceList', []],
    'users' => ['getUsersList', []],
    'user levels' => ['getUserLevelList', []],
    'party balances' => ['getBalanceList', []],
    'broker balances' => ['getbrokersBalanceList', []],
    'vaccinations' => ['getScheduleList', []],
    'feed' => ['getfeedlist', []],
    'expenses' => ['getExpenseList', []],
    'chick purchases' => ['getpurchaselist', []],
    'chick sales' => ['getSalesList', []],
    'chick sale report' => ['chickreport.sale', ['2026-01-01', '2026-12-31']],
    'chick purchase report' => ['chickreport.purchase.data', ['2026-01-01', '2026-12-31']],
    'products report' => ['productreport', ['2026-01-01', '2026-12-31']],
    'product purchases report' => ['productreportpurchase', ['2026-01-01', '2026-12-31']],
    'product sales report' => ['productreportsale', ['2026-01-01', '2026-12-31']],
]);
```

Authenticate a seeded user, request each route with `X-Requested-With: XMLHttpRequest`, assert success and JSON, then fix Laravel 13 API incompatibilities revealed by each endpoint one at a time.

- [ ] **Step 4: Verify AJAX coverage**

Run: `php artisan test tests/Feature/Ajax/DataTablesEndpointsTest.php`

Expected: all cataloged endpoints return successful JSON responses.

---

### Task 4: Cover public and authenticated application pages

**Files:**
- Create: `tests/Support/SmokeTestUser.php`
- Create: `tests/Feature/Smoke/PublicPagesTest.php`
- Create: `tests/Feature/Smoke/AuthenticatedPagesTest.php`

**Interfaces:**
- Produces deterministic authentication and explicit page catalogs.

- [ ] **Step 1: Add strict public-page tests**

Cover `login`, `register`, and `password.request`; assert HTTP 200 and HTML content. Assert `/` redirects to `login` for guests. Replace the loose existing home assertion that currently accepts 200, 301, 302, or 404.

- [ ] **Step 2: Add an authenticated page dataset**

Seed lookup data and authenticate the test admin. Cover the page routes linked by the primary navigation:

```php
dataset('authenticated pages', [
    'dashboard' => ['home'],
    'parties' => ['parties.index'],
    'customers' => ['customers.index'],
    'vendors' => ['vendors.index'],
    'conduct persons' => ['conductpersons.index'],
    'brokers' => ['brokers.index'],
    'party balances' => ['partybalance.index'],
    'broker balances' => ['brokerbalance.index'],
    'customer farms' => ['customerfarms.index'],
    'product stores' => ['productstores.index'],
    'employees' => ['employee.index'],
    'products' => ['products.index'],
    'product purchases' => ['productpurchases.index'],
    'product sales' => ['productsales.index'],
    'vaccinations' => ['vaccination.index'],
    'companies' => ['company.index'],
    'company balances' => ['companybalance.index'],
    'expenses' => ['expense.index'],
    'chick sales' => ['sale.index'],
    'chick purchases' => ['purchase.index'],
    'feed' => ['feed.index'],
    'payables' => ['payables.index'],
    'chick sale reports' => ['chickreport.index'],
    'chick purchase reports' => ['chickreport.purchases'],
    'product purchase reports' => ['productreport.purchase'],
    'product sale reports' => ['productreport.sale'],
    'product reports' => ['productreport.index'],
    'users' => ['users.index'],
    'user levels' => ['userlevel.index'],
]);
```

Assert each response is HTTP 200, HTML, and does not contain exception-renderer markers. Fix each discovered Blade/controller incompatibility independently.

- [ ] **Step 3: Verify page smoke coverage**

Run:

```bash
php artisan test tests/Feature/Smoke/PublicPagesTest.php
php artisan test tests/Feature/Smoke/AuthenticatedPagesTest.php
```

---

### Task 5: Cover linked parameterized pages and complete verification

**Files:**
- Create: `database/seeders/SmokeTestSeeder.php`
- Create: `tests/Feature/Smoke/ParameterizedPagesTest.php`
- Modify controllers/views only where a failing test proves a Laravel 13 incompatibility.

**Interfaces:**
- Produces one minimal valid fixture graph for linked show/edit/invoice pages.

- [ ] **Step 1: Build deterministic SQLite fixtures**

Create one valid record for each required lookup and transaction model: user level, user, country, province, city, division, customer/vendor party, company, product category, product, product purchase, product sale, chick purchase, chicken sale, employee, feed, party balance, and payable. Use existing model fillable fields and migrations as the source of truth.

- [ ] **Step 2: Add parameterized page tests**

Cover only GET pages linked from current Blade templates. Generate URLs with named routes and real fixture IDs. Include resource show/edit pages and purchase/sale invoice pages. Do not invoke report/AJAX endpoints here; they are covered in Task 3.

- [ ] **Step 3: Run focused tests and repair proven failures**

Run: `php artisan test tests/Feature/Smoke/ParameterizedPagesTest.php`

For each failure, add a focused regression assertion before changing application code. Avoid unrelated refactoring.

- [ ] **Step 4: Run complete verification**

Run:

```bash
php artisan route:clear
php artisan config:clear
php artisan migrate:fresh --seed --env=testing
php artisan test
npm run dev
npm run production
git diff --check
```

Expected: all commands exit successfully, all real page/AJAX catalogs pass, and generated frontend artifacts are removed from the working tree after verification if they are untracked.
