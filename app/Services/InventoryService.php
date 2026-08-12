<?php

namespace App\Services;

use App\Models\Feed;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function increaseProductStock(int $productId, int $quantity, int $userId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $product->update([
            'quantity' => (int) $product->quantity + $quantity,
            'updatedby' => $userId,
        ]);
    }

    public function decreaseProductStock(int $productId, int $quantity, int $userId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $newQty = (int) $product->quantity - $quantity;
        if ($newQty < 0) {
            throw ValidationException::withMessages([
                'product_qty' => "Insufficient stock for product {$product->product_name}. Available: {$product->quantity}",
            ]);
        }

        $product->update([
            'quantity' => $newQty,
            'updatedby' => $userId,
        ]);
    }

    public function increaseFeedStock(int $feedId, int $quantity, int $userId): void
    {
        $feed = Feed::find($feedId);
        if (! $feed) {
            return;
        }

        $feed->update([
            'total_quantity' => (int) $feed->total_quantity + $quantity,
            'remaining_quantity' => (int) $feed->remaining_quantity + $quantity,
            'updatedby' => $userId,
        ]);
    }

    public function decreaseFeedStock(int $feedId, int $quantity, int $userId): void
    {
        $feed = Feed::find($feedId);
        if (! $feed) {
            return;
        }

        $remaining = (int) $feed->remaining_quantity - $quantity;
        if ($remaining < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Insufficient feed stock for {$feed->feed_name}. Available: {$feed->remaining_quantity}",
            ]);
        }

        $feed->update([
            'remaining_quantity' => $remaining,
            'updatedby' => $userId,
        ]);
    }

    /**
     * Reverse product purchase line quantities (subtract what was added).
     *
     * @param  iterable<int, object>  $details
     */
    public function reverseProductPurchaseLines(iterable $details, int $userId): void
    {
        foreach ($details as $detail) {
            $qty = (int) ($detail->product_total_qty ?? $detail->product_qty ?? 0);
            if ($qty > 0 && $detail->product_id) {
                $this->decreaseProductStock((int) $detail->product_id, $qty, $userId);
            }
        }
    }

    /**
     * Reverse product sale line quantities (add back what was sold).
     *
     * @param  iterable<int, object>  $details
     */
    public function reverseProductSaleLines(iterable $details, int $userId): void
    {
        foreach ($details as $detail) {
            $qty = (int) ($detail->product_total_qty ?? $detail->product_qty ?? 0);
            if ($qty > 0 && $detail->product_id) {
                $this->increaseProductStock((int) $detail->product_id, $qty, $userId);
            }
        }
    }
}
