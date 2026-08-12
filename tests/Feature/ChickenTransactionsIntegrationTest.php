<?php

use App\Models\User;
use App\Models\Party;
use App\Models\Broker;
use App\Models\PartyFarm;
use App\Models\ChickGrade;
use App\Models\PartyCompany;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\CompanyBalance;
use App\Models\PartyBalance;
use App\Models\BrokerBalance;
use App\Models\PartyFarmChickHistory;
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

    // Seed Business Type
    DB::table('business_types')->insert([
        'id' => 1,
        'name' => 'Supplier',
        'slug' => 'supplier',
        ...$timestamps,
    ]);

    // Seed Parties
    $this->customer = Party::create([
        'name' => 'Test Customer',
        'is_customer' => true,
        'cnic_no' => '1234567890123',
        'contact_no' => '03001234567',
        'is_active' => true,
    ]);

    $this->vendor = Party::create([
        'name' => 'Test Vendor',
        'is_vendor' => true,
        'cnic_no' => '1234567890124',
        'contact_no' => '03001234568',
        'is_active' => true,
    ]);

    // Seed Party Farm
    $this->partyFarm = PartyFarm::create([
        'party_id' => $this->customer->id,
        'farm_name' => 'Test Farm',
        'farm_code' => 'TF-1',
        'farm_capacity' => 5000,
        'folk_quantity' => 0,
        'is_occupied' => 0,
        'is_active' => true,
    ]);

    // Seed Party Company
    $this->company = PartyCompany::create([
        'party_id' => $this->vendor->id,
        'business_type_id' => 1,
        'company_name' => 'Test Vendor Company',
        'company_address' => 'Vendor Address',
        'is_active' => true,
    ]);

    // Seed Chick Grade
    $this->chickGrade = ChickGrade::create([
        'name' => 'Grade A',
    ]);

    // Seed Broker
    $this->broker = Broker::create([
        'broker_code' => 'B-100',
        'name' => 'Test Broker',
        'is_active' => true,
    ]);
});

test('it handles store chick purchase correctly updating farm and balances', function () {
    $this->withoutExceptionHandling();

    $payload = [
        'customer_id' => $this->customer->id,
        'purchase_date' => '2026-08-11',
        'chick_grade_id' => $this->chickGrade->id,
        'company_id' => $this->company->id,
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
        'customer_farm_id' => $this->partyFarm->id,
        'vendor_id' => $this->vendor->id,
    ];

    $response = $this->post(route('purchase.store'), $payload);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('purchase.index'));

    $this->assertDatabaseHas('chick_purchases', [
        'customer_id' => $this->customer->id,
        'company_id' => $this->company->id,
        'quantity' => 1000,
    ]);

    // Check Party Farm occupies
    $this->partyFarm->refresh();
    expect($this->partyFarm->folk_quantity)->toBe(1000);
    expect((int)$this->partyFarm->is_occupied)->toBe(1);

    // Check History record
    $this->assertDatabaseHas('party_farm_chick_histories', [
        'party_farm_id' => $this->partyFarm->id,
        'quantity' => 1000,
    ]);

    // Check Company Balance
    $this->assertDatabaseHas('company_balances', [
        'type' => 'chick_purchase',
        'company_id' => $this->company->id,
        'total_amount' => 49500,
        'dr' => 49500,
    ]);

    // Check Party Balance
    $this->assertDatabaseHas('party_balances', [
        'party_id' => $this->customer->id,
        'total_amount' => 49500,
        'remaining_amount' => 49500,
    ]);
});

