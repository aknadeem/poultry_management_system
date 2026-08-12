<?php

namespace App\Services;

use App\Models\CompanyBalance;
use App\Models\PartyBalance;
use App\Models\BrokerBalance;
use App\Helpers\Constant;

class FinancialBalanceService
{
    /**
     * Store or update financial balance records for a Chick Purchase.
     */
    public function recordChickPurchaseBalances(
        int $purchaseId,
        int $companyId,
        int $customerId,
        float $totalPrice,
        string $date,
        int $userId
    ): void {
        // 1. Company Balance
        $companyBalance = CompanyBalance::where('type', 'chick_purchase')
            ->where('model_id', $purchaseId)
            ->first();

        $companyPaid = $companyBalance ? (float) $companyBalance->paid_amount : 0.0;
        $companyRemaining = $totalPrice - $companyPaid;

        CompanyBalance::updateOrCreate(
            [
                'type' => 'chick_purchase',
                'model_id' => $purchaseId,
            ],
            [
                'company_id' => $companyId,
                'total_amount' => $totalPrice,
                'remaining_amount' => $companyRemaining,
                'dr' => $totalPrice,
                'addedby' => $companyBalance ? $companyBalance->addedby : $userId,
                'updatedby' => $companyBalance ? $userId : null,
            ]
        );

        // 2. Party Balance (Customer)
        $narrationPattern = "(Purchase #{$purchaseId})";
        $partyBalance = PartyBalance::where('party_id', $customerId)
            ->where('narration', 'like', "%{$narrationPattern}%")
            ->first();

        // If not found, try searching for the legacy format if it matches the exact price and date
        if (!$partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $customerId)
                ->where('transaction_date', $date)
                ->where('total_amount', $totalPrice)
                ->where('narration', 'chicks purchases for you')
                ->first();
        }

        $partyPaid = $partyBalance ? (float) $partyBalance->paid_amount : 0.0;
        $partyRemaining = $totalPrice - $partyPaid;

