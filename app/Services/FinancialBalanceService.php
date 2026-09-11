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
        $companyRemaining = round($totalPrice - $companyPaid, 2);

        CompanyBalance::updateOrCreate(
            [
                'type' => 'chick_purchase',
                'model_id' => $purchaseId,
            ],
            [
                'company_id' => $companyId,
                'reference_type' => 'chick_purchase',
                'reference_id' => $purchaseId,
                'total_amount' => $totalPrice,
                'remaining_amount' => $companyRemaining,
                'dr' => $totalPrice,
                'addedby' => $companyBalance ? $companyBalance->addedby : $userId,
                'updatedby' => $companyBalance ? $userId : null,
            ]
        );

        // 2. Party Balance (Customer)
        $narrationPattern = "(Purchase #{$purchaseId})";
        $partyBalance = PartyBalance::query()
            ->where('reference_type', 'chick_purchase')
            ->where('reference_id', $purchaseId)
            ->first();

        if (! $partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $customerId)
                ->where('narration', 'like', "%{$narrationPattern}%")
                ->first();
        }

        // If not found, try searching for the legacy format if it matches the exact price and date
        if (!$partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $customerId)
                ->where('transaction_date', $date)
                ->where('total_amount', $totalPrice)
                ->where('narration', 'chicks purchases for you')
                ->first();
        }

        $partyPaid = $partyBalance ? (float) $partyBalance->paid_amount : 0.0;
        $partyRemaining = round($totalPrice - $partyPaid, 2);

        PartyBalance::updateOrCreate(
            [
                'id' => $partyBalance ? $partyBalance->id : null,
            ],
            [
                'party_id' => $customerId,
                'reference_type' => 'chick_purchase',
                'reference_id' => $purchaseId,
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
        $partyBalance = PartyBalance::query()
            ->where('reference_type', 'chicken_sale')
            ->where('reference_id', $saleId)
            ->first();

        if (! $partyBalance) {
            $partyBalance = PartyBalance::where('narration', 'like', "%{$partyPattern}%")
                ->first();
        }

        if (!$partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $customerId)
                ->where('transaction_date', $date)
                ->where('total_amount', $totalPrice)
                ->where('narration', 'Chicken sale balance')
                ->first();
        }

        $partyPaid = $partyBalance ? (float) $partyBalance->paid_amount : 0.0;
        $partyRemaining = round($totalPrice - $partyPaid, 2);

        PartyBalance::updateOrCreate(
            [
                'id' => $partyBalance ? $partyBalance->id : null,
            ],
            [
                'party_id' => $customerId,
                'reference_type' => 'chicken_sale',
                'reference_id' => $saleId,
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
        $brokerBalance = BrokerBalance::query()
            ->where('reference_type', 'chicken_sale')
            ->where('reference_id', $saleId)
            ->first();

        if (! $brokerBalance) {
            $brokerBalance = BrokerBalance::where('narration', 'like', "%{$brokerPattern}%")
                ->first();
        }

        if (!$brokerBalance) {
            $brokerBalance = BrokerBalance::where('broker_id', $brokerId)
                ->where('total_amount', $brokerCommission)
                ->where('narration', 'chicken sale commession')
                ->first();
        }

        $brokerPaid = $brokerBalance ? (float) $brokerBalance->paid_amount : 0.0;
        $brokerRemaining = round($brokerCommission - $brokerPaid, 2);

        BrokerBalance::updateOrCreate(
            [
                'id' => $brokerBalance ? $brokerBalance->id : null,
            ],
            [
                'broker_id' => $brokerId,
                'reference_type' => 'chicken_sale',
                'reference_id' => $saleId,
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
        $partyBalance = PartyBalance::query()
            ->where('reference_type', 'product_sale')
            ->where('reference_id', $saleId)
            ->first();

        if (! $partyBalance) {
            $partyBalance = PartyBalance::where('party_id', $partyId)
                ->where('narration', 'like', "%{$pattern}%")
                ->first();
        }

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
                'remaining_amount' => round($totalPrice - $partyPaid, 2),
                'transaction_date' => $date,
                'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                'narration' => "product sale balance {$pattern}",
                'reference_type' => 'product_sale',
                'reference_id' => $saleId,
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
        // Legacy entry point retained for compatibility. Payment actions now allocate
        // through PaymentAllocationService, which rejects overpayments and locks rows.
        $amount = \App\Support\FinancialAmount::fromDecimalString((string) $paymentAmount);
        $remaining = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->remaining_amount ?? 0));

        if (! $amount->isPositive() || $amount->greaterThan($remaining)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount_payment' => 'Payment amount cannot exceed the remaining balance.',
            ]);
        }

        $paid = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0))->add($amount);
        $newRemaining = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->total_amount ?? 0))->subtract($paid);

        $balance->update([
            'paid_amount' => $paid->toDecimalString(),
            'remaining_amount' => $newRemaining->toDecimalString(),
            'status' => $newRemaining->isZero() ? 'paid' : ($paid->isPositive() ? 'pending' : 'unpaid'),
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
        $amount = \App\Support\FinancialAmount::fromDecimalString((string) $paymentAmount);
        $remaining = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->remaining_amount ?? 0));

        if (! $amount->isPositive() || $amount->greaterThan($remaining)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount_payment' => 'Payment amount cannot exceed the remaining balance.',
            ]);
        }

        $paid = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0))->add($amount);
        $newRemaining = \App\Support\FinancialAmount::fromDecimalString((string) ($balance->total_amount ?? 0))->subtract($paid);

        $status = Constant::PAYMENT_STATUS['UnPaid'];
        if ($newRemaining->isZero()) {
            $status = Constant::PAYMENT_STATUS['Paid'];
        } elseif ($paid->isPositive()) {
            $status = Constant::PAYMENT_STATUS['Pending'];
        }

        $balance->update([
            'paid_amount' => $paid->toDecimalString(),
            'remaining_amount' => $newRemaining->toDecimalString(),
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
        $companyRemaining = round($totalPrice - $companyPaid, 2);

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

        $payload['reference_type'] = $type;
        $payload['reference_id'] = $modelId;

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
