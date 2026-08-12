<?php

namespace App\Actions\InventoryManagement;

use App\Models\CompanyBalance;
use App\Models\Feed;
use App\Models\FeedPurchase;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class DestroyFeedAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(Feed $feed): void
    {
        DB::transaction(function () use ($feed) {
            $purchases = FeedPurchase::where('feed_id', $feed->id)->get();

            foreach ($purchases as $purchase) {
                $this->uploadService->delete('feeds', $purchase->picture);
                $this->balanceService->deleteFeedPurchaseBalances($purchase->id);
                $purchase->delete();
            }

            // Legacy balances keyed by feed id (column may not exist on all DBs)
            if (\Illuminate\Support\Facades\Schema::hasColumn('company_balances', 'feed_purchase_id')) {
                CompanyBalance::where('type', 'feed')
                    ->where('feed_purchase_id', $feed->id)
                    ->delete();
            }

            $this->uploadService->delete('feeds', $feed->picture ?? null);
            $feed->delete();
        });
    }
}
