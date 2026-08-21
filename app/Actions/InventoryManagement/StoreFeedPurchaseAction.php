<?php

namespace App\Actions\InventoryManagement;

use App\Models\Feed;
use App\Models\FeedPurchase;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class StoreFeedPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?object $imageFile, int $userId): Feed
    {
        $stagedPicture = null;

        try {
            $stagedPicture = $this->uploadService->store($imageFile, 'feeds');

            return DB::transaction(function () use ($data, $stagedPicture, $userId): Feed {
                $feed = Feed::query()->create([
                    'feed_code' => 'FEED-'.time(),
                    'feed_name' => $data['feed_name'],
                    'feed_category_id' => $data['feed_category_id'],
                    'total_quantity' => $data['quantity'],
                    'remaining_quantity' => $data['quantity'],
                    'addedby' => $userId,
                ]);

                $feedPurchase = FeedPurchase::query()->create([
                    'feed_id' => $feed->id,
                    'purchase_date' => $data['purchase_date'],
                    'company_id' => $data['company_id'],
                    'quantity' => $data['quantity'],
                    'remaining_quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'discount_amount' => $data['discount_amount'] ?? null,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                    'total_price' => $data['total_price'],
                    'bilty_number' => $data['bilty_number'] ?? null,
                    'bilty_charges' => $data['bilty_charges'] ?? null,
                    'per_bag_discount_amount' => $data['per_bag_discount'] ?? $data['bag_discount'] ?? null,
                    'sale_order_number' => $data['sale_order_number'] ?? null,
                    'delivery_order_number' => $data['delivery_order_number'] ?? null,
                    'description' => $data['description'] ?? null,
                    'picture' => $stagedPicture,
                    'addedby' => $userId,
                ]);

                $this->balanceService->recordFeedPurchaseBalances(
                    $feedPurchase->id,
                    (int) $data['company_id'],
                    (float) $data['total_price'],
                    $userId
                );

                return $feed;
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('feeds', $stagedPicture);

            throw $exception;
        }
    }
}
