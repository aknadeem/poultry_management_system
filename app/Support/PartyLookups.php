<?php

namespace App\Support;

use App\Helpers\Constant;
use App\Models\BusinessType;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\VendorType;

class PartyLookups
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
            'divisions' => Division::query()->orderBy('name')->get(['id', 'name']),
            'customerTypes' => CustomerType::query()->orderBy('name')->get(['id', 'name']),
            'farmTypes' => FarmType::query()->orderBy('name')->get(['id', 'name']),
            'farmSubtypes' => FarmSubtype::query()->orderBy('name')->get(['id', 'name']),
            'vendorTypes' => VendorType::query()->orderBy('name')->get(['id', 'name']),
            'businessTypes' => BusinessType::query()->orderBy('name')->get(['id', 'name']),
            'contactPersons' => ConductPerson::query()->orderBy('name')->get(['id', 'name']),
            'amountTypes' => collect(Constant::AMOUNT_TYPE)
                ->map(fn (int $value, string $label): array => [
                    'value' => $value,
                    'label' => $label,
                ])
                ->values()
                ->all(),
        ];
    }
}
