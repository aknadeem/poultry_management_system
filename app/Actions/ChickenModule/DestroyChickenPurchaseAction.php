<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DestroyChickenPurchaseAction
{
    public function execute(ChickenPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Delete image if exists
            $imgPath = 'chicks/' . $purchase->picture;
            if ($purchase->picture && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }

            // Delete linked company balance
            CompanyBalance::where('type', 'chicken_purchase')
                ->where('model_id', $purchase->id)
                ->delete();

            $purchase->delete();
        });
    }
}
