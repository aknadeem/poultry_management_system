<?php

namespace App\Services;

use App\Models\Feed;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Increase the product quantity, using a pessimistic lock to prevent
     * race conditions when multiple requests touch the same product.
     *
     * Throws if the product does not exist (do not silently ignore missing
     * products — a purchase referencing a non-existent product is a data
     * integrity bug that must surface immediately).
     */
    public function increaseProductStock(int $productId, int $quantity, int $userId): void
    {
        $product = Product::lockForUpdate()->findOrFail($productId);

        $product->update([
            'quantity'  => (int) $product->quantity + $quantity,
            'updatedby' => $userId,
        ]);
    }

    /**
     * Decrease the product quantity, using a pessimistic lock to guarantee
     * that no two concurrent requests can both read the same stock value and
     * both succeed when combined they would exceed available stock.
     *
     * Throws ValidationException (surfaced as a 422) when stock is insufficient,
     * so the transaction will roll back cleanly.
     *
     * Throws ModelNotFoundException if the product does not exist.
     */
    public function decreaseProductStock(int $productId, int $quantity, int $userId): void
    {
        $product = Product::lockForUpdate()->findOrFail($productId);

        $newQty = (int) $product->quantity - $quantity;
        if ($newQty < 0) {
            throw ValidationException::withMessages([
                'product_qty' => "Insufficient stock for product {$product->product_name}. Available: {$product->quantity}",
            ]);
        }

        $product->update([
            'quantity'  => $newQty,
            'updatedby' => $userId,
        ]);
    }

    /**
     * Increase feed stock (total and remaining), using a pessimistic lock.
     * Throws if the feed does not exist.
     */
    public function increaseFeedStock(int $feedId, int $quantity, int $userId): void
    {
        $feed = Feed::lockForUpdate()->findOrFail($feedId);

        $feed->update([
            'total_quantity'     => (int) $feed->total_quantity + $quantity,
            'remaining_quantity' => (int) $feed->remaining_quantity + $quantity,
            'updatedby'          => $userId,
        ]);
    }

    /**
     * Decrease feed remaining stock, using a pessimistic lock.
     * Throws if the feed does not exist or stock is insufficient.
     */
    public function decreaseFeedStock(int $feedId, int $quantity, int $userId): void
    {
        $feed = Feed::lockForUpdate()->findOrFail($feedId);

        $remaining = (int) $feed->remaining_quantity - $quantity;
        if ($remaining < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Insufficient feed stock for {$feed->feed_name}. Available: {$feed->remaining_quantity}",
            ]);
        }

        $feed->update([
            'remaining_quantity' => $remaining,
            'updatedby'          => $userId,
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
