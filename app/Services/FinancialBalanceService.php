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
}