test('it handles update and delete chick purchase correctly', function () {
    $this->withoutExceptionHandling();

    // 1. Create a purchase manually
    $purchase = ChickPurchase::create([
        'customer_id' => $this->customer->id,
        'purchase_date' => '2026-08-11',
        'chick_grade_id' => $this->chickGrade->id,
        'company_id' => $this->company->id,
        'chick_entry_age' => 1,
        'quantity' => 1000,
        'price' => 50,
        'total_price' => 50000,
        'addedby' => $this->user->id,
    ]);

    // Setup history and occupancy
    $history = PartyFarmChickHistory::create([
        'party_farm_id' => $this->partyFarm->id,
        'chick_purchase_id' => $purchase->id,
        'quantity' => 1000,
        'entry_date' => '2026-08-11',
    ]);
    $this->partyFarm->update([
        'folk_quantity' => 1000,
        'is_occupied' => 1,
    ]);

    // Create balances
    CompanyBalance::create([
        'type' => 'chick_purchase',
        'company_id' => $this->company->id,
        'model_id' => $purchase->id,
        'total_amount' => 50000,
        'dr' => 50000,
    ]);
    PartyBalance::create([
        'party_id' => $this->customer->id,
        'total_amount' => 50000,
        'narration' => "chicks purchases for you (Purchase #{$purchase->id})",
        'transaction_date' => '2026-08-11',
    ]);

    // 2. Perform Update via PUT route
    $updatePayload = [
        'vehicle_number' => 'Updated-1',
        'driver_name' => 'Updated Ahmad',
        'driver_contact' => '1234567890',
        'purchase_date' => '2026-08-12',
        'company_id' => $this->company->id,
        'quantity' => 800,
        'chick_weight' => 50,
        'price' => 45,
        'discount_amount' => 0,
        'discount_percentage' => 0,
        'total_price' => 36000,
    ];

    $response = $this->put(route('purchase.update', $purchase->id), $updatePayload);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('purchase.index'));

    // Verify updated database entries
    $this->assertDatabaseHas('chick_purchases', [
        'id' => $purchase->id,
        'quantity' => 800,
        'total_price' => 36000,
    ]);

    $this->partyFarm->refresh();
    expect($this->partyFarm->folk_quantity)->toBe(800);

    $this->assertDatabaseHas('company_balances', [
        'type' => 'chick_purchase',
        'model_id' => $purchase->id,
        'total_amount' => 36000,
    ]);

    $this->assertDatabaseHas('party_balances', [
        'party_id' => $this->customer->id,
        'total_amount' => 36000,
        'narration' => "chicks purchases for you (Purchase #{$purchase->id})",
    ]);

    // 3. Delete Chick Purchase
    $deleteResponse = $this->delete(route('purchase.destroy', $purchase->id));
    $deleteResponse->assertSessionHasNoErrors();
    $deleteResponse->assertRedirect(route('purchase.index'));

    $this->assertSoftDeleted('chick_purchases', [
        'id' => $purchase->id,
    ]);

    // Verify balances deleted (they are soft deleted in db)
    $this->assertSoftDeleted('company_balances', [
        'type' => 'chick_purchase',
        'model_id' => $purchase->id,
    ]);
    $this->assertSoftDeleted('party_balances', [
        'party_id' => $this->customer->id,
        'narration' => "chicks purchases for you (Purchase #{$purchase->id})",
    ]);
    
    $this->partyFarm->refresh();
    expect($this->partyFarm->folk_quantity)->toBe(0);
    expect((int)$this->partyFarm->is_occupied)->toBe(0);
});

test('it handles store, update, and delete chicken sale correctly', function () {
    $this->withoutExceptionHandling();

    $storePayload = [
        'manual_number' => 'M-123',
        'sale_date' => '2026-08-11',
        'customer_id' => $this->customer->id,
        'broker_id' => $this->broker->id,
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
    ];

    // 1. Store Chicken Sale
    $response = $this->post(route('sale.store'), $storePayload);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('sale.index'));

    $sale = ChickenSale::where('manual_number', 'M-123')->firstOrFail();

    $this->assertDatabaseHas('party_balances', [
        'party_id' => $this->customer->id,
        'total_amount' => 100000,
        'narration' => "Chicken sale balance (Sale #{$sale->id})",
    ]);

    $this->assertDatabaseHas('broker_balances', [
        'broker_id' => $this->broker->id,
        'total_amount' => 2000,
        'narration' => "chicken sale commession (Sale #{$sale->id})",
    ]);

    // 2. Update Chicken Sale
    $updatePayload = [
        'vehicle_number' => 'TRK-999-U',
        'driver_name' => 'Wali Updated',
        'driver_contact' => '1234567',
        'sale_date' => '2026-08-12',
        'customer_id' => $this->customer->id,
        'total_weight' => 450,
        'per_kg_price' => 200,
        'discount_amount' => 1000,
        'discount_percentage' => 1,
        'total_price' => 89000,
        'broker_commission' => 1800,
    ];

    $updateResponse = $this->put(route('sale.update', $sale->id), $updatePayload);
    $updateResponse->assertSessionHasNoErrors();
    $updateResponse->assertRedirect(route('sale.index'));

    $this->assertDatabaseHas('chicken_sales', [
        'id' => $sale->id,
        'total_price' => 89000,
    ]);

    $this->assertDatabaseHas('party_balances', [
        'party_id' => $this->customer->id,
        'total_amount' => 89000,
        'narration' => "Chicken sale balance (Sale #{$sale->id})",
    ]);

    $this->assertDatabaseHas('broker_balances', [
        'broker_id' => $this->broker->id,
        'total_amount' => 1800,
        'narration' => "chicken sale commession (Sale #{$sale->id})",
    ]);

    // 3. Delete Chicken Sale
    $deleteResponse = $this->delete(route('sale.destroy', $sale->id));
    $deleteResponse->assertSessionHasNoErrors();
    $deleteResponse->assertRedirect(route('sale.index'));

    $this->assertSoftDeleted('chicken_sales', [
        'id' => $sale->id,
    ]);

    $this->assertSoftDeleted('party_balances', [
        'party_id' => $this->customer->id,
        'narration' => "Chicken sale balance (Sale #{$sale->id})",
    ]);

    $this->assertSoftDeleted('broker_balances', [
        'broker_id' => $this->broker->id,
        'narration' => "chicken sale commession (Sale #{$sale->id})",
    ]);
});
