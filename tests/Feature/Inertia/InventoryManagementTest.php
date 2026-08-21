<?php

use App\Models\BrokerBalance;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\CompanyBalance;
use App\Models\Feed;
use App\Models\FeedPurchase;
use App\Models\PartyBalance;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
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
    seedInventoryFixtures();
});

function inertiaInventoryUser(): User
{
    return User::query()->firstOrFail();
}

function seedInventoryFixtures(): void
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
        [
            'id' => 1,
            'is_customer' => true,
            'is_vendor' => false,
            'is_active' => true,
            'name' => 'Fixture Customer',
            'cnic_no' => '1000000000001',
            'contact_no' => '03000000001',
            ...$timestamps,
        ],
        [
            'id' => 2,
            'is_customer' => false,
            'is_vendor' => true,
            'is_active' => true,
            'name' => 'Fixture Vendor',
            'cnic_no' => '1000000000002',
            'contact_no' => '03000000002',
            ...$timestamps,
        ],
    ]);
    DB::table('party_farms')->insert([
        'id' => 1,
        'party_id' => 1,
        'farm_name' => 'Fixture Customer Farm',
        'farm_code' => 'FARM-001',
        'farm_capacity' => 5000,
        'farm_address' => 'Farm Road',
        'folk_quantity' => 0,
        'is_occupied' => 0,
        'is_active' => true,
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 2,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        'company_address' => 'Vendor Address',
        'is_active' => true,
        ...$timestamps,
    ]);
    DB::table('chick_grades')->insert([
        'id' => 1,
        'name' => 'Grade A',
        ...$timestamps,
    ]);
    DB::table('brokers')->insert([
        'id' => 1,
        'broker_code' => 'BRK-001',
        'name' => 'Fixture Broker',
        'is_active' => true,
        ...$timestamps,
    ]);
    DB::table('feed_categories')->insert([
        'id' => 1,
        'name' => 'Starter',
        'slug' => 'starter',
        ...$timestamps,
    ]);
}

function inertiaSalePayload(array $overrides = []): array
{
    return array_merge([
        'manual_number' => 'M-123',
        'sale_date' => '2026-08-11',
        'customer_id' => 1,
        'broker_id' => 1,
        'first_weight' => 100,
        'second_weight' => 600,
        'net_weight' => 500,
        'total_weight' => 500,
        'per_kg_price' => 200,
        'discount_amount' => 0,
        'discount_percentage' => 0,
        'total_price' => 100000,
        'broker_commission' => 2000,
        'vehicle_number' => 'TRK-999',
        'driver_name' => 'Wali',
        'driver_contact' => '12345',
    ], $overrides);
}

function inertiaChickPurchasePayload(array $overrides = []): array
{
    return array_merge([
        'customer_id' => 1,
        'purchase_date' => '2026-08-11',
        'chick_grade_id' => 1,
        'company_id' => 1,
        'chick_entry_age' => 1,
        'chick_weight' => 45,
        'quantity' => 1000,
        'price' => 50,
        'discount_amount' => 500,
        'discount_percentage' => 1,
        'total_price' => 49500,
        'vehicle_number' => 'LES-1234',
        'driver_name' => 'Ahmad',
        'driver_contact' => '03009999999',
        'customer_farm_id' => 1,
        'vendor_id' => 2,
    ], $overrides);
}

function inertiaFeedPayload(array $overrides = []): array
{
    return array_merge([
        'feed_name' => 'Demo Starter Feed',
        'purchase_date' => '2026-08-01',
        'feed_category_id' => 1,
        'company_id' => 1,
        'quantity' => 50,
        'price' => 3000,
        'discount_amount' => 0,
        'discount_percentage' => 0,
        'total_price' => 150000,
        'bilty_charges' => 500,
        'per_bag_discount' => 0,
        'sale_order_number' => 'SO-1',
        'delivery_order_number' => 'DO-1',
    ], $overrides);
}

it('redirects guests away from inertia inventory pages', function () {
    $this->get(route('inertia.chick-sales.index'))->assertRedirect();
    $this->get(route('inertia.chick-purchases.index'))->assertRedirect();
    $this->get(route('inertia.feeds.index'))->assertRedirect();
});

