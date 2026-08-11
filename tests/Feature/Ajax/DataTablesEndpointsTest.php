<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());

    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    DB::table('business_types')->insert([
        'id' => 1,
        'name' => 'Supplier',
        'slug' => 'supplier',
        ...$timestamps,
    ]);
    DB::table('parties')->insert([
        'id' => 1,
        'is_vendor' => true,
        'is_customer' => true,
        'name' => 'Fixture Party',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        'company_address' => 'Fixture Address',
        ...$timestamps,
    ]);
    DB::table('employees')->insert([
        'id' => 1,
        'emp_code' => 'EMP-1',
        'name' => 'Fixture Employee',
        'email' => 'employee@example.test',
        'contact_no' => '03001111111',
        'address' => 'Fixture Employee Address',
        'employee_image' => 'employee.jpg',
        'net_salary' => 0,
        ...$timestamps,
    ]);
    DB::table('company_balances')->insert([
        'id' => 1,
        'company_id' => 1,
        'type' => 'chick_purchase',
        'total_amount' => 100,
        'remaining_amount' => 100,
        ...$timestamps,
    ]);
    DB::table('party_balances')->insert([
        'id' => 1,
        'party_id' => 1,
        'transaction_date' => '2026-06-01',
        'total_amount' => 100,
        'paid_amount' => 0,
        'remaining_amount' => 100,
        'narration' => 'Fixture balance',
        ...$timestamps,
    ]);
    DB::table('brokers')->insert([
        'id' => 1,
        'broker_code' => 'BROKER-1',
        'name' => 'Fixture Broker',
        ...$timestamps,
    ]);
    DB::table('broker_balances')->insert([
        'id' => 1,
        'broker_id' => 1,
        'total_amount' => 100,
        'paid_amount' => 0,
        'remaining_amount' => 100,
        'narration' => 'Fixture broker balance',
        ...$timestamps,
    ]);
    DB::table('expense_categories')->insert([
        'id' => 1,
        'name' => 'Fixture Expense',
        ...$timestamps,
    ]);
    DB::table('expenses')->insert([
        'id' => 1,
        'expense_code' => 'EXP-1',
        'category_id' => 1,
        'amount' => 10,
        'expense_date' => '2026-06-01',
        ...$timestamps,
    ]);
    DB::table('feed_categories')->insert([
        'id' => 1,
        'name' => 'Starter Feed',
        'slug' => 'starter-feed',
        ...$timestamps,
    ]);
    DB::table('feeds')->insert([
        'id' => 1,
        'feed_name' => 'Fixture Feed',
        'feed_code' => 'FEED-1',
        'feed_category_id' => 1,
        'total_quantity' => 100,
        'remaining_quantity' => 75,
        ...$timestamps,
    ]);
    DB::table('product_categories')->insert([
        'id' => 1,
        'name' => 'Fixture Product Category',
        'slug' => 'fixture-product-category',
        'company_id' => 1,
        ...$timestamps,
    ]);
    DB::table('products')->insert([
        'id' => 1,
        'product_code' => 'PRODUCT-1',
        'product_group' => 1,
        'product_name' => 'Fixture Product',
        'party_company_id' => 1,
        'product_category_id' => 1,
        'quantity' => 10,
        ...$timestamps,
    ]);
    DB::table('party_farms')->insert([
        'id' => 1,
        'party_id' => 1,
        'farm_code' => 'FARM-1',
        'farm_name' => 'Fixture Farm',
        ...$timestamps,
    ]);
    DB::table('vaccination_schedules')->insert([
        'id' => 1,
        'party_farm_id' => 1,
        'product_id' => 1,
        'schedule_date' => '2026-06-01',
        'vaccination_date' => '2026-06-02',
        'description' => 'Fixture vaccination',
        ...$timestamps,
    ]);
    DB::table('chick_purchases')->insert([
        'id' => 1,
        'purchase_code' => 'CHICK-PURCHASE-1',
        'purchase_date' => '2026-06-01',
        'company_id' => 1,
        'customer_id' => 1,
        'quantity' => 10,
        'price' => 10,
        'discount_amount' => 5,
        'total_price' => 100,
        ...$timestamps,
    ]);
    DB::table('chicken_sales')->insert([
        'id' => 1,
        'sale_code' => 'CHICK-SALE-1',
        'sale_date' => '2026-06-01',
        'customer_id' => 1,
        'party_id' => 1,
        'quantity' => 10,
        'per_kg_price' => 20,
        'total_weight' => 5,
        'discount_amount' => 5,
        'total_price' => 100,
        ...$timestamps,
    ]);
    DB::table('product_purchases')->insert([
        'id' => 1,
        'purchase_code' => 'PRODUCT-PURCHASE-1',
        'product_category_id' => 1,
        'party_company_id' => 1,
        'purchase_date' => '2026-06-01',
        'total_amount' => 100,
        'discount_amount' => 5,
        'final_amount' => 95,
        ...$timestamps,
    ]);
    DB::table('product_sales')->insert([
        'id' => 1,
        'sale_code' => 'PRODUCT-SALE-1',
        'party_id' => 1,
        'product_category_id' => 1,
        'party_company_id' => 1,
        'sale_date' => '2026-06-01',
        'total_amount' => 100,
        'discount_amount' => 5,
        'final_amount' => 95,
        ...$timestamps,
    ]);
});

