<?php

use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
use App\Models\User;
use App\Services\InventoryService;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $this->seed(UserSeeder::class);
    Storage::fake('public');
    seedProductFixtures();
});

function inertiaProductUser(): User
{
    return User::query()->firstOrFail();
}

function seedProductFixtures(): void
{
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
    DB::table('product_categories')->insert([
        'id' => 1,
        'name' => 'Fixture Product Category',
        'slug' => 'fixture-product-category',
        'company_id' => 1,
        ...$timestamps,
    ]);
    DB::table('product_stores')->insert([
        'id' => 1,
        'store_name' => 'Main Store',
        'store_code' => '00001',
        'store_area' => 100,
        'total_racks' => 10,
        ...$timestamps,
    ]);
    DB::table('vaccination_groups')->insert([
        'id' => 1,
        'name' => 'ND Group',
        'slug' => 'nd-group',
        ...$timestamps,
    ]);
    DB::table('product_types')->insert([
        'id' => 1,
        'name' => 'Local',
        'slug' => 'local',
        ...$timestamps,
    ]);
}

function inertiaProductPayload(array $overrides = []): array
{
    return array_merge([
        'product_group' => 2,
        'company_id' => 1,
        'product_category_id' => 1,
        'product_name' => 'Broiler Starter Feed',
        'batch_number' => 'BN-100',
        'serial_number' => 'SN-200',
        'product_type' => 1,
        'vaccination_group' => 1,
        'pack_size_unit' => 25,
        'pack_size_unit_type' => 'kilo_gram',
        'store_id' => 1,
        'rack_number' => 12,
        'min_level' => 5,
        'max_level' => 50,
        'mrp_price' => 110.50,
        'whole_sale_price' => 95.25,
        'full_less_price' => 90,
        'store_price' => 92.75,
        'retail_price' => 105,
        'trade_price' => 88.40,
        'purchase_price' => 80,
        'sale_price' => 100,
        'discount_amount' => 2.50,
        'tax_percentage' => 17,
        'tax_amount' => 17,
        'discount_percentage' => 2.5,
        'warranty_period' => 30,
        'is_taxable' => 1,
        'is_sale_on_tp' => 1,
        'is_claimable' => 1,
        'is_fridged' => 1,
        'is_narcotic' => 1,
        'is_unwaranted' => 1,
        'description' => 'Full catalog product',
    ], $overrides);
}

function inertiaPurchasePayload(array $overrides = []): array
{
    return array_merge([
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-01',
        'total_amount' => 500,
        'discount_amount' => 0,
        'discount_percentage' => 0,
        'other_charges' => 0,
        'final_amount' => 500,
        'product_id' => [1],
        'product_code' => ['PRODUCT-1'],
        'product_name' => ['Fixture Product'],
        'product_sale_price' => [50],
        'product_qty' => [10],
        'product_bonus_qty' => [0],
        'product_total_qty' => [10],
        'product_discount' => [0],
        'product_discount_percentage' => [0],
        'product_total_price' => [500],
    ], $overrides);
}

function seedCatalogProduct(array $overrides = []): Product
{
    $attributes = array_merge([
        'product_code' => 'PRODUCT-1',
        'product_group' => 2,
        'product_name' => 'Fixture Product',
        'party_company_id' => 1,
        'product_category_id' => 1,
        'product_type' => 1,
        'product_type_id' => 1,
        'quantity' => 10,
        'purchase_price' => 50,
        'sale_price' => 80,
    ], $overrides);

    return Product::query()->create($attributes);
}

it('redirects guests away from inertia product pages', function () {
    $this->get(route('inertia.products.index'))->assertRedirect();
    $this->get(route('inertia.product-purchases.index'))->assertRedirect();
});

it('lists products through inertia with typed product_type options', function () {
    seedCatalogProduct(['product_name' => 'Inertia Product']);

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->has('products.data')
            ->has('filters')
            ->where('products.data.0.product_name', 'Inertia Product')
            ->where('products.data.0.product_type', 1)
        );

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.products.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Create')
            ->has('productTypes')
            ->where('productTypes.0.id', 1)
            ->where('productTypes.0.name', 'Local')
        );
});

it('creates updates and deletes a product through inertia using product_types id', function () {
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.products.store'), inertiaProductPayload([
            'product_picture' => UploadedFile::fake()->image('feed.png'),
        ]))
        ->assertRedirect(route('inertia.products.index'));

    $product = Product::query()->firstOrFail();
    expect($product->product_type)->toBe('1')
        ->and((int) $product->product_type_id)->toBe(1);
    Storage::disk('public')->assertExists('products/'.$product->product_picture);

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.products.edit', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Edit')
            ->where('product.product_type', 1)
            ->where('product.product_type_name', 'Local')
        );

    $this->actingAs(inertiaProductUser())
        ->put(route('inertia.products.update', $product), inertiaProductPayload([
            'product_name' => 'Updated Inertia Product',
            'product_type' => 1,
        ]))
        ->assertRedirect(route('inertia.products.index'));

    expect($product->refresh()->product_name)->toBe('Updated Inertia Product')
        ->and((int) $product->product_type_id)->toBe(1);

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.products.show', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Show')
            ->where('product.product_name', 'Updated Inertia Product')
        );

    $this->actingAs(inertiaProductUser())
        ->put(route('inertia.products.toggle-status', $product))
        ->assertRedirect(route('inertia.products.index'));

    $this->actingAs(inertiaProductUser())
        ->delete(route('inertia.products.destroy', $product))
        ->assertRedirect(route('inertia.products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

it('validates product_type on inertia store', function () {
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.products.store'), inertiaProductPayload([
            'product_type' => '',
        ]))
        ->assertSessionHasErrors('product_type');
});

