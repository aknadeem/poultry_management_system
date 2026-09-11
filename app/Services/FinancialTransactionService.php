<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Support\FinancialAmount;
use Illuminate\Database\Eloquent\Model;

class FinancialTransactionService
{
    /**
     * @param  array{transaction_type: string, direction: string, amount: string|int|float, transaction_date?: string|null, narration?: string|null, idempotency_key?: string|null}  $data
     */
    public function post(Model $reference, array $data, int $userId): FinancialTransaction
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;
        if (is_string($idempotencyKey) && $idempotencyKey !== '') {
            $existing = FinancialTransaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $amount = FinancialAmount::fromDecimalString($data['amount']);
        if (! $amount->isPositive()) {
            throw new \InvalidArgumentException('Financial transaction amount must be positive.');
        }

        return FinancialTransaction::query()->create([
            'reference_type' => $reference->getMorphClass(),
            'reference_id' => $reference->getKey(),
            'transaction_type' => $data['transaction_type'],
            'direction' => $data['direction'],
            'amount' => $amount->toDecimalString(),
            'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
            'status' => FinancialTransaction::STATUS_POSTED,
            'idempotency_key' => $idempotencyKey,
            'narration' => $data['narration'] ?? null,
            'addedby' => $userId,
        ]);
    }

    public function reverse(FinancialTransaction $original, int $userId, ?string $idempotencyKey = null, ?string $narration = null): FinancialTransaction
    {
        if ($original->status !== FinancialTransaction::STATUS_POSTED) {
            throw new \InvalidArgumentException('Only posted financial transactions can be reversed.');
        }

        $existingReversal = FinancialTransaction::query()
            ->where('reversal_of_id', $original->id)
            ->first();
        if ($existingReversal) {
            return $existingReversal;
        }

        if (is_string($idempotencyKey) && $idempotencyKey !== '') {
            $byKey = FinancialTransaction::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($byKey) {
                return $byKey;
            }
        }

        $direction = $original->direction === 'debit' ? 'credit' : 'debit';

        $reversal = FinancialTransaction::query()->create([
            'reference_type' => $original->reference_type,
            'reference_id' => $original->reference_id,
            'transaction_type' => $original->transaction_type.'_reversal',
            'direction' => $direction,
            'amount' => FinancialAmount::fromDecimalString((string) $original->amount)->toDecimalString(),
            'transaction_date' => now()->toDateString(),
            'status' => FinancialTransaction::STATUS_POSTED,
            'reversal_of_id' => $original->id,
            'idempotency_key' => $idempotencyKey,
            'narration' => $narration ?? ('Reversal of transaction #'.$original->id),
            'addedby' => $userId,
        ]);

        $original->update([
            'status' => FinancialTransaction::STATUS_REVERSED,
            'updatedby' => $userId,
        ]);

        return $reversal;
    }
}
