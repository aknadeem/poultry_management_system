<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class DestroyChickenPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickenPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $this->uploadService->delete('chicks', $purchase->picture);
            $this->balanceService->deleteChickenPurchaseBalances($purchase->id);
            $purchase->delete();
        });
    }
}
