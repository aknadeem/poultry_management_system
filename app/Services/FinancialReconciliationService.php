<?php

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Models\PaymentAllocation;
use App\Support\FinancialAmount;
use Illuminate\Support\Collection;

class FinancialReconciliationService
{
    /**
     * @return array{
     *     clean: bool,
     *     issues: list<array{code: string, message: string, meta?: array<string, mixed>}>,
     *     summary: array<string, int>
     * }
     */
    public function report(): array
    {
        $issues = collect()
            ->merge($this->balanceEquationIssues())
            ->merge($this->negativeAmountIssues())
            ->merge($this->orphanPaymentIssues())
            ->merge($this->duplicateSourceIssues())
            ->merge($this->narrationOnlySourceIssues())
            ->merge($this->legacyPaymentPayableIssues())
            ->merge($this->allocationDriftIssues())
            ->values()
            ->all();

        $byCode = collect($issues)->groupBy('code')->map->count()->all();

        return [
            'clean' => $issues === [],
            'issues' => $issues,
            'summary' => $byCode,
        ];
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function balanceEquationIssues(): Collection
    {
        $issues = collect();

        CompanyBalance::query()->orderBy('id')->chunkById(200, function ($balances) use ($issues): void {
            foreach ($balances as $balance) {
                $total = FinancialAmount::fromDecimalString((string) $balance->total_amount);
                $paid = FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0));
                $remaining = FinancialAmount::fromDecimalString((string) ($balance->remaining_amount ?? 0));

                if (! $total->subtract($paid)->equals($remaining)) {
                    $issues->push([
                        'code' => 'company_balance_equation',
                        'message' => "CompanyBalance #{$balance->id} total - paid != remaining.",
                        'meta' => ['id' => $balance->id],
                    ]);
                }
            }
        });

