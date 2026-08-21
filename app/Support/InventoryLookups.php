<?php

namespace App\Support;

use App\Models\Broker;
use App\Models\ChickGrade;
use App\Models\FeedCategory;
use App\Models\Party;
use App\Models\PartyCompany;

class InventoryLookups
{
    /**
     * @return array<string, mixed>
     */
    public static function saleOptions(): array
    {
        return [
            'today' => now()->toDateString(),
            'customers' => Party::query()
                ->where('is_customer', 1)
                ->with('farm:id,farm_name,party_id')
                ->get(['id', 'name', 'contact_no', 'cnic_no']),
            'brokers' => Broker::query()
                ->where('is_active', 1)
                ->get(['id', 'name', 'contact_no', 'cnic_no']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function purchaseOptions(): array
    {
        return [
            'today' => now()->toDateString(),
            'chickGrades' => ChickGrade::query()->orderBy('name')->get(['id', 'name']),
            'companies' => PartyCompany::query()
                ->where('is_active', 1)
                ->with('vendor:id,name,guardian_name')
                ->get(['id', 'party_id', 'company_name', 'company_address']),
            'customers' => Party::query()
                ->where('is_active', 1)
                ->where('is_customer', 1)
                ->whereHas('farm')
                ->with('farm:id,party_id,farm_name,farm_code,farm_capacity,farm_address')
                ->get(['id', 'is_customer', 'name', 'cnic_no', 'contact_no']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function feedOptions(): array
    {
        return [
            'today' => now()->toDateString(),
            'categories' => FeedCategory::query()->orderBy('name')->get(['id', 'name']),
            'companies' => PartyCompany::query()
                ->where('is_active', 1)
                ->with('vendor:id,name,guardian_name')
                ->get(['id', 'party_id', 'company_name', 'company_address']),
        ];
    }
}
