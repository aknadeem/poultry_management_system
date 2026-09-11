<?php

namespace App\Services;

use App\Helpers\Constant;
use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Models\PaymentAllocation;
use App\Support\FinancialAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PaymentAllocationService
{
    public function __construct(
        private PayableService $payableService,
    ) {
    }

    /**
     * @param  array{amount: string|int|float, idempotency_key?: string|null}  $data
     */
    public function allocateToCompanyBalance(
        CompanyBalancePayment $payment,
        int $companyBalanceId,
        array $data,
        int $userId,
    ): PaymentAllocation {
        $balance = CompanyBalance::query()->lockForUpdate()->find($companyBalanceId);
        if (! $balance) {
            throw ValidationException::withMessages([
                'company_balance_id' => 'No Balance Found against this record',
            ]);
        }

        if ((int) $payment->party_company_id !== (int) $balance->company_id) {
            throw ValidationException::withMessages([
                'party_company_id' => 'Payment company does not match the selected balance.',
            ]);
        }

        return $this->allocate($payment, $balance, $data, $userId);
    }

    /**
     * @param  array{amount: string|int|float, idempotency_key?: string|null}  $data
     */
    public function allocateToPartyBalance(
        PartyBalancePayment $payment,
        int $partyBalanceId,
        array $data,
        int $userId,
    ): PaymentAllocation {
        $balance = PartyBalance::query()->lockForUpdate()->find($partyBalanceId);
        if (! $balance) {
            throw ValidationException::withMessages([
                'balance_id' => 'No Balance Found against this record',
            ]);
        }

        if ((int) $payment->party_id !== (int) $balance->party_id) {
            throw ValidationException::withMessages([
                'party_id' => 'Payment party does not match the selected balance.',
            ]);
        }

        return $this->allocate($payment, $balance, $data, $userId);
    }

    /**
     * @param  array{amount: string|int|float, idempotency_key?: string|null}  $data
     */
    private function allocate(Model $payment, Model $obligation, array $data, int $userId): PaymentAllocation
    {
        $amount = FinancialAmount::fromDecimalString($data['amount']);
        if (! $amount->isPositive()) {
            throw ValidationException::withMessages([
                'amount_payment' => 'Payment amount must be greater than zero.',
            ]);
        }

        $idempotencyKey = $data['idempotency_key'] ?? null;
        if (is_string($idempotencyKey) && $idempotencyKey !== '') {
            $existing = PaymentAllocation::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $existingPair = PaymentAllocation::query()
            ->where('payment_type', $payment->getMorphClass())
            ->where('payment_id', $payment->getKey())
            ->where('obligation_type', $obligation->getMorphClass())
            ->where('obligation_id', $obligation->getKey())
            ->where('status', PaymentAllocation::STATUS_POSTED)
            ->first();
        if ($existingPair) {
            return $existingPair;
        }

        $remaining = FinancialAmount::fromDecimalString((string) ($obligation->remaining_amount ?? 0));
        if ($remaining->isZero() || $remaining->isNegative()) {
            throw ValidationException::withMessages([
                'amount_payment' => 'This balance is already fully paid.',
            ]);
        }

        if ($amount->greaterThan($remaining)) {
            throw ValidationException::withMessages([
                'amount_payment' => 'Payment amount cannot exceed the remaining balance of '.$remaining->toDecimalString().'.',
            ]);
        }

        $allocation = PaymentAllocation::query()->create([
            'payment_type' => $payment->getMorphClass(),
            'payment_id' => $payment->getKey(),
            'obligation_type' => $obligation->getMorphClass(),
            'obligation_id' => $obligation->getKey(),
            'allocated_amount' => $amount->toDecimalString(),
            'status' => PaymentAllocation::STATUS_POSTED,
            'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== '' ? $idempotencyKey : null,
            'addedby' => $userId,
        ]);

        $this->recomputeObligation($obligation, $userId);
        $this->syncOriginalPayable($obligation, $userId);

        return $allocation;
    }

    public function voidAllocation(PaymentAllocation $allocation, int $userId): PaymentAllocation
    {
        if ($allocation->status !== PaymentAllocation::STATUS_POSTED) {
            throw ValidationException::withMessages([
                'payment' => 'This payment allocation has already been reversed.',
            ]);
        }

        $obligation = $allocation->obligation_type::query()
            ->lockForUpdate()
            ->findOrFail($allocation->obligation_id);

        $allocation->update([
            'status' => PaymentAllocation::STATUS_VOIDED,
            'updatedby' => $userId,
        ]);

        $this->recomputeObligation($obligation, $userId);
        $this->syncOriginalPayable($obligation, $userId);

        return $allocation->fresh();
    }

    public function hasActiveAllocations(Model $obligation): bool
    {
        return PaymentAllocation::query()
            ->where('obligation_type', $obligation->getMorphClass())
            ->where('obligation_id', $obligation->getKey())
            ->where('status', PaymentAllocation::STATUS_POSTED)
            ->exists();
    }

    public function allocatedTotal(Model $obligation): FinancialAmount
    {
        $sum = PaymentAllocation::query()
            ->where('obligation_type', $obligation->getMorphClass())
            ->where('obligation_id', $obligation->getKey())
            ->where('status', PaymentAllocation::STATUS_POSTED)
            ->sum('allocated_amount');

        return FinancialAmount::fromDecimalString((string) $sum);
    }

    private function recomputeObligation(Model $obligation, int $userId): void
    {
        $total = FinancialAmount::fromDecimalString((string) ($obligation->total_amount ?? 0));
        $paid = $this->allocatedTotal($obligation);
        $remaining = $total->subtract($paid);

        if ($remaining->isNegative()) {
            throw ValidationException::withMessages([
                'amount_payment' => 'Allocated amount cannot exceed the outstanding balance.',
            ]);
        }

        $payload = [
            'paid_amount' => $paid->toDecimalString(),
            'remaining_amount' => $remaining->toDecimalString(),
            'updatedby' => $userId,
        ];

        if ($obligation instanceof CompanyBalance) {
            $payload['status'] = $remaining->isZero()
                ? 'paid'
                : ($paid->isPositive() ? 'pending' : 'unpaid');
        }

        if ($obligation instanceof PartyBalance) {
            $payload['payment_status'] = $remaining->isZero()
                ? Constant::PAYMENT_STATUS['Paid']
                : ($paid->isPositive()
                    ? Constant::PAYMENT_STATUS['Pending']
                    : Constant::PAYMENT_STATUS['UnPaid']);
        }

        $obligation->update($payload);
    }

    private function syncOriginalPayable(Model $obligation, int $userId): void
    {
        if (! $obligation instanceof CompanyBalance) {
            return;
        }

        $payable = AccountPayable::query()
            ->where('amount_type', 'product_purchase')
            ->where(function ($query) use ($obligation): void {
                $query->where('company_balance_id', $obligation->id)
                    ->orWhere(function ($inner) use ($obligation): void {
                        $inner->whereNull('company_balance_id')
                            ->where('model_id', $obligation->id);
                    });
            })
            ->where(function ($query): void {
                $query->whereNull('legacy_payment_row')
                    ->orWhere('legacy_payment_row', '!=', 'reconciled');
            })
            ->lockForUpdate()
            ->first();

        if (! $payable) {
            return;
        }

        $paid = FinancialAmount::fromDecimalString((string) ($obligation->paid_amount ?? 0));
        $total = FinancialAmount::fromDecimalString((string) ($obligation->total_amount ?? 0));
        $remaining = $total->subtract($paid);

        $payable->update([
            'company_balance_id' => $obligation->id,
            'reference_type' => $obligation->reference_type ?? $obligation->type,
            'reference_id' => $obligation->reference_id ?? $obligation->model_id,
            'paid_amount' => $paid->toDecimalString(),
            'remaining_amount' => $remaining->toDecimalString(),
            'amount_status' => $remaining->isZero() ? 'paid' : ($paid->isPositive() ? 'pending' : 'unpaid'),
            'updatedby' => $userId,
        ]);
    }
}