        PartyBalance::query()->orderBy('id')->chunkById(200, function ($balances) use ($issues): void {
            foreach ($balances as $balance) {
                $total = FinancialAmount::fromDecimalString((string) $balance->total_amount);
                $paid = FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0));
                $remaining = FinancialAmount::fromDecimalString((string) ($balance->remaining_amount ?? 0));

                if (! $total->subtract($paid)->equals($remaining)) {
                    $issues->push([
                        'code' => 'party_balance_equation',
                        'message' => "PartyBalance #{$balance->id} total - paid != remaining.",
                        'meta' => ['id' => $balance->id],
                    ]);
                }
            }
        });

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function negativeAmountIssues(): Collection
    {
        $issues = collect();

        foreach ([CompanyBalance::class, PartyBalance::class] as $model) {
            $model::query()
                ->where(function ($query): void {
                    $query->where('total_amount', '<', 0)
                        ->orWhere('paid_amount', '<', 0)
                        ->orWhere('remaining_amount', '<', 0);
                })
                ->orderBy('id')
                ->each(function ($balance) use ($issues, $model): void {
                    $issues->push([
                        'code' => 'negative_amount',
                        'message' => class_basename($model)." #{$balance->id} has a negative monetary column.",
                        'meta' => ['id' => $balance->id, 'model' => $model],
                    ]);
                });
        }

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function orphanPaymentIssues(): Collection
    {
        $issues = collect();

        CompanyBalancePayment::query()
            ->whereDoesntHave('companyBalance')
            ->orderBy('id')
            ->each(function (CompanyBalancePayment $payment) use ($issues): void {
                $issues->push([
                    'code' => 'orphan_company_payment',
                    'message' => "CompanyBalancePayment #{$payment->id} has no company balance.",
                    'meta' => ['id' => $payment->id],
                ]);
            });

        PartyBalancePayment::query()
            ->whereDoesntHave('partyBalance')
            ->orderBy('id')
            ->each(function (PartyBalancePayment $payment) use ($issues): void {
                $issues->push([
                    'code' => 'orphan_party_payment',
                    'message' => "PartyBalancePayment #{$payment->id} has no party balance.",
                    'meta' => ['id' => $payment->id],
                ]);
            });

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function duplicateSourceIssues(): Collection
    {
        $issues = collect();

        $duplicates = CompanyBalance::query()
            ->selectRaw('type, model_id, COUNT(*) as aggregate')
            ->whereNotNull('type')
            ->whereNotNull('model_id')
            ->groupBy('type', 'model_id')
            ->having('aggregate', '>', 1)
            ->get();

        foreach ($duplicates as $row) {
            $issues->push([
                'code' => 'duplicate_company_source',
                'message' => "Duplicate company balances for {$row->type}#{$row->model_id}.",
                'meta' => ['type' => $row->type, 'model_id' => $row->model_id, 'count' => $row->aggregate],
            ]);
        }

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function narrationOnlySourceIssues(): Collection
    {
        $issues = collect();

        PartyBalance::query()
            ->whereNull('reference_type')
            ->whereNotNull('narration')
            ->where(function ($query): void {
                $query->where('narration', 'like', '%(ProductSale #%')
                    ->orWhere('narration', 'like', '%product sale%');
            })
            ->orderBy('id')
            ->each(function (PartyBalance $balance) use ($issues): void {
                $issues->push([
                    'code' => 'narration_only_party_source',
                    'message' => "PartyBalance #{$balance->id} still relies on narration matching.",
                    'meta' => ['id' => $balance->id],
                ]);
            });

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function legacyPaymentPayableIssues(): Collection
    {
        $issues = collect();

        AccountPayable::query()
            ->where('amount_type', 'company_balance_payment')
            ->where(function ($query): void {
                $query->whereNull('legacy_payment_row')
                    ->orWhere('legacy_payment_row', '!=', 'reconciled');
            })
            ->orderBy('id')
            ->each(function (AccountPayable $payable) use ($issues): void {
                $issues->push([
                    'code' => 'legacy_payment_payable',
                    'message' => "AccountPayable #{$payable->id} is an unreconciled company_balance_payment row.",
                    'meta' => ['id' => $payable->id],
                ]);
            });

        return $issues;
    }

    /**
     * @return Collection<int, array{code: string, message: string, meta?: array<string, mixed>}>
     */
    private function allocationDriftIssues(): Collection
    {
        $issues = collect();

        CompanyBalance::query()->orderBy('id')->chunkById(100, function ($balances) use ($issues): void {
            foreach ($balances as $balance) {
                $allocated = PaymentAllocation::query()
                    ->where('obligation_type', $balance->getMorphClass())
                    ->where('obligation_id', $balance->id)
                    ->where('status', PaymentAllocation::STATUS_POSTED)
                    ->sum('allocated_amount');

                $paid = FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0));
                $allocatedAmount = FinancialAmount::fromDecimalString((string) $allocated);

                if (! $paid->equals($allocatedAmount) && $allocatedAmount->isPositive()) {
                    $issues->push([
                        'code' => 'company_allocation_drift',
                        'message' => "CompanyBalance #{$balance->id} paid_amount differs from active allocations.",
                        'meta' => [
                            'id' => $balance->id,
                            'paid' => $paid->toDecimalString(),
                            'allocated' => $allocatedAmount->toDecimalString(),
                        ],
                    ]);
                }
            }
        });

        PartyBalance::query()->orderBy('id')->chunkById(100, function ($balances) use ($issues): void {
            foreach ($balances as $balance) {
                $allocated = PaymentAllocation::query()
                    ->where('obligation_type', $balance->getMorphClass())
                    ->where('obligation_id', $balance->id)
                    ->where('status', PaymentAllocation::STATUS_POSTED)
                    ->sum('allocated_amount');

                $paid = FinancialAmount::fromDecimalString((string) ($balance->paid_amount ?? 0));
                $allocatedAmount = FinancialAmount::fromDecimalString((string) $allocated);

                if (! $paid->equals($allocatedAmount) && $allocatedAmount->isPositive()) {
                    $issues->push([
                        'code' => 'party_allocation_drift',
                        'message' => "PartyBalance #{$balance->id} paid_amount differs from active allocations.",
                        'meta' => [
                            'id' => $balance->id,
                            'paid' => $paid->toDecimalString(),
                            'allocated' => $allocatedAmount->toDecimalString(),
                        ],
                    ]);
                }
            }
        });

        return $issues;
    }
}