it('lists chick sales through inertia', function () {
    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.chick-sales.store'), inertiaSalePayload())
        ->assertRedirect(route('inertia.chick-sales.index'));

    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-sales.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickSales/Index')
            ->has('sales.data')
            ->has('filters')
            ->where('sales.data.0.customer_name', 'Fixture Customer')
        );
});

it('creates updates shows and deletes a chick sale through inertia with balances once', function () {
    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-sales.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickSales/Create')
            ->has('customers')
            ->has('brokers')
        );

    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.chick-sales.store'), inertiaSalePayload([
            'image_file' => UploadedFile::fake()->image('sale.png'),
        ]))
        ->assertRedirect(route('inertia.chick-sales.index'));

    $sale = ChickenSale::query()->firstOrFail();
    expect(PartyBalance::count())->toBe(1)
        ->and(BrokerBalance::count())->toBe(1);
    Storage::disk('public')->assertExists('chickens/'.$sale->picture);

    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-sales.show', $sale))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickSales/Show')
            ->where('sale.customer_name', 'Fixture Customer')
            ->where('sale.manual_number', 'M-123')
        );

    $this->actingAs(inertiaInventoryUser())
        ->put(route('inertia.chick-sales.update', $sale), inertiaSalePayload([
            'total_price' => 89000,
            'broker_commission' => 1800,
            'discount_amount' => 1000,
            'discount_percentage' => 1,
        ]))
        ->assertRedirect(route('inertia.chick-sales.index'));

    expect(PartyBalance::count())->toBe(1)
        ->and(BrokerBalance::count())->toBe(1)
        ->and((float) $sale->refresh()->total_price)->toBe(89000.0);

    $this->actingAs(inertiaInventoryUser())
        ->delete(route('inertia.chick-sales.destroy', $sale))
        ->assertRedirect(route('inertia.chick-sales.index'));

    $this->assertSoftDeleted('chicken_sales', ['id' => $sale->id]);
    expect(PartyBalance::count())->toBe(0)
        ->and(BrokerBalance::count())->toBe(0);
});

it('rolls back a chick sale and staged picture when balance writing fails', function () {
    $this->mock(FinancialBalanceService::class, function ($mock): void {
        $mock->shouldReceive('recordChickenSaleBalances')->once()->andThrow(new RuntimeException('balance failed'));
    });

    $this->actingAs(inertiaInventoryUser())->withoutExceptionHandling();

    expect(fn () => $this->post(route('inertia.chick-sales.store'), inertiaSalePayload([
        'image_file' => UploadedFile::fake()->image('sale.png'),
    ])))->toThrow(RuntimeException::class);

    expect(ChickenSale::count())->toBe(0)
        ->and(PartyBalance::count())->toBe(0)
        ->and(BrokerBalance::count())->toBe(0)
        ->and(Storage::disk('public')->files('chickens'))->toBe([]);
});

it('keeps the existing chick sale picture when the replacement upload fails', function () {
    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.chick-sales.store'), inertiaSalePayload([
            'image_file' => UploadedFile::fake()->image('sale.png'),
        ]))
        ->assertRedirect();

    $sale = ChickenSale::query()->firstOrFail();
    $existingPicture = $sale->picture;
    Storage::disk('public')->assertExists('chickens/'.$existingPicture);

    $this->mock(FileUploadService::class, function ($mock): void {
        $mock->shouldReceive('store')->once()->andThrow(new RuntimeException('upload failed'));
        $mock->shouldReceive('delete')->zeroOrMoreTimes();
    });

    $this->actingAs(inertiaInventoryUser())->withoutExceptionHandling();

    expect(fn () => $this->put(route('inertia.chick-sales.update', $sale), inertiaSalePayload([
        'manual_number' => 'Should Not Persist',
        'image_file' => UploadedFile::fake()->image('replacement.png'),
    ])))->toThrow(RuntimeException::class);

    $sale->refresh();
    expect($sale->manual_number)->toBe('M-123')
        ->and($sale->picture)->toBe($existingPicture);
    Storage::disk('public')->assertExists('chickens/'.$existingPicture);
});

