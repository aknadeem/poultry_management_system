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
        ?Carbon $entryDate = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): AccountPayable {
        $entryDate = $entryDate ?? Carbon::now();

        $payable = AccountPayable::query()
            ->where('amount_type', $amountType)
            ->where(function ($query) use ($companyBalanceId): void {
                $query->where('company_balance_id', $companyBalanceId)
                    ->orWhere(function ($inner) use ($companyBalanceId): void {
                        $inner->whereNull('company_balance_id')
                            ->where('model_id', $companyBalanceId);
                    });
            })
            ->where(function ($query): void {
                $query->whereNull('legacy_payment_row')
                    ->orWhere('legacy_payment_row', '!=', 'reconciled');
            })
            ->first();

        if ($payable) {
            $paid = (float) ($payable->paid_amount ?? 0);
            $payable->update([
                'total_amount' => $amount,
                'remaining_amount' => $amount - $paid,
                'dr' => $amount,
                'narration' => $narration,
                'company_balance_id' => $companyBalanceId,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
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
            'company_balance_id' => $companyBalanceId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'total_amount' => $amount,
            'remaining_amount' => $amount,
            'dr' => $amount,
            'addedby' => $userId,
        ]);
    }

    public function deleteByCompanyBalance(int $companyBalanceId, string $amountType): void
    {
        AccountPayable::query()
            ->where('amount_type', $amountType)
            ->where(function ($query) use ($companyBalanceId): void {
                $query->where('company_balance_id', $companyBalanceId)
                    ->orWhere(function ($inner) use ($companyBalanceId): void {
                        $inner->whereNull('company_balance_id')
                            ->where('model_id', $companyBalanceId);
                    });
            })
            ->delete();
    }

    public function deleteByModel(string $amountType, int $modelId): void
    {
        AccountPayable::where('amount_type', $amountType)
            ->where('model_id', $modelId)
            ->delete();
    }

    /**
     * @deprecated Prefer synchronizing the original payable via PaymentAllocationService.
     */
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
            'company_balance_payment_id' => $amountType === 'company_balance_payment' ? $modelId : null,
            'legacy_payment_row' => 'legacy',
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'remaining_amount' => 0,
            'cr' => $amount,
            'addedby' => $userId,
        ]);
    }
}
