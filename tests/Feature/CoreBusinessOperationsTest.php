<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\PartyBalance;
use App\Models\PartyFarm;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Feed;
use App\Models\FeedPurchase;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test core business operations and workflows
 */
class CoreBusinessOperationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a party (supplier/customer)
     */
    public function test_can_create_party()
    {
        $party = Party::factory()->create([
            'name' => 'Test Farm Supplier',
            'phone' => '03001234567',
        ]);

        $this->assertDatabaseHas('parties', [
            'name' => 'Test Farm Supplier',
        ]);
    }

    /**
     * Test creating and tracking party balance
     */
    public function test_party_balance_can_be_tracked()
    {
        $party = Party::factory()->create();
        
        $balance = PartyBalance::create([
            'party_id' => $party->id,
            'opening_balance' => 10000,
            'total_purchase' => 5000,
            'total_payment' => 2000,
        ]);

        $this->assertDatabaseHas('party_balances', [
            'party_id' => $party->id,
            'opening_balance' => 10000,
        ]);
    }

    /**
     * Test product management
     */
    public function test_can_create_product()
    {
        $product = Product::factory()->create([
            'name' => 'Broiler Chicken Feed',
            'code' => 'BCF-001',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Broiler Chicken Feed',
        ]);
    }

    /**
     * Test product store/inventory tracking
     */
    public function test_product_store_inventory_operations()
    {
        $product = Product::factory()->create();
        
        $store = ProductStore::create([
            'product_id' => $product->id,
            'opening_stock' => 100,
            'total_purchase' => 50,
            'total_sale' => 20,
        ]);

        $this->assertDatabaseHas('product_stores', [
            'product_id' => $product->id,
            'opening_stock' => 100,
        ]);
    }

    /**
     * Test feed purchases
     */
    public function test_can_create_feed_purchase()
    {
        $party = Party::factory()->create();
        $feed = Feed::factory()->create();

        $purchase = FeedPurchase::create([
            'feed_id' => $feed->id,
            'party_id' => $party->id,
            'quantity' => 500,
            'unit_price' => 150,
        ]);

        $this->assertDatabaseHas('feed_purchases', [
            'feed_id' => $feed->id,
            'party_id' => $party->id,
        ]);
    }

    /**
     * Test employee operations
     */
    public function test_can_manage_employees()
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Ali',
            'last_name' => 'Khan',
        ]);

        $this->assertDatabaseHas('employees', [
            'first_name' => 'Ali',
            'last_name' => 'Khan',
        ]);

        // Test update
        $employee->update(['first_name' => 'Ahmed']);
        $this->assertEquals('Ahmed', $employee->fresh()->first_name);
    }

    /**
     * Test party farm relationship
     */
    public function test_party_farm_management()
    {
        $party = Party::factory()->create();
        
        $farm = PartyFarm::factory()->create([
            'party_id' => $party->id,
            'farm_name' => 'North Farm',
        ]);

        $this->assertDatabaseHas('party_farms', [
            'party_id' => $party->id,
            'farm_name' => 'North Farm',
        ]);
    }
}