it('soft deletes a product with purchase history and keeps historical records', function () {
    $product = seedCatalogProduct();
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertRedirect(route('inertia.product-purchases.index'));

    $this->actingAs(inertiaProductUser())
        ->delete(route('inertia.products.destroy', $product))
        ->assertRedirect(route('inertia.products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
    $this->assertDatabaseHas('product_purchase_details', [
        'product_id' => $product->id,
    ]);
});

it('creates a product purchase through inertia exactly once per request', function () {
    seedCatalogProduct();

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-purchases.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductPurchases/Create')
            ->has('productGroups')
            ->has('companies')
            ->has('productCategories')
        );

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertRedirect(route('inertia.product-purchases.index'));

    expect(ProductPurchase::count())->toBe(1)
        ->and(ProductPurchaseDetail::count())->toBe(1)
        ->and(CompanyBalance::count())->toBe(1)
        ->and(AccountPayable::count())->toBe(1)
        ->and((int) Product::query()->find(1)->quantity)->toBe(20);

    $purchase = ProductPurchase::query()->firstOrFail();

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-purchases.show', $purchase))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductPurchases/Show')
            ->where('purchase.invoice_url', route('productpurchases.invoice', $purchase->id, false))
            ->has('purchase.items', 1)
        );
});

it('destroys a product purchase through inertia and reverses inventory and financials once', function () {
    seedCatalogProduct();

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertRedirect();

    $purchase = ProductPurchase::query()->firstOrFail();

    $this->actingAs(inertiaProductUser())
        ->delete(route('inertia.product-purchases.destroy', $purchase))
        ->assertRedirect(route('inertia.product-purchases.index'));

    expect(ProductPurchase::count())->toBe(0)
        ->and(ProductPurchaseDetail::count())->toBe(0)
        ->and(CompanyBalance::count())->toBe(0)
        ->and(AccountPayable::count())->toBe(0)
        ->and((int) Product::query()->find(1)->quantity)->toBe(10);
});

it('rolls back an inertia product purchase when inventory fails', function () {
    seedCatalogProduct();

    $this->mock(InventoryService::class, function ($mock): void {
        $mock->shouldReceive('increaseProductStock')->once()->andThrow(new RuntimeException('stock failed'));
    });

    $this->actingAs(inertiaProductUser())->withoutExceptionHandling();

    expect(fn () => $this->post(route('inertia.product-purchases.store'), inertiaPurchasePayload()))
        ->toThrow(RuntimeException::class);

    expect(ProductPurchase::count())->toBe(0)
        ->and(CompanyBalance::count())->toBe(0)
        ->and(AccountPayable::count())->toBe(0)
        ->and((int) Product::query()->find(1)->quantity)->toBe(10);
});

it('rejects invalid purchase quantities and unknown companies', function () {
    seedCatalogProduct();

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload([
            'product_qty' => [0],
        ]))
        ->assertSessionHasErrors('items.0.product_qty');

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload([
            'party_company_id' => 999,
        ]))
        ->assertSessionHasErrors('party_company_id');

    expect(ProductPurchase::count())->toBe(0);
});

it('toggles product purchase status on the product_purchases table', function () {
    seedCatalogProduct();
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertRedirect();

    $purchase = ProductPurchase::query()->firstOrFail();
    expect((int) $purchase->is_active)->toBe(1);

    $this->actingAs(inertiaProductUser())
        ->put(route('inertia.product-purchases.toggle-status', $purchase))
        ->assertRedirect(route('inertia.product-purchases.index'));

    expect((int) $purchase->refresh()->is_active)->toBe(0);
});

it('records a purchase rebate through inertia and rejects invalid quantities', function () {
    seedCatalogProduct();
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertRedirect();

    $detail = ProductPurchaseDetail::query()->firstOrFail();

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.rebate'), [
            'from_page' => 'ProductPurchaseDetail',
            'product_detail_id' => $detail->id,
            'rebate_qty' => 0,
        ])
        ->assertSessionHasErrors('rebate_qty');

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.rebate'), [
            'from_page' => 'ProductPurchaseDetail',
            'product_detail_id' => $detail->id,
            'rebate_qty' => 11,
        ])
        ->assertSessionHasErrors('rebate_qty');

    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-purchases.rebate'), [
            'from_page' => 'ProductPurchaseDetail',
            'product_detail_id' => $detail->id,
            'rebate_qty' => 2,
            'rebate_reason' => 'Damaged',
        ])
        ->assertRedirect();

    expect(ProductPurchaseRebate::count())->toBe(1)
        ->and((int) $detail->refresh()->rebate_qty)->toBe(2);

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-purchases.rebates'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductPurchases/Rebates')
            ->has('rebates', 1)
        );
});