it('creates updates shows and deletes a chick purchase through inertia occupying the farm once', function () {
    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-purchases.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickPurchases/Create')
            ->has('chickGrades')
            ->has('companies')
            ->has('customers')
        );

    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.chick-purchases.store'), inertiaChickPurchasePayload([
            'image_file' => UploadedFile::fake()->image('purchase.png'),
        ]))
        ->assertRedirect(route('inertia.chick-purchases.index'));

    $purchase = ChickPurchase::query()->firstOrFail();
    $farm = PartyFarm::query()->findOrFail(1);
    expect((int) $farm->folk_quantity)->toBe(1000)
        ->and((int) $farm->is_occupied)->toBe(1)
        ->and(PartyFarmChickHistory::count())->toBe(1)
        ->and(CompanyBalance::where('type', 'chick_purchase')->count())->toBe(1)
        ->and(PartyBalance::count())->toBe(1);
    Storage::disk('public')->assertExists('chicks/'.$purchase->picture);

    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-purchases.show', $purchase))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickPurchases/Show')
            ->where('purchase.company_name', 'Fixture Company')
            ->where('purchase.customer_farm_name', 'Fixture Customer Farm')
        );

    $this->actingAs(inertiaInventoryUser())
        ->put(route('inertia.chick-purchases.update', $purchase), inertiaChickPurchasePayload([
            'quantity' => 800,
            'price' => 45,
            'discount_amount' => 0,
            'discount_percentage' => 0,
            'total_price' => 36000,
        ]))
        ->assertRedirect(route('inertia.chick-purchases.index'));

    expect((int) $farm->refresh()->folk_quantity)->toBe(800)
        ->and(CompanyBalance::where('type', 'chick_purchase')->count())->toBe(1)
        ->and(PartyBalance::count())->toBe(1);

    $this->actingAs(inertiaInventoryUser())
        ->delete(route('inertia.chick-purchases.destroy', $purchase))
        ->assertRedirect(route('inertia.chick-purchases.index'));

    $this->assertSoftDeleted('chick_purchases', ['id' => $purchase->id]);
    expect((int) $farm->refresh()->folk_quantity)->toBe(0)
        ->and((int) $farm->is_occupied)->toBe(0)
        ->and(PartyFarmChickHistory::count())->toBe(0)
        ->and(CompanyBalance::where('type', 'chick_purchase')->count())->toBe(0)
        ->and(PartyBalance::count())->toBe(0);
});

it('rejects chick purchases that use the same party as customer and vendor', function () {
    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.chick-purchases.store'), inertiaChickPurchasePayload([
            'vendor_id' => 1,
        ]))
        ->assertSessionHasErrors('customer_id');

    expect(ChickPurchase::count())->toBe(0)
        ->and(PartyFarmChickHistory::count())->toBe(0);
});

it('rolls back a chick purchase when balance writing fails', function () {
    $this->mock(FinancialBalanceService::class, function ($mock): void {
        $mock->shouldReceive('recordChickPurchaseBalances')->once()->andThrow(new RuntimeException('balance failed'));
    });

    $this->actingAs(inertiaInventoryUser())->withoutExceptionHandling();

    expect(fn () => $this->post(route('inertia.chick-purchases.store'), inertiaChickPurchasePayload([
        'image_file' => UploadedFile::fake()->image('purchase.png'),
    ])))->toThrow(RuntimeException::class);

    expect(ChickPurchase::count())->toBe(0)
        ->and(PartyFarmChickHistory::count())->toBe(0)
        ->and((int) PartyFarm::query()->findOrFail(1)->is_occupied)->toBe(0)
        ->and(Storage::disk('public')->files('chicks'))->toBe([]);
});

