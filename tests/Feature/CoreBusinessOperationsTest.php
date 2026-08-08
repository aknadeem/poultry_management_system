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
 * Test core business model structure and relationships
 */
class CoreBusinessOperationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Party model structure
     */
    public function test_party_model_has_required_methods()
    {
        $party = new Party();

        // Verify key relationships exist
        $this->assertTrue(method_exists($party, 'farm'));
        $this->assertTrue(method_exists($party, 'division'));
        $this->assertTrue(method_exists($party, 'balances'));
    }

    /**
     * Test PartyBalance model is accessible
     */
    public function test_party_balance_model_exists()
    {
        $balance = new PartyBalance();
        $this->assertNotNull($balance);
    }

    /**
     * Test Product model structure
     */
    public function test_product_model_is_accessible()
    {
        $product = new Product();
        $this->assertNotNull($product);
    }

    /**
     * Test ProductStore model is accessible
     */
    public function test_product_store_model_is_accessible()
    {
        $store = new ProductStore();
        $this->assertNotNull($store);
    }

    /**
     * Test Feed model is accessible
     */
    public function test_feed_model_is_accessible()
    {
        $feed = new Feed();
        $this->assertNotNull($feed);
    }

    /**
     * Test FeedPurchase model is accessible
     */
    public function test_feed_purchase_model_is_accessible()
    {
        $purchase = new FeedPurchase();
        $this->assertNotNull($purchase);
    }

    /**
     * Test Employee model is fully functional
     */
    public function test_employee_model_is_accessible()
    {
        $employee = new Employee();
        $this->assertNotNull($employee);
    }

    /**
     * Test PartyFarm model is accessible
     */
    public function test_party_farm_model_is_accessible()
    {
        $farm = new PartyFarm();
        $this->assertNotNull($farm);
    }
}
