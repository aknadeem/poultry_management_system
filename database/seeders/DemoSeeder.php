<?php

namespace Database\Seeders;

use App\Actions\ChickenModule\StoreChickPurchaseAction;
use App\Actions\ChickenModule\StoreChickenPurchaseAction;
use App\Actions\ChickenModule\StoreChickenSaleAction;
use App\Helpers\Constant;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\ChickGrade;
use App\Models\City;
use App\Models\CompanyBalance;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeLevel;
use App\Models\EmployeeType;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Feed;
use App\Models\FeedCategory;
use App\Models\FeedPurchase;
use App\Models\FeedSubcategory;
use App\Models\Party;
use App\Models\PartyBalanceLimit;
use App\Models\PartyCompany;
use App\Models\PartyFarm;
use App\Models\PersonalFarm;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Models\ProductStore;
use App\Models\ProductType;
use App\Models\Province;
use App\Models\User;
use App\Models\UserRole;
use App\Models\VaccinationGroup;
use App\Models\VaccinationSchedule;
use App\Models\VendorType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Full-application demo data. Intended for a fresh database.
 *
 *   php artisan db:seed --class=DemoSeeder
 *
 * Or: SEED_DEMO=true php artisan migrate:fresh --seed
 *
 * Login: admin@admin.com / 1234
 */
class DemoSeeder extends Seeder
{
    private int $userId;

    private Carbon $now;

    public function run(): void
    {
        $this->now = Carbon::now();

        $this->seedBase();
        $this->seedLookups();
        $this->seedPeopleAndParties();
        $this->seedCatalog();
        $this->seedTransactions();
        $this->seedEmployeesAndLimits();

        $this->info('Demo data ready. Login: admin@admin.com / 1234');
    }

    private function seedBase(): void
    {
        if (Country::count() === 0) {
            $this->call(CountryProvinceSeeder::class);
        }
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }
        if (ExpenseCategory::count() === 0) {
            $this->call(ExpenseCategorySeed::class);
        }

        $adminRoleId = UserRole::where('slug', 'super-admin')->value('id') ?? 1;
        $admin = User::where('email', 'admin@admin.com')->first();
        $this->userId = $admin?->id ?? 1;

        $extraUsers = [
            ['name' => 'Farm Admin', 'email' => 'admin.user@example.com', 'slug' => 'admin'],
            ['name' => 'Operations HOD', 'email' => 'hod@example.com', 'slug' => 'hod'],
        ];
        foreach ($extraUsers as $extra) {
            $roleId = UserRole::where('slug', $extra['slug'])->value('id') ?? $adminRoleId;
            User::firstOrCreate(
                ['email' => $extra['email']],
                [
                    'name' => $extra['name'],
                    'user_role_id' => $roleId,
                    'password' => Hash::make('1234'),
                    'contact_no' => '0300'.str_pad((string) random_int(1000000, 9999999), 7, '0'),
                    'addedby' => $this->userId,
                ]
            );
        }

