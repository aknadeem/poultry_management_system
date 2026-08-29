<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->withoutVite();
    $this->seed(UserSeeder::class);
    $this->user = User::query()->firstOrFail();
    seedReportFixtures();
});

function seedReportFixtures(): void
{
    $timestamps = [
        'created_at' => '2026-08-10 10:00:00',
        'updated_at' => '2026-08-10 10:00:00',
    ];

    DB::table('business_types')->insert([
        'id' => 1,
        'name' => 'Supplier',
        'slug' => 'supplier',
        ...$timestamps,
    ]);
    DB::table('divisions')->insert([
        'id' => 1,
        'name' => 'Fixture Division',
        'slug' => 'fixture-division',
        ...$timestamps,
    ]);
    DB::table('parties')->insert([
        'id' => 1,
        'is_vendor' => true,
        'is_customer' => true,
        'name' => 'Fixture Customer',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        'customer_division_id' => 1,
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        'company_address' => 'Fixture Address',
        'is_active' => 1,
        ...$timestamps,
    ]);
    DB::table('product_categories')->insert([
        'id' => 1,
        'name' => 'Fixture Category',
        'slug' => 'fixture-category',
        'company_id' => 1,
        'is_active' => 1,
        ...$timestamps,
    ]);
    DB::table('chick_grades')->insert([
        'id' => 1,
        'name' => 'Grade A',
        ...$timestamps,
    ]);
    DB::table('chicken_sales')->insert([
        'id' => 1,
        'manual_number' => 'M-100',
        'sale_date' => '2026-08-11',
        'party_id' => 1,
        'customer_id' => 1,
        'total_weight' => 500,
        'per_kg_price' => 200,
        'discount_amount' => 0,
        'total_price' => 100000,
        ...$timestamps,
    ]);
    DB::table('chick_purchases')->insert([
        'id' => 1,
        'purchase_date' => '2026-08-11',
        'company_id' => 1,
        'customer_id' => 1,
        'chick_grade_id' => 1,
        'quantity' => 1000,
        'price' => 50,
        'discount_amount' => 0,
        'total_price' => 50000,
        ...$timestamps,
    ]);
    DB::table('products')->insert([
        'id' => 1,
        'product_code' => 'PRODUCT-1',
        'product_group' => 2,
        'product_name' => 'Fixture Product',
        'party_company_id' => 1,
        'product_category_id' => 1,
        'quantity' => 10,
        'purchase_price' => 50,
        'sale_price' => 80,
        'created_at' => '2026-08-11 12:00:00',
        'updated_at' => '2026-08-11 12:00:00',
    ]);
    DB::table('product_purchases')->insert([
        'id' => 1,
        'purchase_code' => '00001',
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-11',
        'total_amount' => 500,
        'discount_amount' => 0,
        'final_amount' => 500,
        ...$timestamps,
    ]);
    DB::table('product_sales')->insert([
        'id' => 1,
        'sale_code' => '00001',
        'division_id' => 1,
        'party_id' => 1,
        'party_company_id' => 1,
        'product_category_id' => 1,
        'sale_date' => '2026-08-11',
        'due_date_option' => 'cash',
        'sale_type' => 'cash',
        'total_amount' => 800,
        'discount_amount' => 0,
        'final_amount' => 800,
        ...$timestamps,
    ]);
}

it('prevents guests from accessing report endpoints', function (): void {
    $this->get(route('inertia.reports.chick-sale'))->assertRedirect(route('inertia.login'));
    $this->get(route('inertia.reports.product'))->assertRedirect(route('inertia.login'));
});

it('loads chick sale report form empty without dates', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.chick-sale'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ChickSaleReport')
            ->has('sales', 0)
            ->where('filters.searched', false)
        );
});

it('loads product report empty without dates', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductReport')
            ->has('products', 0)
            ->where('filters.searched', false)
        );
});

it('loads chick purchase report form empty without dates', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.chick-purchase'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ChickPurchaseReport')
            ->where('filters.searched', false)
        );
});

it('loads product purchase report form empty without dates', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product-purchase'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductPurchaseReport')
            ->where('filters.searched', false)
        );
});

it('loads product sale report form empty without dates', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product-sale'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductSaleReport')
            ->where('filters.searched', false)
        );
});

it('loads chick sale report rows for the selected date range', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.chick-sale', [
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-31',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ChickSaleReport')
            ->has('sales', 1)
            ->where('filters.searched', true)
            ->where('sales.0.per_kg_price', '200.00')
            ->where('sales.0.total_weight', 500)
            ->where('sales.0.manual_number', 'M-100')
        );
});

it('loads chick purchase report rows for the selected date range', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.chick-purchase', [
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-31',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ChickPurchaseReport')
            ->has('purchases', 1)
            ->where('filters.searched', true)
            ->where('purchases.0.price', '50.00')
            ->where('purchases.0.quantity', 1000)
            ->where('purchases.0.company_name', 'Fixture Company')
        );
});

it('loads product report rows for the selected date range', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product', [
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-31',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductReport')
            ->has('products', 1)
            ->where('filters.searched', true)
            ->where('products.0.product_code', 'PRODUCT-1')
            ->where('products.0.company_name', 'Fixture Company')
        );
});

it('loads product purchase report rows for the selected date range', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product-purchase', [
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-31',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductPurchaseReport')
            ->has('purchases', 1)
            ->where('filters.searched', true)
            ->where('purchases.0.purchase_code', '00001')
            ->where('purchases.0.final_amount', 500)
        );
});

it('loads product sale report rows for the selected date range', function (): void {
    $this->actingAs($this->user)
        ->get(route('inertia.reports.product-sale', [
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-31',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reports/ProductSaleReport')
            ->has('sales', 1)
            ->where('filters.searched', true)
            ->where('sales.0.sale_code', '00001')
            ->where('sales.0.party_name', 'Fixture Customer')
            ->where('sales.0.final_amount', 800)
        );
});
