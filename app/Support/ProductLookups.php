<?php

namespace App\Support;

use App\Helpers\Constant;
use App\Models\Division;
use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\ProductCategory;
use App\Models\ProductStore;
use App\Models\ProductType;
use App\Models\VaccinationGroup;

class ProductLookups
{
    /**
     * @return array<string, mixed>
     */
    public static function formOptions(): array
    {
        return [
            'productGroups' => collect(Constant::PRODUCT_GROUP)
                ->map(fn (int $value, string $label): array => [
                    'value' => $value,
                    'label' => $label,
                ])
                ->values()
                ->all(),
            'companies' => PartyCompany::query()
                ->where('is_active', 1)
                ->orderBy('company_name')
                ->get(['id', 'company_name', 'company_code']),
            'productCategories' => ProductCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'company_id']),
            'productTypes' => ProductType::query()->orderBy('name')->get(['id', 'name']),
            'productStores' => ProductStore::query()
                ->orderBy('store_name')
                ->get(['id', 'store_name', 'store_code', 'store_area', 'total_racks']),
            'vaccinationGroups' => VaccinationGroup::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'packSizeUnitTypes' => [
                ['value' => 'gram', 'label' => 'Gram'],
                ['value' => 'kilo_gram', 'label' => 'Kg'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function saleFormOptions(): array
    {
        return [
            'divisions' => Division::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'customers' => Party::query()
                ->where('is_customer', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'cnic_no', 'customer_division_id']),
            'companies' => PartyCompany::query()
                ->where('is_active', 1)
                ->orderBy('company_name')
                ->get(['id', 'company_name', 'company_code']),
            'productCategories' => ProductCategory::query()
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'company_id']),
        ];
    }
}
