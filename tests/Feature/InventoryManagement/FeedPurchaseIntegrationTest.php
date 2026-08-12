<?php

use App\Models\CompanyBalance;
use App\Models\Feed;
use App\Models\FeedPurchase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(UserSeeder::class);
    $this->actingAs(User::query()->firstOrFail());

    $timestamps = [
        'created_at' => '2026-06-01 00:00:00',
        'updated_at' => '2026-06-01 00:00:00',
    ];

    DB::table('business_types')->insert([
        'id' => 1, 'name' => 'Supplier', 'slug' => 'supplier', ...$timestamps,
    ]);
    DB::table('parties')->insert([
        'id' => 1,
        'is_vendor' => true,
        'name' => 'Fixture Vendor',
        'cnic_no' => '1000000000000',
        'contact_no' => '03000000000',
        ...$timestamps,
    ]);
    DB::table('party_companies')->insert([
        'id' => 1,
        'party_id' => 1,
        'business_type_id' => 1,
        'company_name' => 'Fixture Company',
        ...$timestamps,
    ]);
    DB::table('feed_categories')->insert([
        'id' => 1,
        'name' => 'Starter',
        'slug' => 'starter',
        ...$timestamps,
    ]);
});

test('feed store creates feed purchase and company balance', function () {
    $this->withoutExceptionHandling();

    $this->post(route('feed.store'), [
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
    ])->assertRedirect(route('feed.index'));

    expect(Feed::count())->toBe(1);
    expect(FeedPurchase::count())->toBe(1);
    expect(CompanyBalance::where('type', 'feed')->count())->toBe(1);
});

test('feed destroy removes purchases and balances', function () {
    $this->withoutExceptionHandling();

    $this->post(route('feed.store'), [
        'feed_name' => 'Demo Starter Feed',
        'purchase_date' => '2026-08-01',
        'feed_category_id' => 1,
        'company_id' => 1,
        'quantity' => 50,
        'price' => 3000,
        'total_price' => 150000,
        'bilty_charges' => 500,
        'per_bag_discount' => 0,
        'sale_order_number' => 'SO-1',
        'delivery_order_number' => 'DO-1',
    ])->assertRedirect();

    $feed = Feed::firstOrFail();

    $this->delete(route('feed.destroy', $feed->id))
        ->assertRedirect(route('feed.index'));

    expect(Feed::count())->toBe(0);
    expect(FeedPurchase::count())->toBe(0);
    expect(CompanyBalance::where('type', 'feed')->count())->toBe(0);
});
