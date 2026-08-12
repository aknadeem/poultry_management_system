<?php

use App\Models\Broker;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\PartyFarm;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DemoSeeder;

it('seeds enough demo data to exercise the main modules', function () {
    $this->seed(DemoSeeder::class);

    expect(User::count())->toBeGreaterThanOrEqual(3);
    expect(Party::count())->toBeGreaterThanOrEqual(10);
    expect(PartyFarm::count())->toBeGreaterThanOrEqual(6);
    expect(PartyCompany::count())->toBeGreaterThanOrEqual(4);
    expect(Broker::count())->toBeGreaterThanOrEqual(2);
    expect(Product::count())->toBeGreaterThanOrEqual(16);
    expect(Feed::count())->toBeGreaterThanOrEqual(6);
    expect(ChickPurchase::count())->toBeGreaterThanOrEqual(12);
    expect(ChickenSale::count())->toBeGreaterThanOrEqual(8);
    expect(Expense::count())->toBeGreaterThanOrEqual(15);
    expect(Employee::count())->toBeGreaterThanOrEqual(6);
});