it('forbids inertia product mutations for users without product privileges', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inertia.products.store'), inertiaProductPayload())
        ->assertForbidden();

    seedCatalogProduct();

    $this->actingAs($user)
        ->post(route('inertia.product-purchases.store'), inertiaPurchasePayload())
        ->assertForbidden();
});

it('ignores arbitrary product sort columns', function () {
    seedCatalogProduct(['product_name' => 'Alpha']);
    seedCatalogProduct([
        'product_code' => 'PRODUCT-2',
        'product_name' => 'Beta',
    ]);

    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.products.index', [
            'sort' => 'drop table products',
            'direction' => 'asc',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->where('filters.sort', 'id')
            ->where('filters.direction', 'asc')
        );
});

it('keeps blade product and purchase store routes working', function () {
    $this->actingAs(inertiaProductUser())
        ->post(route('products.store'), inertiaProductPayload())
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'product_name' => 'Broiler Starter Feed',
        'product_type_id' => 1,
    ]);

    $catalog = Product::query()->where('product_name', 'Broiler Starter Feed')->firstOrFail();

    $this->actingAs(inertiaProductUser())
        ->post(route('productpurchases.store'), inertiaPurchasePayload([
            'product_id' => [$catalog->id],
            'product_code' => [$catalog->product_code],
            'product_name' => [$catalog->product_name],
        ]))
        ->assertRedirect();

    expect(ProductPurchase::count())->toBe(1);
});

it('does not register inertia product purchase edit or update routes', function () {
    expect(Route::has('inertia.product-purchases.edit'))->toBeFalse()
        ->and(Route::has('inertia.product-purchases.update'))->toBeFalse();
});

it('redirects guests away from inertia product store pages', function () {
    $this->get(route('inertia.product-stores.index'))->assertRedirect();
});

it('lists product stores through inertia', function () {
    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-stores.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductStores/Index')
            ->has('stores.data')
            ->has('filters')
            ->where('stores.data.0.store_name', 'Main Store')
            ->where('routes.inertia.product-stores.index', '/app/productmanagement/product-stores')
            ->where('urls.productStores', route('inertia.product-stores.index'))
        );
});

it('shows the create product store page', function () {
    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-stores.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductStores/Create')
        );
});

it('creates a product store through inertia', function () {
    $this->actingAs(inertiaProductUser())
        ->post(route('inertia.product-stores.store'), [
            'store_name' => 'New Test Store',
            'store_type' => 'Retail',
            'total_racks' => 15,
            'store_area' => 250.5,
            'store_desciption' => 'Test Description',
        ])
        ->assertRedirect(route('inertia.product-stores.index'));

    $this->assertDatabaseHas('product_stores', [
        'store_name' => 'New Test Store',
        'store_type' => 'Retail',
        'total_racks' => 15,
        'store_area' => 250.5,
        'description' => 'Test Description',
    ]);
});

it('shows the edit product store page', function () {
    $store = \App\Models\ProductStore::query()->firstOrFail();
    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-stores.edit', $store))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductStores/Edit')
            ->where('store.store_name', $store->store_name)
        );
});

it('updates a product store through inertia', function () {
    $store = \App\Models\ProductStore::query()->firstOrFail();
    $this->actingAs(inertiaProductUser())
        ->put(route('inertia.product-stores.update', $store), [
            'store_name' => 'Updated Test Store',
            'store_type' => 'Warehouse',
            'total_racks' => 8,
            'store_area' => 500,
            'store_desciption' => 'Updated Description',
        ])
        ->assertRedirect(route('inertia.product-stores.index'));

    expect($store->refresh()->store_name)->toBe('Updated Test Store')
        ->and($store->store_type)->toBe('Warehouse')
        ->and((int) $store->total_racks)->toBe(8)
        ->and((float) $store->store_area)->toBe(500.0)
        ->and($store->description)->toBe('Updated Description');
});

it('shows the show product store page', function () {
    $store = \App\Models\ProductStore::query()->firstOrFail();
    $this->actingAs(inertiaProductUser())
        ->get(route('inertia.product-stores.show', $store))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProductStores/Show')
            ->where('store.store_name', $store->store_name)
        );
});

it('toggles the product store active status', function () {
    $store = \App\Models\ProductStore::query()->firstOrFail();
    
    // Set status to active first
    $store->update(['is_active' => 1]);

    $this->actingAs(inertiaProductUser())
        ->put(route('inertia.product-stores.toggle-status', $store))
        ->assertRedirect(route('inertia.product-stores.index'));

    expect((int) $store->refresh()->is_active)->toBe(0);
});

it('deletes a product store through inertia', function () {
    $store = \App\Models\ProductStore::query()->firstOrFail();
    $this->actingAs(inertiaProductUser())
        ->delete(route('inertia.product-stores.destroy', $store))
        ->assertRedirect(route('inertia.product-stores.index'));

    $this->assertSoftDeleted('product_stores', ['id' => $store->id]);
});

