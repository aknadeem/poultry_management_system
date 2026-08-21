<?php

namespace App\Support;

use App\Models\Country;
use App\Models\EmployeeLevel;
use App\Models\EmployeeType;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\PartyFarm;
use App\Models\PersonalFarm;
use App\Models\Product;

class FarmLookups
{
    /**
     * @return array<string, mixed>
     */
    public static function formOptions(): array
    {
        return [
            'countries' => Country::query()
                ->with([
                    'provinces:id,name,country_id',
                    'provinces.cities:id,name,province_id',
                ])
                ->orderBy('name')
                ->get(['id', 'name']),
            'farmTypes' => FarmType::query()->orderBy('name')->get(['id', 'name']),
            'farmSubtypes' => FarmSubtype::query()->orderBy('name')->get(['id', 'name']),
            'employeeTypes' => EmployeeType::query()->orderBy('name')->get(['id', 'name']),
            'employeeLevels' => EmployeeLevel::query()->orderBy('name')->get(['id', 'name']),
            'personalFarms' => PersonalFarm::query()
                ->orderBy('farm_name')
                ->get(['id', 'farm_name', 'farm_address']),
            'customerFarms' => PartyFarm::query()
                ->whereHas('party', fn ($party) => $party->where('is_customer', 1))
                ->orderBy('farm_name')
                ->get(['id', 'party_id', 'farm_name', 'farm_address']),
            'products' => Product::query()
                ->orderBy('product_name')
                ->get(['id', 'product_code', 'product_name']),
        ];
    }
}