        $this->info('Base geo, roles, and users seeded');
    }

    private function seedLookups(): void
    {
        $this->upsertNamed(Division::class, [
            ['name' => 'Lahore', 'slug' => 'lahore'],
            ['name' => 'Karachi', 'slug' => 'karachi'],
            ['name' => 'Islamabad', 'slug' => 'islamabad'],
            ['name' => 'Faisalabad', 'slug' => 'faisalabad'],
        ]);
        $this->upsertNamed(BusinessType::class, [
            ['name' => 'Feed Mill', 'slug' => 'feed-mill'],
            ['name' => 'Hatchery', 'slug' => 'hatchery'],
            ['name' => 'Medicine Supplier', 'slug' => 'medicine-supplier'],
        ]);
        $this->upsertNamed(CustomerType::class, [
            ['name' => 'Farm Owner', 'slug' => 'farm-owner'],
            ['name' => 'Trader', 'slug' => 'trader'],
            ['name' => 'Retailer', 'slug' => 'retailer'],
        ]);
        $this->upsertNamed(VendorType::class, [
            ['name' => 'Feed Vendor', 'slug' => 'feed-vendor'],
            ['name' => 'Chick Vendor', 'slug' => 'chick-vendor'],
            ['name' => 'Medicine Vendor', 'slug' => 'medicine-vendor'],
        ]);

        $broiler = FarmType::firstOrCreate(['slug' => 'broiler'], ['name' => 'Broiler']);
        $layer = FarmType::firstOrCreate(['slug' => 'layer'], ['name' => 'Layer']);
        FarmSubtype::firstOrCreate(['slug' => 'open-shed'], ['name' => 'Open Shed', 'farm_type_id' => $broiler->id]);
        FarmSubtype::firstOrCreate(['slug' => 'control-shed'], ['name' => 'Control Shed', 'farm_type_id' => $broiler->id]);
        FarmSubtype::firstOrCreate(['slug' => 'layer-cage'], ['name' => 'Cage System', 'farm_type_id' => $layer->id]);

        foreach (['Grade A' => 'grade-a', 'Grade B' => 'grade-b', 'Grade C' => 'grade-c'] as $name => $slug) {
            ChickGrade::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $starter = FeedCategory::firstOrCreate(['slug' => 'starter'], ['name' => 'Starter']);
        $grower = FeedCategory::firstOrCreate(['slug' => 'grower'], ['name' => 'Grower']);
        FeedSubcategory::firstOrCreate(['slug' => 'starter-crumbles'], ['name' => 'Starter Crumbles', 'feed_category_id' => $starter->id]);
        FeedSubcategory::firstOrCreate(['slug' => 'starter-mash'], ['name' => 'Starter Mash', 'feed_category_id' => $starter->id]);
        FeedSubcategory::firstOrCreate(['slug' => 'grower-pellets'], ['name' => 'Grower Pellets', 'feed_category_id' => $grower->id]);
        FeedSubcategory::firstOrCreate(['slug' => 'grower-mash'], ['name' => 'Grower Mash', 'feed_category_id' => $grower->id]);

        EmployeeType::firstOrCreate(['slug' => 'farm-worker'], ['name' => 'Farm Worker', 'is_Active' => 1]);
        EmployeeType::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor', 'is_Active' => 1]);
        EmployeeType::firstOrCreate(['slug' => 'driver'], ['name' => 'Driver', 'is_Active' => 1]);
        EmployeeLevel::firstOrCreate(['slug' => 'junior'], ['name' => 'Junior', 'is_Active' => 1]);
        EmployeeLevel::firstOrCreate(['slug' => 'senior'], ['name' => 'Senior', 'is_Active' => 1]);
        EmployeeAllowance::firstOrCreate(['name' => 'Transport'], ['allowance_amount' => 5000, 'is_Active' => 1]);
        EmployeeAllowance::firstOrCreate(['name' => 'Housing'], ['allowance_amount' => 8000, 'is_Active' => 1]);

        ProductStore::firstOrCreate(['store_code' => 'STORE-MAIN'], [
            'store_name' => 'Main Warehouse',
            'store_type' => 'warehouse',
            'store_area' => 1200,
            'total_racks' => 24,
            'is_active' => 1,
            'addedby' => $this->userId,
        ]);
        ProductStore::firstOrCreate(['store_code' => 'STORE-COLD'], [
            'store_name' => 'Cold Store',
            'store_type' => 'cold',
            'store_area' => 400,
            'total_racks' => 8,
            'is_active' => 1,
            'addedby' => $this->userId,
        ]);

        $this->upsertNamed(ProductType::class, [
            ['name' => 'Vaccine', 'slug' => 'vaccine'],
            ['name' => 'Antibiotic', 'slug' => 'antibiotic'],
            ['name' => 'Vitamin', 'slug' => 'vitamin'],
        ]);
        $this->upsertNamed(VaccinationGroup::class, [
            ['name' => 'Day 1', 'slug' => 'day-1'],
            ['name' => 'Week 1', 'slug' => 'week-1'],
            ['name' => 'Week 2', 'slug' => 'week-2'],
        ]);

        $this->info('Lookup tables seeded');
    }

    private function seedPeopleAndParties(): void
    {
        $countryId = Country::query()->value('id');
        $provinceId = Province::query()->value('id');
        $cityId = City::query()->value('id');
        $customerTypeId = CustomerType::query()->value('id');
        $vendorTypeId = VendorType::query()->value('id');
        $customerDivisionId = Division::query()->value('id');
        $vendorDivisionId = Division::query()->skip(1)->value('id') ?? $customerDivisionId;
        $farmTypeId = FarmType::query()->value('id');
        $farmSubtypeId = FarmSubtype::query()->value('id');
        $businessTypeIds = BusinessType::query()->pluck('id')->all();

        ConductPerson::firstOrCreate(['person_code' => 'CP-001'], [
            'name' => 'Imran Conduct',
            'guardian_name' => 'Akbar Ali',
            'cnic_no' => '3520211111111',
            'email' => 'imran.conduct@example.com',
            'contact_number' => '03011111111',
            'country_id' => $countryId,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'address' => 'Lahore',
            'is_active' => 1,
            'addedby' => $this->userId,
        ]);
        ConductPerson::firstOrCreate(['person_code' => 'CP-002'], [
            'name' => 'Sajid Conduct',
            'guardian_name' => 'Nazeer Ahmed',
            'cnic_no' => '3520211111112',
            'email' => 'sajid.conduct@example.com',
            'contact_number' => '03011111112',
            'country_id' => $countryId,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'address' => 'Faisalabad',
            'is_active' => 1,
            'addedby' => $this->userId,
        ]);

        foreach ([
            ['B-101', 'Rashid Broker'],
            ['B-102', 'Kamran Broker'],
            ['B-103', 'Naveed Broker'],
        ] as $i => [$code, $name]) {
            Broker::firstOrCreate(['broker_code' => $code], [
                'name' => $name,
                'guardian_name' => 'Demo Guardian',
                'cnic_no' => '35202900000'.(10 + $i),
                'email' => Str::slug($code).'@example.com',
                'contact_no' => '0321'.str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                'opening_balance' => 0,
                'country_id' => $countryId,
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'address' => 'Poultry Market',
                'is_active' => 1,
                'addedby' => $this->userId,
            ]);
        }

        $customerNames = [
            'Al-Noor Poultry Farm',
            'Sadiq Broilers',
            'Chenab Layer Farm',
            'Green Valley Poultry',
            'Pak Broiler Traders',
            'Ravi Farm House',
        ];
        $vendorNames = [
            'National Feed Mills',
            'Big Bird Hatchery',
            'Hi-Tech Pharma',
            'Punjab Feed House',
        ];
        $dualNames = [
            'United Poultry Traders',
            'City Chick Exchange',
        ];

        foreach ($customerNames as $i => $name) {
            $this->makeParty($name, true, false, $i + 1, $countryId, $provinceId, $cityId, $customerTypeId, $vendorTypeId, $customerDivisionId, $vendorDivisionId);
        }
        foreach ($vendorNames as $i => $name) {
            $this->makeParty($name, false, true, $i + 20, $countryId, $provinceId, $cityId, $customerTypeId, $vendorTypeId, $customerDivisionId, $vendorDivisionId);
        }
        foreach ($dualNames as $i => $name) {
            $this->makeParty($name, true, true, $i + 40, $countryId, $provinceId, $cityId, $customerTypeId, $vendorTypeId, $customerDivisionId, $vendorDivisionId);
        }

        $customers = Party::where('is_customer', true)->orderBy('id')->get();
        foreach ($customers->take(6) as $i => $party) {
            PartyFarm::firstOrCreate(['farm_code' => 'FARM-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'party_id' => $party->id,
                'farm_type_id' => $farmTypeId,
                'farm_subtype_id' => $farmSubtypeId,
                'farm_name' => $party->name.' Shed',
                'farm_address' => 'Farm Road, '.$party->name,
                'farm_area' => 2.5 + $i,
                'farm_capacity' => 5000 + ($i * 500),
                'folk_quantity' => 0,
                'is_occupied' => 0,
                'is_active' => 1,
                'addedby' => $this->userId,
            ]);
        }

        $vendors = Party::where('is_vendor', true)->orderBy('id')->get();
        foreach ($vendors->take(4) as $i => $party) {
            PartyCompany::firstOrCreate(['company_code' => 'CO-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'party_id' => $party->id,
                'business_type_id' => $businessTypeIds[$i % count($businessTypeIds)],
                'company_name' => $party->name,
                'company_address' => 'Industrial Area',
                'is_active' => 1,
                'addedby' => $this->userId,
            ]);
        }

        PersonalFarm::firstOrCreate(['farm_code' => 'PF-001'], [
            'farm_type_id' => $farmTypeId,
            'farm_subtype_id' => $farmSubtypeId,
            'farm_name' => 'Own Broiler Farm',
            'farm_address' => 'Sheikhupura Road',
            'farm_area' => 4.0,
            'farm_capacity' => 8000,
            'is_active' => 1,
            'country_id' => $countryId,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'addedby' => $this->userId,
        ]);
        PersonalFarm::firstOrCreate(['farm_code' => 'PF-002'], [
            'farm_type_id' => FarmType::where('slug', 'layer')->value('id') ?? $farmTypeId,
            'farm_subtype_id' => FarmSubtype::where('slug', 'layer-cage')->value('id') ?? $farmSubtypeId,
            'farm_name' => 'Own Layer Farm',
            'farm_address' => 'Kasur Road',
            'farm_area' => 3.0,
            'farm_capacity' => 6000,
            'is_active' => 1,
            'country_id' => $countryId,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'addedby' => $this->userId,
        ]);

        $this->info('Parties, farms, companies, brokers seeded');
    }

    private function seedCatalog(): void
    {
        $companies = PartyCompany::query()->orderBy('id')->get();
        $storeId = ProductStore::query()->value('id');
        $productTypeId = ProductType::query()->value('id');
        $vaccinationGroupId = VaccinationGroup::query()->value('id');
        $feedCategoryId = FeedCategory::query()->value('id');
        $feedSubcategoryId = FeedSubcategory::query()->value('id');

        $categories = [];
        $categoryNames = ['Vaccines', 'Antibiotics', 'Vitamins', 'Disinfectants', 'Feed Additives'];
        foreach ($categoryNames as $i => $name) {
            $company = $companies[$i % max($companies->count(), 1)] ?? $companies->first();
            $categories[] = ProductCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'company_id' => $company?->id,
                    'is_active' => 1,
                ]
            );
        }

        $medicineNames = [
            'ND Vaccine Live', 'IBD Vaccine', 'IB Vaccine', 'Enrofloxacin 10%',
            'Amoxicillin 20%', 'Tylosin 50%', 'Vitamin AD3E', 'Vitamin C Plus',
            'Electrolyte Mix', 'Liver Tonic', 'Disinfectant Iodine', 'Formalin 37%',
        ];
        foreach ($medicineNames as $i => $name) {
            $category = $categories[$i % count($categories)];
            Product::firstOrCreate(['product_code' => 'PRD-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'product_name' => $name,
                'product_group' => Constant::PRODUCT_GROUP['Medicine'],
                'party_company_id' => $category->company_id,
                'product_category_id' => $category->id,
                'product_type_id' => $productTypeId,
                'vaccination_group_id' => $vaccinationGroupId,
                'product_store_id' => $storeId,
                'quantity' => 200 + ($i * 10),
                'total_quantity' => 200 + ($i * 10),
                'remaining_quantity' => 180 + ($i * 8),
                'purchase_price' => 80 + ($i * 5),
                'sale_price' => 110 + ($i * 6),
                'mrp_price' => 130 + ($i * 6),
                'is_active' => 1,
                'entry_date' => $this->now->copy()->subDays(40 - $i)->toDateString(),
                'addedby' => $this->userId,
            ]);
        }

        $feedProductNames = ['Starter Crumble 50kg', 'Grower Pellet 50kg', 'Finisher Mash 50kg', 'Layer Mash 50kg'];
        foreach ($feedProductNames as $i => $name) {
            $category = $categories[count($categories) - 1];
            Product::firstOrCreate(['product_code' => 'FEEDP-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'product_name' => $name,
                'product_group' => Constant::PRODUCT_GROUP['Feed'],
                'party_company_id' => $category->company_id,
                'product_category_id' => $category->id,
                'product_store_id' => $storeId,
                'quantity' => 500,
                'total_quantity' => 500,
                'remaining_quantity' => 420,
                'purchase_price' => 3200,
                'sale_price' => 3450,
                'is_active' => 1,
                'addedby' => $this->userId,
            ]);
        }

        $feedNames = [
            'Starter Crumble Gold',
            'Starter Mash Plus',
            'Grower Pellet Max',
            'Grower Mash Standard',
            'Finisher High Energy',
            'Layer Peak Mash',
        ];
        foreach ($feedNames as $i => $name) {
            Feed::firstOrCreate(['feed_code' => 'FEED-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'feed_name' => $name,
                'feed_category_id' => $feedCategoryId,
                'feed_subcategory_id' => $feedSubcategoryId,
                'total_quantity' => 0,
                'remaining_quantity' => 0,
                'is_active' => 1,
                'addedby' => $this->userId,
            ]);
        }

        $this->info('Product and feed catalog seeded');
    }

    private function seedTransactions(): void
    {
        $customers = Party::where('is_customer', true)->orderBy('id')->get();
        $vendors = Party::where('is_vendor', true)->orderBy('id')->get();
        $farms = PartyFarm::query()->orderBy('id')->get();
        $companies = PartyCompany::query()->orderBy('id')->get();
        $grades = ChickGrade::query()->orderBy('id')->get();
        $brokers = Broker::query()->orderBy('id')->get();
        $feeds = Feed::query()->orderBy('id')->get();
        $products = Product::query()->orderBy('id')->get();
        $categories = ProductCategory::query()->orderBy('id')->get();
        $divisions = Division::query()->orderBy('id')->get();
        $expenseCategories = ExpenseCategory::query()->orderBy('id')->get();
        $personalFarmId = PersonalFarm::query()->value('id');

        $chickPurchaseAction = app(StoreChickPurchaseAction::class);
        $chickenPurchaseAction = app(StoreChickenPurchaseAction::class);
        $chickenSaleAction = app(StoreChickenSaleAction::class);

        for ($i = 0; $i < 12; $i++) {
            $customer = $customers[$i % $customers->count()];
            $vendor = $vendors[$i % $vendors->count()];
            if ($customer->id === $vendor->id) {
                $vendor = $vendors[($i + 1) % $vendors->count()];
            }
            $farm = $farms[$i % $farms->count()];
            $company = $companies[$i % $companies->count()];
            $grade = $grades[$i % $grades->count()];
            $qty = 800 + ($i * 50);
            $price = 48 + ($i % 5);
            $total = $qty * $price;
            $date = $this->now->copy()->subDays(80 - ($i * 6))->toDateString();

            $chickPurchaseAction->execute([
                'customer_id' => $customer->id,
                'vendor_id' => $vendor->id,
                'purchase_date' => $date,
                'chick_grade_id' => $grade->id,
                'company_id' => $company->id,
                'chick_entry_age' => 1,
                'chick_weight' => 42 + ($i % 6),
                'quantity' => $qty,
                'price' => $price,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'total_price' => $total,
                'customer_farm_id' => $farm->id,
                'vehicle_number' => 'LES-'.(1000 + $i),
                'driver_name' => 'Driver '.($i + 1),
                'driver_contact' => '0302'.str_pad((string) (2000000 + $i), 7, '0', STR_PAD_LEFT),
            ], null, $this->userId);
        }

        for ($i = 0; $i < 8; $i++) {
            $customer = $customers[$i % $customers->count()];
            $broker = $brokers[$i % $brokers->count()];
            $weight = 1200 + ($i * 80);
            $perKg = 310 + ($i * 3);
            $total = $weight * $perKg;
            $date = $this->now->copy()->subDays(70 - ($i * 7))->toDateString();

            $chickenSaleAction->execute([
                'manual_number' => 'CS-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'sale_date' => $date,
                'customer_id' => $customer->id,
                'broker_id' => $broker->id,
                'total_weight' => $weight,
                'first_weight' => $weight + 40,
                'second_weight' => 40,
                'net_weight' => $weight,
                'per_kg_price' => $perKg,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'total_price' => $total,
                'broker_commission' => 1500 + ($i * 100),
                'vehicle_number' => 'RWP-'.(2000 + $i),
                'driver_name' => 'Sale Driver '.($i + 1),
                'driver_contact' => '0333'.str_pad((string) (3000000 + $i), 7, '0', STR_PAD_LEFT),
            ], null, $this->userId);
        }

        for ($i = 0; $i < 6; $i++) {
            $company = $companies[$i % $companies->count()];
            $grade = $grades[$i % $grades->count()];
            $qty = 400 + ($i * 40);
            $price = 280 + $i;
            $total = $qty * $price;
            $date = $this->now->copy()->subDays(60 - ($i * 8))->toDateString();

            $chickenPurchaseAction->execute([
                'purchase_date' => $date,
                'chick_grade_id' => $grade->id,
                'company_id' => $company->id,
                'chick_weight' => 1.6 + ($i * 0.1),
                'quantity' => $qty,
                'price' => $price,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'total_price' => $total,
                'personal_farm_id' => $personalFarmId,
                'vehicle_number' => 'LHR-'.(3000 + $i),
                'driver_name' => 'Meat Driver '.($i + 1),
                'driver_contact' => '0345'.str_pad((string) (4000000 + $i), 7, '0', STR_PAD_LEFT),
            ], null, $this->userId);
        }

        foreach ($feeds as $i => $feed) {
            $company = $companies[$i % $companies->count()];
            $qty = 100 + ($i * 20);
            $price = 3100 + ($i * 50);
            $total = $qty * $price;
            $date = $this->now->copy()->subDays(50 - ($i * 5))->toDateString();

            $purchase = FeedPurchase::create([
                'feed_id' => $feed->id,
                'company_id' => $company->id,
                'purchase_date' => $date,
                'quantity' => $qty,
                'remaining_quantity' => $qty,
                'price' => $price,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'total_price' => $total,
                'final_price' => $total,
                'bilty_charges' => 1500,
                'sale_order_number' => 'SO-F-'.($i + 1),
                'delivery_order_number' => 'DO-F-'.($i + 1),
                'addedby' => $this->userId,
            ]);

            $feed->update([
                'total_quantity' => (int) $feed->total_quantity + $qty,
                'remaining_quantity' => (int) $feed->remaining_quantity + $qty,
            ]);

            CompanyBalance::create([
                'type' => 'feed',
                'company_id' => $company->id,
                'model_id' => $purchase->id,
                'total_amount' => $total,
                'remaining_amount' => $total,
                'dr' => $total,
                'addedby' => $this->userId,
            ]);
        }

        $medicineProducts = Product::where('product_group', Constant::PRODUCT_GROUP['Medicine'])->get();
        for ($i = 0; $i < 6; $i++) {
            $company = $companies[$i % $companies->count()];
            $category = $categories[$i % $categories->count()];
            $lineProducts = $medicineProducts->slice($i, 3);
            if ($lineProducts->isEmpty()) {
                $lineProducts = $medicineProducts->take(2);
            }
            $lineTotal = 0;
            $date = $this->now->copy()->subDays(45 - ($i * 6))->toDateString();

            $purchase = ProductPurchase::create([
                'purchase_code' => 'PP-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'product_category_id' => $category->id,
                'party_company_id' => $company->id,
                'purchase_date' => $date,
                'due_date_option' => '15',
                'total_amount' => 0,
                'discount_amount' => 0,
                'other_charges' => 500,
                'final_amount' => 0,
                'addedby' => $this->userId,
            ]);

            foreach ($lineProducts as $product) {
                $qty = 10 + $i;
                $line = $qty * (float) $product->purchase_price;
                $lineTotal += $line;
                ProductPurchaseDetail::create([
                    'product_purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_purchase_price' => $product->purchase_price,
                    'product_qty' => $qty,
                    'product_total_qty' => $qty,
                    'product_total_price' => $line,
                    'addedby' => $this->userId,
                ]);
            }

            $purchase->update([
                'total_amount' => $lineTotal,
                'final_amount' => $lineTotal + 500,
            ]);
        }

        for ($i = 0; $i < 6; $i++) {
            $company = $companies[$i % $companies->count()];
            $category = $categories[$i % $categories->count()];
            $party = $customers[$i % $customers->count()];
            $division = $divisions[$i % $divisions->count()];
            $lineProducts = $medicineProducts->slice($i + 1, 2);
            if ($lineProducts->isEmpty()) {
                $lineProducts = $medicineProducts->take(2);
            }
            $lineTotal = 0;
            $date = $this->now->copy()->subDays(35 - ($i * 5))->toDateString();

            $sale = ProductSale::create([
                'sale_code' => 'PS-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'division_id' => $division->id,
                'party_id' => $party->id,
                'product_category_id' => $category->id,
                'party_company_id' => $company->id,
                'sale_date' => $date,
                'due_date_option' => '15',
                'sale_type' => 'credit',
                'total_amount' => 0,
                'discount_amount' => 0,
                'other_charges' => 200,
                'final_amount' => 0,
                'addedby' => $this->userId,
            ]);

            foreach ($lineProducts as $product) {
                $qty = 4 + $i;
                $line = $qty * (float) $product->sale_price;
                $lineTotal += $line;
                ProductSaleDetail::create([
                    'product_sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_sale_price' => $product->sale_price,
                    'product_qty' => $qty,
                    'product_total_qty' => $qty,
                    'product_total_price' => $line,
                    'addedby' => $this->userId,
                ]);
            }

            $sale->update([
                'total_amount' => $lineTotal,
                'final_amount' => $lineTotal + 200,
            ]);
        }

        $remarks = [
            'Monthly farm electricity',
            'Generator diesel',
            'Worker salary advance',
            'Water tanker',
            'Shed repair material',
            'Office stationery',
        ];
        for ($i = 0; $i < 18; $i++) {
            $category = $expenseCategories[$i % $expenseCategories->count()];
            Expense::create([
                'expense_code' => 'EXP-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'category_id' => $category->id,
                'amount' => 2500 + ($i * 350),
                'expense_date' => $this->now->copy()->subDays(85 - ($i * 4))->toDateString(),
                'remarks' => $remarks[$i % count($remarks)],
                'addedby' => $this->userId,
            ]);
        }

        $vaccine = Product::where('product_name', 'like', '%Vaccine%')->first() ?? $products->first();
        foreach ($farms->take(4) as $i => $farm) {
            VaccinationSchedule::create([
                'party_farm_id' => $farm->id,
                'product_id' => $vaccine?->id,
                'schedule_date' => $this->now->copy()->addDays($i * 3)->toDateString(),
                'is_vaccinated' => $i % 2 === 0,
                'is_active' => 1,
                'description' => 'Demo vaccination schedule',
                'addedby' => $this->userId,
            ]);
        }

        $this->info('Transactions, expenses, and vaccination schedules seeded');
    }

    private function seedEmployeesAndLimits(): void
    {
        $countryId = Country::query()->value('id');
        $provinceId = Province::query()->value('id');
        $cityId = City::query()->value('id');
        $personalFarms = PersonalFarm::query()->orderBy('id')->get();
        $types = EmployeeType::query()->orderBy('id')->get();
        $levels = EmployeeLevel::query()->orderBy('id')->get();
        $allowanceId = EmployeeAllowance::query()->value('id');

        $names = [
            'Ali Raza', 'Usman Khan', 'Bilal Ahmed', 'Hassan Javed',
            'Tariq Mehmood', 'Shahid Iqbal', 'Waseem Akram', 'Farooq Ahmad',
        ];
        foreach ($names as $i => $name) {
            Employee::firstOrCreate(['emp_code' => 'EMP-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'name' => $name,
                'guardian_name' => 'Demo Father',
                'contact_no' => '0312'.str_pad((string) (5000000 + $i), 7, '0', STR_PAD_LEFT),
                'email' => Str::slug($name).'@example.com',
                'cnic_no' => '35202800000'.(20 + $i),
                'basic_salary' => 25000 + ($i * 1500),
                'other_amount' => 2000,
                'net_salary' => 27000 + ($i * 1500),
                'joining_date' => $this->now->copy()->subMonths(8 - $i)->toDateString(),
                'is_active' => 1,
                'address' => 'Staff colony',
                'personal_farm_id' => $personalFarms[$i % $personalFarms->count()]->id,
                'employee_type_id' => $types[$i % $types->count()]->id,
                'employee_level_id' => $levels[$i % $levels->count()]->id,
                'employee_allowance_id' => $allowanceId,
                'country_id' => $countryId,
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'addedby' => $this->userId,
            ]);
        }

        $vendors = Party::where('is_vendor', true)->orderBy('id')->take(4)->get();
        foreach ($vendors as $i => $vendor) {
            PartyBalanceLimit::firstOrCreate(
                ['party_id' => $vendor->id],
                [
                    'start_date' => $this->now->copy()->startOfYear()->toDateString(),
                    'end_date' => $this->now->copy()->endOfYear()->toDateString(),
                    'debit_limit' => 500000 + ($i * 100000),
                    'credit_limit' => 200000 + ($i * 50000),
                    'is_active' => 1,
                    'remarks' => 'Demo credit limit',
                    'addedby' => $this->userId,
                ]
            );
        }

        $this->info('Employees and party balance limits seeded');
    }

    private function makeParty(
        string $name,
        bool $isCustomer,
        bool $isVendor,
        int $index,
        ?int $countryId,
        ?int $provinceId,
        ?int $cityId,
        ?int $customerTypeId,
        ?int $vendorTypeId,
        ?int $customerDivisionId,
        ?int $vendorDivisionId
    ): Party {
        return Party::firstOrCreate(['party_code' => 'PTY-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT)], [
            'is_customer' => $isCustomer,
            'is_vendor' => $isVendor,
            'name' => $name,
            'guardian_name' => 'Demo Guardian',
            'cnic_no' => '35202'.str_pad((string) (1000000 + $index), 8, '0', STR_PAD_LEFT),
            'email' => 'party'.$index.'@example.com',
            'contact_no' => '0300'.str_pad((string) (1000000 + $index), 7, '0', STR_PAD_LEFT),
            'address' => $name.', Punjab',
            'is_active' => 1,
            'customer_type_id' => $isCustomer ? $customerTypeId : null,
            'vendor_type_id' => $isVendor ? $vendorTypeId : null,
            'customer_division_id' => $isCustomer ? $customerDivisionId : null,
            'vendor_division_id' => $isVendor ? $vendorDivisionId : null,
            'country_id' => $countryId,
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'addedby' => $this->userId,
        ]);
    }

    private function upsertNamed(string $model, array $rows): void
    {
        foreach ($rows as $row) {
            $model::firstOrCreate(['slug' => $row['slug']], $row);
        }
    }

    private function info(string $message): void
    {
        $this->command?->info($message);
    }
}
