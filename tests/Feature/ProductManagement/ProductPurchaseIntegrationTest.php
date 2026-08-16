<?php

use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
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
        ...$timestamps,
    ]);
});

test('product purchase store increases inventory and creates balances', function () {
    $this->withoutExceptionHandling();

    $payload = [
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
    ];

    $this->post(route('productpurchases.store'), $payload)
        ->assertRedirect(route('productpurchases.index'));

    expect(ProductPurchase::count())->toBe(1);
    expect((int) Product::find(1)->quantity)->toBe(20);

    $this->assertDatabaseHas('company_balances', [
        'type' => 'product_purchase',
        'company_id' => 1,
        'total_amount' => 500,
    ]);

    expect(AccountPayable::where('amount_type', 'product_purchase')->count())->toBe(1);
});

test('product purchase destroy reverses inventory and balances', function () {
    $this->withoutExceptionHandling();

    $this->post(route('productpurchases.store'), [
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-01',
        'total_amount' => 500,
        'discount_amount' => 0,
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
    ])->assertRedirect();

    $purchase = ProductPurchase::firstOrFail();

    $this->delete(route('productpurchases.destroy', $purchase->id))
        ->assertRedirect(route('productpurchases.index'));

    expect(ProductPurchase::count())->toBe(0);
    expect((int) Product::find(1)->quantity)->toBe(10);
    expect(CompanyBalance::where('type', 'product_purchase')->count())->toBe(0);
    expect(AccountPayable::where('amount_type', 'product_purchase')->count())->toBe(0);
});

test('product purchase rebate stores the purchase id and reduces line totals', function () {
    $this->withoutExceptionHandling();

    $this->post(route('productpurchases.store'), [
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-01',
        'total_amount' => 500,
        'discount_amount' => 0,
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
    ])->assertRedirect();

    $purchase = ProductPurchase::firstOrFail();
    $detail = ProductPurchaseDetail::query()->where('product_purchase_id', $purchase->id)->firstOrFail();

    $this->from(route('productpurchases.show', $purchase->id))
        ->post(route('productRebate'), [
            'from_page' => 'ProductPurchaseDetail',
            'product_detail_id' => $detail->id,
            'rebate_qty' => 2,
            'rebate_reason' => 'Damaged stock',
            'rebate_description' => 'Two units returned to supplier',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('product_purchase_rebates', [
        'rebate_item_id' => $purchase->id,
        'product_id' => 1,
        'rebate_qty' => 2,
        'rebate_reason' => 'Damaged stock',
    ]);

    $detail->refresh();
    $purchase->refresh();

    expect((int) $detail->product_total_qty)->toBe(8);
    expect((int) $detail->is_rebate)->toBe(1);
    expect((int) $detail->rebate_qty)->toBe(2);
    expect((float) $purchase->final_amount)->toBe(400.0);
    expect((int) $purchase->is_rebate)->toBe(1);
    expect((float) $purchase->rebate_amount)->toBe(100.0);
    expect(ProductPurchaseRebate::count())->toBe(1);
});

test('product purchase rebate rejects a quantity larger than remaining qty', function () {
    $this->post(route('productpurchases.store'), [
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-01',
        'total_amount' => 500,
        'discount_amount' => 0,
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
    ])->assertRedirect();

    $purchase = ProductPurchase::firstOrFail();
    $detail = ProductPurchaseDetail::query()->where('product_purchase_id', $purchase->id)->firstOrFail();

    $this->from(route('productpurchases.show', $purchase->id))
        ->post(route('productRebate'), [
            'from_page' => 'ProductPurchaseDetail',
            'product_detail_id' => $detail->id,
            'rebate_qty' => 11,
            'rebate_reason' => 'Too many',
            'rebate_description' => 'Should fail',
        ])
        ->assertSessionHasErrors('rebate_qty');

    expect(ProductPurchaseRebate::count())->toBe(0);
});