it('loads the DataTables facade', function () {
    expect(class_exists(Yajra\DataTables\Facades\DataTables::class))->toBeTrue();
});

it('allows broker balance narration to be null', function () {
    $narration = collect(Schema::getColumns('broker_balances'))
        ->firstWhere('name', 'narration');

    expect($narration['nullable'])->toBeTrue();

    DB::table('broker_balances')->insert([
        'broker_id' => 1,
        'narration' => null,
        'created_at' => '2026-06-02 00:00:00',
        'updated_at' => '2026-06-02 00:00:00',
    ]);

    expect(DB::table('broker_balances')->whereNull('narration')->exists())->toBeTrue();
});

dataset('datatable endpoints', [
    'employees' => [
        'getEmployeeList',
        [],
        ['DT_RowIndex', 'employee_image', 'name', 'email', 'contact_no', 'address', 'Actions'],
        'data.0.name',
        'Fixture Employee',
    ],
    'company balances' => [
        'getCompaniesBalanceList',
        [],
        ['DT_RowIndex', 'type', 'company_id', 'total_amount', 'paid_amount', 'remaining_amount', 'status', 'created_at', 'Action'],
        'data.0.company_id',
        '<span> Fixture Company </span>',
    ],
    'users' => [
        'getUsersList',
        [],
        ['DT_RowIndex', 'name', 'user_level_id', 'email', 'Actions'],
        'data.0.user_level_id',
        '<span>Super Admin</span>',
    ],
    'party balances' => [
        'getBalanceList',
        [],
        ['DT_RowIndex', 'party_id', 'total_amount', 'paid_amount', 'remaining_amount', 'narration', 'created_at', 'Action'],
        'data.0.party_id',
        '<span> Fixture Party </span>',
    ],
    'broker balances' => [
        'getbrokersBalanceList',
        [],
        ['DT_RowIndex', 'narration', 'broker_id', 'total_amount', 'paid_amount', 'remaining_amount', 'status', 'created_at', 'Action'],
        'data.0.narration',
        'Fixture broker balance',
    ],
    'vaccinations' => [
        'getScheduleList',
        [],
        ['DT_RowIndex', 'party_farm_id', 'product_id', 'schedule_date', 'is_vaccinated', 'description', 'is_active', 'Actions'],
        'data.0.party_farm_id',
        '<span class="fs-5">Fixture Farm</span>',
    ],
    'expenses' => [
        'getExpenseList',
        [],
        ['DT_RowIndex', 'picture', 'category_id', 'expense_date', 'amount', 'remarks', 'Actions'],
        'data.0.category_id',
        '<span>Fixture Expense</span>',
    ],
    'chick purchases' => [
        'getpurchaselist',
        [],
        ['DT_RowIndex', 'picture', 'company_id', 'purchase_date', 'price', 'quantity', 'discount_amount', 'total_price', 'Actions'],
        'data.0.company_id',
        '<span> Fixture Company </span>',
    ],
    'chick sales' => [
        'getSalesList',
        [],
        ['DT_RowIndex', 'picture', 'customer_id', 'sale_date', 'per_kg_price', 'total_weight', 'discount_amount', 'total_price', 'Actions'],
        'data.0.customer_id',
        '<span> Fixture Party </span>',
    ],
    'chick sale report' => [
        'chickreport.sale',
        ['2026-01-01', '2026-12-31'],
        ['DT_RowIndex', 'DateFrom', 'DateTo', 'Customer', 'party_id', 'manual_number', 'sale_date', 'per_kg_price', 'total_weight', 'discount_amount', 'total_price'],
        'data.0.Customer',
        '<span> Fixture Party <br> <b> 1000000000000</b> </span>',
    ],
    'chick purchase report' => [
        'chickreport.purchase.data',
        ['2026-01-01', '2026-12-31'],
        ['DT_RowIndex', 'DateFrom', 'DateTo', 'company_id', 'purchase_date', 'price', 'quantity', 'discount_amount', 'total_price'],
        'data.0.company_id',
        '<span> Fixture Company </span>',
    ],
    'products report' => [
        'productreport',
        ['2026-01-01', '2026-12-31'],
        ['DT_RowIndex', 'DateFrom', 'DateTo', 'party_company_id', 'product_code', 'ProductGroup', 'product_name', 'quantity'],
        'data.0.ProductGroup',
        '<span> Feed</span>',
    ],
    'product purchases report' => [
        'productreportpurchase',
        ['2026-01-01', '2026-12-31'],
        ['DT_RowIndex', 'DateFrom', 'DateTo', 'party_company_id', 'purchase_code', 'purchase_date', 'total_price', 'discount_amount', 'final_price'],
        'data.0.purchase_code',
        'PRODUCT-PURCHASE-1',
    ],
    'product sales report' => [
        'productreportsale',
        ['2026-01-01', '2026-12-31'],
        ['DT_RowIndex', 'DateFrom', 'DateTo', 'party_company_id', 'sale_code', 'party_id', 'Party', 'total_amount', 'discount_amount', 'final_amount'],
        'data.0.Party',
        '<span> Fixture Party</span>',
    ],
]);

