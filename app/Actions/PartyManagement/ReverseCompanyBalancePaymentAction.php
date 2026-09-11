<?php

namespace App\Actions\PartyManagement;

use App\Models\CompanyBalancePayment;
use App\Models\FinancialTransaction;
use App\Models\PaymentAllocation;
use App\Services\FinancialTransactionService;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReverseCompanyBalancePaymentAction
{
    public function __construct(
        private PaymentAllocationService $allocationService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(CompanyBalancePayment $payment, int $userId, ?string $reason = null): CompanyBalancePayment
    {
        return DB::transaction(function () use ($payment, $userId, $reason) {
            $payment = CompanyBalancePayment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($payment->payment_status === 'reversed') {
                throw ValidationException::withMessages([
                    'payment' => 'This payment has already been reversed.',
                ]);
            }

            $allocation = PaymentAllocation::query()
                ->where('payment_type', $payment->getMorphClass())
                ->where('payment_id', $payment->id)
                ->where('status', PaymentAllocation::STATUS_POSTED)
                ->lockForUpdate()
                ->first();

            if ($allocation) {
                $this->allocationService->voidAllocation($allocation, $userId);
            }

            $posted = FinancialTransaction::query()
                ->where('reference_type', $payment->getMorphClass())
                ->where('reference_id', $payment->id)
                ->where('transaction_type', 'company_balance_payment')
                ->where('status', FinancialTransaction::STATUS_POSTED)
                ->lockForUpdate()
                ->first();

            if ($posted) {
                $this->transactionService->reverse(
                    $posted,
                    $userId,
                    'reverse-txn-company-payment-'.$payment->id,
                    $reason ?? 'Company payment reversal #'.$payment->id,
                );
            }

            $payment->update([
                'payment_status' => 'reversed',
                'reversed_at' => now(),
                'reversed_by' => $userId,
                'reversal_reason' => $reason,
                'updatedby' => $userId,
            ]);

            return $payment->fresh();
        });
    }
}
