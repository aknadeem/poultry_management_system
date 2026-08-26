<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductStore;
use Illuminate\Support\Facades\DB;

class StoreProductStoreAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, int $userId): ProductStore
    {
        return DB::transaction(function () use ($data, $userId): ProductStore {
            return ProductStore::query()->create([
                'store_name' => $data['store_name'],
                'store_type' => $data['store_type'],
                'total_racks' => $data['total_racks'],
                'store_area' => $data['store_area'],
                'description' => $data['store_desciption'] ?? null,
                'addedby' => $userId,
            ]);
        });
    }
}