it('returns the fields consumed by :dataset', function (
    string $routeName,
    array $parameters,
    array $expectedKeys,
    string $fixturePath,
    mixed $fixtureValue,
) {
    $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->get(route($routeName, $parameters))
        ->assertSuccessful()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [$expectedKeys],
        ])
        ->assertJsonCount(1, 'data')
        ->assertJsonPath($fixturePath, $fixtureValue);
})->with('datatable endpoints');

dataset('JSON lookup endpoints', [
    'companies' => ['getCompaniesList', 'companies', 'company_name', 'Fixture Company'],
    'user levels' => ['getUserLevelList', 'userlevels', 'name', 'Super Admin'],
]);

it('preserves the JSON lookup contract for :dataset', function (
    string $routeName,
    string $collectionKey,
    string $fixtureKey,
    string $fixtureValue,
) {
    $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->get(route($routeName))
        ->assertStatus(201)
        ->assertJsonStructure([
            'success',
            $collectionKey,
        ])
        ->assertJsonPath('success', 'yes')
        ->assertJsonFragment([$fixtureKey => $fixtureValue]);
})->with('JSON lookup endpoints');

it('returns the feed columns consumed by the DataTable', function () {
    $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->get(route('getfeedlist'))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [[
                'DT_RowIndex',
                'feed_category_id',
                'feed_name',
                'total_quantity',
                'remaining_quantity',
                'Actions',
            ]],
        ])
        ->assertJsonPath('data.0.feed_category_id', '<b>Starter Feed</b>')
        ->assertJsonPath('data.0.feed_name', 'Fixture Feed')
        ->assertJsonPath('data.0.total_quantity', 100)
        ->assertJsonPath('data.0.remaining_quantity', 75)
        ->assertJsonPath('data.0.DT_RowIndex', 1);

    expect($response->json('data.0.Actions'))
        ->toContain(route('feed.show', 1))
        ->toContain(route('feed.destroy', 1))
        ->toContain('View')
        ->toContain('Edit')
        ->toContain('Delete');
});

it('renders feed markup aligned with its DataTable configuration', function () {
    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('id="Feed-datatable"', false)
        ->assertDontSee('id="basic-datatable"', false)
        ->assertSeeInOrder([
            '<th> # </th>',
            '<th> Category </th>',
            '<th> Feed Name </th>',
            '<th> Total Quantity </th>',
            '<th> Remaining Quantity </th>',
            '<th> Actions </th>',
        ], false)
        ->assertSeeInOrder([
            "{data:'DT_RowIndex'}",
            "{data:'feed_category_id'}",
            "{data:'feed_name'}",
            "{data:'total_quantity'}",
            "{data:'remaining_quantity'}",
            "{data:'Actions'}",
        ], false)
        ->assertDontSee('Fixture Product');
});
