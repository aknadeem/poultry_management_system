<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenSale;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DestroyChickenSaleAction
{
    private $balanceService;

    public function __construct(FinancialBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function execute(ChickenSale $sale): void
    {
        DB::transaction(function () use ($sale) {
            // Delete image if exists
            $imgPath = 'chickens/' . $sale->picture;
            if ($sale->picture && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }

            // Sync balances deletion
            $this->balanceService->deleteChickenSaleBalances(
                $sale->id,
                (int) $sale->party_id,
                (int) $sale->broker_id
            );

            // Delete sale
            $sale->delete();
        });
    }
}
