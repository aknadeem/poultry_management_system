<?php

namespace App\Actions\ProductManagement;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DestroyProductAction
{
    public function execute(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });
    }
}