        PartyBalance::updateOrCreate(
            [
                'id' => $partyBalance ? $partyBalance->id : null,
            ],
            [
                'party_id' => $customerId,
                'total_amount' => $totalPrice,
                'remaining_amount' => $partyRemaining,
                'transaction_date' => $date,
                'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                'narration' => "chicks purchases for you {$narrationPattern}",
                'addedby' => $partyBalance ? $partyBalance->addedby : $userId,
                'updatedby' => $partyBalance ? $userId : null,
            ]
        );
    }

    /**
     * Delete financial balance records for a Chick Purchase.
     */
    public function deleteChickPurchaseBalances(int $purchaseId, int $companyId, int $customerId): void
    {
        // Delete company balance
        CompanyBalance::where('type', 'chick_purchase')
            ->where('model_id', $purchaseId)
            ->delete();

        // Delete party balance
        $narrationPattern = "(Purchase #{$purchaseId})";
        PartyBalance::where('party_id', $customerId)
            ->where('narration', 'like', "%{$narrationPattern}%")
            ->delete();
    }

    /**
     * Store or update financial balance records for a Chicken Sale.
     */
    public function recordChickenSaleBalances(
        int $saleId,
        int $customerId,
        int $brokerId,
        float $totalPrice,
        float $brokerCommission,
        string $date,
        int $userId
    ): void {
        // 1. Party Balance (Customer)
        $partyPattern = "(Sale #{$saleId})";
        $partyBalance = PartyBalance::where('party_id', $customerId)
            ->where('narration', 'like', "%{$partyPattern}%")
            ->first();

        if (!$partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $customerId)
                ->where('transaction_date', $date)
                ->where('total_amount', $totalPrice)
                ->where('narration', 'Chicken sale balance')
                ->first();
        }

        $partyPaid = $partyBalance ? (float) $partyBalance->paid_amount : 0.0;
        $partyRemaining = $totalPrice - $partyPaid;

        PartyBalance::updateOrCreate(
            [
                'id' => $partyBalance ? $partyBalance->id : null,
            ],
            [
                'party_id' => $customerId,
                'total_amount' => $totalPrice,
                'remaining_amount' => $partyRemaining,
                'transaction_date' => $date,
                'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                'narration' => "Chicken sale balance {$partyPattern}",
                'addedby' => $partyBalance ? $partyBalance->addedby : $userId,
                'updatedby' => $partyBalance ? $userId : null,
            ]
        );

        // 2. Broker Balance
        $brokerPattern = "(Sale #{$saleId})";
        $brokerBalance = BrokerBalance::where('broker_id', $brokerId)
            ->where('narration', 'like', "%{$brokerPattern}%")
            ->first();

        if (!$brokerBalance) {
            $brokerBalance = BrokerBalance::where('broker_id', $brokerId)
                ->where('total_amount', $brokerCommission)
                ->where('narration', 'chicken sale commession')
                ->first();
        }

        $brokerPaid = $brokerBalance ? (float) $brokerBalance->paid_amount : 0.0;
        $brokerRemaining = $brokerCommission - $brokerPaid;

        BrokerBalance::updateOrCreate(
            [
                'id' => $brokerBalance ? $brokerBalance->id : null,
            ],
            [
                'broker_id' => $brokerId,
                'dr' => $brokerCommission,
                'total_amount' => $brokerCommission,
                'remaining_amount' => $brokerRemaining,
                'narration' => "chicken sale commession {$brokerPattern}",
                'addedby' => $brokerBalance ? $brokerBalance->addedby : $userId,
                'updatedby' => $brokerBalance ? $userId : null,
            ]
        );
    }

    /**
     * Delete financial balance records for a Chicken Sale.
     */
    public function deleteChickenSaleBalances(int $saleId, int $customerId, int $brokerId): void
    {
        $pattern = "(Sale #{$saleId})";

        PartyBalance::where('party_id', $customerId)
            ->where('narration', 'like', "%{$pattern}%")
            ->delete();

        BrokerBalance::where('broker_id', $brokerId)
            ->where('narration', 'like', "%{$pattern}%")
            ->delete();
    }

    /**
     * Store or update company balance for a Chicken Purchase (meat birds).
     */
    public function recordChickenPurchaseBalances(
        int $purchaseId,
        int $companyId,
        float $totalPrice,
        int $userId
    ): CompanyBalance {
        return $this->upsertCompanyBalance(
            'chicken_purchase',
            $purchaseId,
            $companyId,
            $totalPrice,
            $userId
        );
    }

    public function deleteChickenPurchaseBalances(int $purchaseId): void
    {
        $this->deleteCompanyBalance('chicken_purchase', $purchaseId);
    }

    /**
     * Store or update company balance for a Product Purchase.
     */
    public function recordProductPurchaseBalances(
        int $purchaseId,
        int $companyId,
        float $totalPrice,
        int $userId,
        ?string $balanceType = 'Product Purchase balance'
    ): CompanyBalance {
        return $this->upsertCompanyBalance(
            'product_purchase',
            $purchaseId,
            $companyId,
            $totalPrice,
            $userId,
            $balanceType
        );
    }

    public function deleteProductPurchaseBalances(int $purchaseId): ?int
    {
        $balance = CompanyBalance::where('type', 'product_purchase')
            ->where('model_id', $purchaseId)
            ->first();

        $balanceId = $balance?->id;
        if ($balance) {
            $balance->delete();
        }

        return $balanceId;
    }

    /**
     * Store or update party balance for a Product Sale.
     */
    public function recordProductSaleBalances(
        int $saleId,
        int $partyId,
        float $totalPrice,
        string $date,
        int $userId
    ): void {
        $pattern = "(ProductSale #{$saleId})";
        $partyBalance = PartyBalance::where('party_id', $partyId)
            ->where('narration', 'like', "%{$pattern}%")
            ->first();

        if (! $partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $partyId)
                ->where('transaction_date', $date)
                ->where('total_amount', $totalPrice)
                ->where('narration', 'like', '%product sale%')
                ->first();
        }

        $partyPaid = $partyBalance ? (float) $partyBalance->paid_amount : 0.0;

        PartyBalance::updateOrCreate(
            ['id' => $partyBalance ? $partyBalance->id : null],
            [
                'party_id' => $partyId,
                'total_amount' => $totalPrice,
                'remaining_amount' => $totalPrice - $partyPaid,
                'transaction_date' => $date,
                'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                'narration' => "product sale balance {$pattern}",
                'addedby' => $partyBalance ? $partyBalance->addedby : $userId,
                'updatedby' => $partyBalance ? $userId : null,
            ]
        );
    }

    public function deleteProductSaleBalances(int $saleId, int $partyId, ?float $totalAmount = null): void
    {
        $pattern = "(ProductSale #{$saleId})";
        PartyBalance::where('party_id', $partyId)
            ->where('narration', 'like', "%{$pattern}%")
            ->delete();

        // Legacy rows created before Actions migration
        if ($totalAmount !== null) {
            PartyBalance::where('party_id', $partyId)
                ->where('total_amount', $totalAmount)
                ->where('narration', 'sale product balance')
                ->delete();
        }
    }

    /**
     * Store or update company balance for a Feed Purchase.
     */
    public function recordFeedPurchaseBalances(
        int $purchaseId,
        int $companyId,
        float $totalPrice,
        int $userId
    ): CompanyBalance {
        return $this->upsertCompanyBalance(
            'feed',
            $purchaseId,
            $companyId,
            $totalPrice,
            $userId
        );
    }

    public function deleteFeedPurchaseBalances(int $purchaseId): void
    {
        $this->deleteCompanyBalance('feed', $purchaseId);
    }

    /**
     * Apply a payment against a company balance, preserving paid/remaining.
     */
    public function applyCompanyBalancePayment(
        CompanyBalance $balance,
        float $paymentAmount,
        int $userId
    ): CompanyBalance {
        $paid = (float) $balance->paid_amount + $paymentAmount;
        $remaining = (float) $balance->total_amount - $paid;
        if ($remaining < 0) {
            $remaining = 0;
        }

        $balance->update([
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'status' => $remaining <= 0 ? 'paid' : ($paid > 0 ? 'pending' : 'unpaid'),
            'updatedby' => $userId,
        ]);

        return $balance->fresh();
    }

    /**
     * Create an opening PartyBalance row for a newly stored party.
     */
    public function recordOpeningPartyBalance(
        int $partyId,
        float $amount,
        ?string $amountType,
        int $userId
    ): PartyBalance {
        return PartyBalance::create([
            'party_id' => $partyId,
            'total_amount' => $amount,
            'remaining_amount' => $amount,
            'transaction_date' => today()->format('Y-m-d'),
            'amount_type' => $amountType,
            'narration' => 'Opening Balance',
            'addedby' => $userId,
        ]);
    }

    /**
     * Apply a payment against a party balance.
     */
    public function applyPartyBalancePayment(
        PartyBalance $balance,
        float $paymentAmount,
        int $userId
    ): PartyBalance {
        $paid = (float) ($balance->paid_amount ?? 0) + $paymentAmount;
        $remaining = (float) $balance->total_amount - $paid;
        if ($remaining < 0) {
            $remaining = 0;
        }

        $status = Constant::PAYMENT_STATUS['UnPaid'];
        if ($remaining <= 0) {
            $status = Constant::PAYMENT_STATUS['Paid'];
        } elseif ($paid > 0) {
            $status = Constant::PAYMENT_STATUS['Pending'];
        }

        $balance->update([
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'payment_status' => $status,
            'updatedby' => $userId,
        ]);

        return $balance->fresh();
    }

    private function upsertCompanyBalance(
        string $type,
        int $modelId,
        int $companyId,
        float $totalPrice,
        int $userId,
        ?string $balanceType = null
    ): CompanyBalance {
        $companyBalance = CompanyBalance::where('type', $type)
            ->where('model_id', $modelId)
            ->first();

        $companyPaid = $companyBalance ? (float) $companyBalance->paid_amount : 0.0;
        $companyRemaining = $totalPrice - $companyPaid;

        $payload = [
            'company_id' => $companyId,
            'total_amount' => $totalPrice,
            'remaining_amount' => $companyRemaining,
            'dr' => $totalPrice,
            'addedby' => $companyBalance ? $companyBalance->addedby : $userId,
            'updatedby' => $companyBalance ? $userId : null,
        ];

        if ($balanceType !== null) {
            $payload['balance_type'] = $balanceType;
        }

        return CompanyBalance::updateOrCreate(
            [
                'type' => $type,
                'model_id' => $modelId,
            ],
            $payload
        );
    }

    private function deleteCompanyBalance(string $type, int $modelId): void
    {
        CompanyBalance::where('type', $type)
            ->where('model_id', $modelId)
            ->delete();
    }
}
