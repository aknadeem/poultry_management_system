<?php

namespace App\Services;

use App\Models\AccountPayable;
use Carbon\Carbon;

class PayableService
{
    public function recordCompanyPayable(
        int $companyBalanceId,
        float $amount,
        string $amountType,
        string $narration,
        int $userId,
        ?Carbon $entryDate = null
    ): AccountPayable {
        $entryDate = $entryDate ?? Carbon::now();

        $payable = AccountPayable::where('amount_type', $amountType)
            ->where('model_id', $companyBalanceId)
            ->first();

        if ($payable) {
            $paid = (float) ($payable->paid_amount ?? 0);
            $payable->update([
                'total_amount' => $amount,
                'remaining_amount' => $amount - $paid,
                'dr' => $amount,
                'narration' => $narration,
                'updatedby' => $userId,
            ]);

            return $payable->fresh();
        }

        return AccountPayable::create([
            'amount_type' => $amountType,
            'narration' => $narration,
            'amount_status' => 'unpaid',
            'entry_date' => $entryDate,
            'model_id' => $companyBalanceId,
            'total_amount' => $amount,
            'remaining_amount' => $amount,
            'dr' => $amount,
            'addedby' => $userId,
        ]);
    }

    public function deleteByCompanyBalance(int $companyBalanceId, string $amountType): void
    {
        AccountPayable::where('amount_type', $amountType)
            ->where('model_id', $companyBalanceId)
            ->delete();
    }

    public function deleteByModel(string $amountType, int $modelId): void
    {
        AccountPayable::where('amount_type', $amountType)
            ->where('model_id', $modelId)
            ->delete();
    }

    public function recordPaidPayable(
        int $modelId,
        float $amount,
        string $amountType,
        int $userId,
        ?Carbon $entryDate = null
    ): AccountPayable {
        return AccountPayable::create([
            'amount_type' => $amountType,
            'amount_status' => 'paid',
            'entry_date' => $entryDate ?? Carbon::now(),
            'model_id' => $modelId,
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'remaining_amount' => 0,
            'cr' => $amount,
            'addedby' => $userId,
        ]);
    }
}
