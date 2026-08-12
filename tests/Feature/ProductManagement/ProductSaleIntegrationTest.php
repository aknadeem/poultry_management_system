<?php

use App\Models\PartyBalance;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->user = User::query()->firstOrFail();
    $this->actingAs($this->user);

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
        'name' => 'Fixture Party',
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
        'product_group' => 2,
        'product_name' => 'Fixture Product',
        'party_company_id' => 1,
        'product_category_id' => 1,
        'quantity' => 100,
        'purchase_price' => 50,
        'sale_price' => 80,
        ...$timestamps,
    ]);
});

function productSalePayload(array $overrides = []): array
{
    return array_merge([
        'division_id' => 1,
        'party_id' => 1,
        'product_category_id' => 1,
        'party_company_id' => 1,
        'sale_date' => '2026-08-01',
        'due_date_option' => 'cash',
        'sale_type' => 'cash',
        'total_amount' => 800,
        'discount_amount' => 0,
        'discount_percentage' => 0,
        'other_charges' => 0,
        'final_amount' => 800,
        'product_id' => [1],
        'product_code' => ['PRODUCT-1'],
        'product_name' => ['Fixture Product'],
        'product_sale_price' => [80],
        'product_qty' => [10],
        'product_bonus_qty' => [0],
        'product_total_qty' => [10],
        'product_discount' => [0],
        'product_discount_percentage' => [0],
        'product_total_price' => [800],
    ], $overrides);
}

test('product sale store decreases inventory and creates party balance', function () {
    $this->withoutExceptionHandling();

    $this->post(route('productsales.store'), productSalePayload())
        ->assertRedirect(route('productsales.index'));

    expect(ProductSale::count())->toBe(1);
    expect((int) Product::find(1)->quantity)->toBe(90);

    $sale = ProductSale::firstOrFail();

    $this->assertDatabaseHas('party_balances', [
        'party_id' => 1,
        'total_amount' => 800,
        'remaining_amount' => 800,
    ]);

    expect(
        PartyBalance::where('party_id', 1)
            ->where('narration', 'like', "%(ProductSale #{$sale->id})%")
            ->count()
    )->toBe(1);
});

test('product sale destroy reverses inventory and deletes party balance', function () {
    $this->withoutExceptionHandling();

    $this->post(route('productsales.store'), productSalePayload())
        ->assertRedirect();

    $sale = ProductSale::firstOrFail();
    expect((int) Product::find(1)->quantity)->toBe(90);

    $this->delete(route('productsales.destroy', $sale->id))
        ->assertRedirect(route('productsales.index'));

    expect(ProductSale::count())->toBe(0);
    expect((int) Product::find(1)->quantity)->toBe(100);
    expect(
        PartyBalance::where('party_id', 1)
            ->where('narration', 'like', "%(ProductSale #{$sale->id})%")
            ->count()
    )->toBe(0);
});
