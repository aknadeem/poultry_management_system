<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductStore;
use Illuminate\Support\Facades\DB;

class DestroyProductStoreAction
{
    public function execute(ProductStore $productStore): void
    {
        DB::transaction(function () use ($productStore) {
            $productStore->delete();
        });
    }
}
