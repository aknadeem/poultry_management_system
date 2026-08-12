<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenSale;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class DestroyChickenSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickenSale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $this->uploadService->delete('chickens', $sale->picture);

            $this->balanceService->deleteChickenSaleBalances(
                $sale->id,
                (int) $sale->party_id,
                (int) $sale->broker_id
            );

            $sale->delete();
        });
    }
}
