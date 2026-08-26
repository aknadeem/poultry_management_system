<?php

namespace App\Support;

use App\Models\ProductStore;

class ProductStorePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function store(ProductStore $store): array
    {
        return [
            'id' => $store->id,
            'store_name' => $store->store_name,
            'store_code' => $store->store_code,
            'store_type' => $store->store_type,
            'store_area' => $store->store_area,
            'total_racks' => $store->total_racks,
            'description' => $store->description,
            'is_active' => (bool) $store->is_active,
            'created_at' => $store->created_at?->format('Y-m-d'),
        ];
    }
}
