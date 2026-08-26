<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
});

function productPayload(array $overrides = []): array
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

test('product store persists every create-form field', function () {
    $this->withoutExceptionHandling();

    $this->post(route('products.store'), productPayload())
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'party_company_id' => 1,
        'product_category_id' => 1,
        'product_group' => 2,
        'product_name' => 'Broiler Starter Feed',
        'batch_number' => 'BN-100',
        'serial_number' => 'SN-200',
        'product_type' => 1,
        'product_type_id' => 1,
        'vaccination_group_id' => 1,
        'pack_size' => 25,
        'pack_size_unit_type' => 'kilo_gram',
        'product_store_id' => 1,
        'rack_number' => 12,
        'min_inventory_level' => 5,
        'max_inventory_level' => 50,
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
        'is_unwarranted' => 1,
        'description' => 'Full catalog product',
        'addedby' => $this->user->id,
    ]);

    $product = Product::query()->first();
    expect($product->reorder_level_period)->toBe(30);
    expect($product->reorder_level_date)->not->toBeNull();
});

test('product store stores unchecked checkboxes as zero', function () {
    $payload = productPayload();
    unset(
        $payload['is_taxable'],
        $payload['is_sale_on_tp'],
        $payload['is_claimable'],
        $payload['is_fridged'],
        $payload['is_narcotic'],
        $payload['is_unwaranted']
    );

    $this->post(route('products.store'), $payload)
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'product_name' => 'Broiler Starter Feed',
        'is_taxable' => 0,
        'is_sale_on_tp' => 0,
        'is_claimable' => 0,
        'is_fridged' => 0,
        'is_narcotic' => 0,
        'is_unwarranted' => 0,
    ]);
});

test('product update persists fields and keeps the existing picture', function () {
    Storage::fake('public');

    $this->post(route('products.store'), productPayload([
        'product_picture' => UploadedFile::fake()->image('feed.png'),
    ]))->assertRedirect(route('products.index'));

    $product = Product::query()->firstOrFail();
    $existingPicture = $product->product_picture;
    expect($existingPicture)->not->toBeNull();
    Storage::disk('public')->assertExists('products/'.$existingPicture);

    $this->put(route('products.update', $product->id), productPayload([
        'product_name' => 'Updated Feed',
        'sale_price' => 120,
        'is_taxable' => 0,
        'is_sale_on_tp' => 0,
        'is_claimable' => 0,
        'is_fridged' => 0,
        'is_narcotic' => 0,
        'is_unwaranted' => 0,
    ]))->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'product_name' => 'Updated Feed',
        'sale_price' => 120,
        'batch_number' => 'BN-100',
        'product_picture' => $existingPicture,
        'updatedby' => $this->user->id,
        'is_taxable' => 0,
        'is_unwarranted' => 0,
    ]);
});

test('product destroy soft deletes the product', function () {
    $this->post(route('products.store'), productPayload())
        ->assertRedirect(route('products.index'));

    $product = Product::query()->firstOrFail();

    $this->delete(route('products.destroy', $product->id))
        ->assertRedirect(route('products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('product picture replacement keeps the existing file when the new upload fails', function () {
    Storage::fake('public');

    $this->post(route('products.store'), productPayload([
        'product_picture' => UploadedFile::fake()->image('feed.png'),
    ]))->assertRedirect(route('products.index'));

    $product = Product::query()->firstOrFail();
    $existingPicture = $product->product_picture;
    Storage::disk('public')->assertExists('products/'.$existingPicture);

    $this->mock(\App\Services\FileUploadService::class, function ($mock): void {
        $mock->shouldReceive('store')->once()->andThrow(new RuntimeException('upload failed'));
        $mock->shouldReceive('delete')->zeroOrMoreTimes();
    });

    $this->withoutExceptionHandling();

    expect(fn () => $this->put(route('products.update', $product->id), productPayload([
        'product_name' => 'Should Not Persist',
        'product_picture' => UploadedFile::fake()->image('replacement.png'),
    ])))->toThrow(RuntimeException::class);

    $product->refresh();
    expect($product->product_name)->toBe('Broiler Starter Feed')
        ->and($product->product_picture)->toBe($existingPicture);
    Storage::disk('public')->assertExists('products/'.$existingPicture);
});

test('product destroy with purchase history soft deletes and keeps historical records', function () {
    $this->post(route('products.store'), productPayload())
        ->assertRedirect(route('products.index'));

    $product = Product::query()->firstOrFail();

    DB::table('product_purchases')->insert([
        'id' => 1,
        'party_company_id' => 1,
        'product_category_id' => 1,
        'purchase_date' => '2026-08-01',
        'total_amount' => 100,
        'final_amount' => 100,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('product_purchase_details')->insert([
        'product_purchase_id' => 1,
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'product_qty' => 5,
        'product_total_qty' => 5,
        'product_purchase_price' => 80,
        'product_total_price' => 400,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->delete(route('products.destroy', $product->id))
        ->assertRedirect(route('products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
    $this->assertDatabaseHas('product_purchase_details', [
        'product_purchase_id' => 1,
        'product_id' => $product->id,
    ]);
});