it('creates shows updates name and deletes a feed through inertia with company balance once', function () {
    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.feeds.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Feeds/Create')
            ->has('categories')
            ->has('companies')
        );

    $this->actingAs(inertiaInventoryUser())
        ->post(route('inertia.feeds.store'), inertiaFeedPayload([
            'image_file' => UploadedFile::fake()->image('feed.png'),
        ]))
        ->assertRedirect(route('inertia.feeds.index'));

    $feed = Feed::query()->firstOrFail();
    expect(FeedPurchase::count())->toBe(1)
        ->and(CompanyBalance::where('type', 'feed')->count())->toBe(1)
        ->and((int) $feed->remaining_quantity)->toBe(50);

    $purchase = FeedPurchase::query()->firstOrFail();
    Storage::disk('public')->assertExists('feeds/'.$purchase->picture);

    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.feeds.show', $feed))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Feeds/Show')
            ->where('feed.feed_name', 'Demo Starter Feed')
            ->has('feed.purchases', 1)
        );

    $this->actingAs(inertiaInventoryUser())
        ->put(route('inertia.feeds.update', $feed), [
            'feed_name' => 'Updated Starter Feed',
            'feed_category_id' => 1,
        ])
        ->assertRedirect(route('inertia.feeds.index'));

    expect($feed->refresh()->feed_name)->toBe('Updated Starter Feed');

    $this->actingAs(inertiaInventoryUser())
        ->delete(route('inertia.feeds.destroy', $feed))
        ->assertRedirect(route('inertia.feeds.index'));

    expect(Feed::count())->toBe(0)
        ->and(FeedPurchase::count())->toBe(0)
        ->and(CompanyBalance::where('type', 'feed')->count())->toBe(0);
});

it('rolls back a feed purchase when balance writing fails', function () {
    $this->mock(FinancialBalanceService::class, function ($mock): void {
        $mock->shouldReceive('recordFeedPurchaseBalances')->once()->andThrow(new RuntimeException('balance failed'));
    });

    $this->actingAs(inertiaInventoryUser())->withoutExceptionHandling();

    expect(fn () => $this->post(route('inertia.feeds.store'), inertiaFeedPayload([
        'image_file' => UploadedFile::fake()->image('feed.png'),
    ])))->toThrow(RuntimeException::class);

    expect(Feed::count())->toBe(0)
        ->and(FeedPurchase::count())->toBe(0)
        ->and(CompanyBalance::where('type', 'feed')->count())->toBe(0)
        ->and(Storage::disk('public')->files('feeds'))->toBe([]);
});

it('forbids inertia inventory mutations for users without privileges', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inertia.chick-sales.store'), inertiaSalePayload())
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('inertia.chick-purchases.store'), inertiaChickPurchasePayload())
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('inertia.feeds.store'), inertiaFeedPayload())
        ->assertForbidden();
});

it('ignores arbitrary inventory sort columns', function () {
    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.chick-sales.index', [
            'sort' => 'drop table chicken_sales',
            'direction' => 'asc',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ChickSales/Index')
            ->where('filters.sort', 'id')
            ->where('filters.direction', 'asc')
        );

    $this->actingAs(inertiaInventoryUser())
        ->get(route('inertia.feeds.index', [
            'sort' => 'drop table feeds',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Feeds/Index')
            ->where('filters.sort', 'id')
        );
});

it('keeps blade sale purchase and feed store routes working', function () {
    $this->actingAs(inertiaInventoryUser())
        ->post(route('sale.store'), inertiaSalePayload())
        ->assertRedirect(route('sale.index'));

    $this->assertDatabaseHas('chicken_sales', [
        'manual_number' => 'M-123',
        'total_price' => 100000,
    ]);

    $this->actingAs(inertiaInventoryUser())
        ->post(route('purchase.store'), inertiaChickPurchasePayload())
        ->assertRedirect(route('purchase.index'));

    $this->assertDatabaseHas('chick_purchases', [
        'quantity' => 1000,
        'company_id' => 1,
    ]);

    $this->actingAs(inertiaInventoryUser())
        ->post(route('feed.store'), inertiaFeedPayload([
            'feed_name' => 'Blade Starter Feed',
        ]))
        ->assertRedirect(route('feed.index'));

    $this->assertDatabaseHas('feeds', [
        'feed_name' => 'Blade Starter Feed',
    ]);
});

it('does not register an inertia feed edit page', function () {
    expect(Route::has('inertia.feeds.edit'))->toBeFalse();
});
