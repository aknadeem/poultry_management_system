<?php

namespace App\Console\Commands;

use App\Models\AccountPayable;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\PartyBalance;
use App\Models\PaymentAllocation;
use App\Support\FinancialAmount;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('finance:backfill-references {--dry-run : Report changes without writing}')]
#[Description('Backfill explicit financial references and reconcile legacy payment payables')]
class FinanceBackfillReferencesCommand extends Command
{
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updatedCompany = 0;
        $updatedParty = 0;
        $reconciledPayables = 0;
        $ambiguous = 0;

        DB::transaction(function () use ($dryRun, &$updatedCompany, &$updatedParty, &$reconciledPayables, &$ambiguous): void {
            CompanyBalance::query()
                ->whereNull('reference_type')
                ->whereNotNull('type')
                ->whereNotNull('model_id')
                ->orderBy('id')
                ->each(function (CompanyBalance $balance) use ($dryRun, &$updatedCompany): void {
                    $payload = [
                        'reference_type' => $balance->type,
                        'reference_id' => $balance->model_id,
                    ];

                    if (! $dryRun) {
                        $balance->update($payload);
                    }

                    $updatedCompany++;
                    $this->line(($dryRun ? '[dry-run] ' : '')."CompanyBalance #{$balance->id} => {$balance->type}#{$balance->model_id}");
                });

            PartyBalance::query()
                ->whereNull('reference_type')
                ->where('narration', 'like', '%(ProductSale #%')
                ->orderBy('id')
                ->each(function (PartyBalance $balance) use ($dryRun, &$updatedParty, &$ambiguous): void {
                    if (! preg_match('/\(ProductSale #(\d+)\)/', (string) $balance->narration, $matches)) {
                        $ambiguous++;
                        $this->warn("Ambiguous PartyBalance #{$balance->id} narration.");

                        return;
                    }

                    $saleId = (int) $matches[1];
                    $duplicate = PartyBalance::query()
                        ->where('reference_type', 'product_sale')
                        ->where('reference_id', $saleId)
                        ->where('id', '!=', $balance->id)
                        ->exists();

                    if ($duplicate) {
                        $ambiguous++;
                        $this->warn("Duplicate product_sale reference for PartyBalance #{$balance->id}.");

                        return;
                    }

                    if (! $dryRun) {
                        $balance->update([
                            'reference_type' => 'product_sale',
                            'reference_id' => $saleId,
                        ]);
                    }

                    $updatedParty++;
                    $this->line(($dryRun ? '[dry-run] ' : '')."PartyBalance #{$balance->id} => product_sale#{$saleId}");
                });

            AccountPayable::query()
                ->where('amount_type', 'company_balance_payment')
                ->where(function ($query): void {
                    $query->whereNull('legacy_payment_row')
                        ->orWhere('legacy_payment_row', '!=', 'reconciled');
                })
                ->orderBy('id')
                ->each(function (AccountPayable $payable) use ($dryRun, &$reconciledPayables, &$ambiguous): void {
                    $payment = CompanyBalancePayment::query()->find($payable->model_id);
                    if (! $payment || ! $payment->company_balance_id) {
                        $ambiguous++;
                        $this->warn("Unresolved legacy payable #{$payable->id}.");

                        return;
                    }

                    $original = AccountPayable::query()
                        ->where('amount_type', 'product_purchase')
                        ->where(function ($query) use ($payment): void {
                            $query->where('company_balance_id', $payment->company_balance_id)
                                ->orWhere(function ($inner) use ($payment): void {
                                    $inner->whereNull('company_balance_id')
                                        ->where('model_id', $payment->company_balance_id);
                                });
                        })
                        ->first();

                    if (! $original) {
                        $ambiguous++;
                        $this->warn("No original payable for legacy payment payable #{$payable->id}.");

                        return;
                    }

                    if (! $dryRun) {
                        $paid = FinancialAmount::fromDecimalString((string) ($original->paid_amount ?? 0))
                            ->add(FinancialAmount::fromDecimalString((string) $payable->total_amount));
                        $total = FinancialAmount::fromDecimalString((string) $original->total_amount);
                        $remaining = $total->subtract($paid);

                        $original->update([
                            'company_balance_id' => $payment->company_balance_id,
                            'paid_amount' => $paid->toDecimalString(),
                            'remaining_amount' => $remaining->isNegative() ? '0.00' : $remaining->toDecimalString(),
                            'amount_status' => $remaining->isZero() || $remaining->isNegative()
                                ? 'paid'
                                : ($paid->isPositive() ? 'pending' : 'unpaid'),
                        ]);

                        $payable->update([
                            'company_balance_payment_id' => $payment->id,
                            'company_balance_id' => $payment->company_balance_id,
                            'legacy_payment_row' => 'reconciled',
                        ]);

                        $hasAllocation = PaymentAllocation::query()
                            ->where('payment_type', $payment->getMorphClass())
                            ->where('payment_id', $payment->id)
                            ->exists();

                        if (! $hasAllocation) {
                            PaymentAllocation::query()->create([
                                'payment_type' => $payment->getMorphClass(),
                                'payment_id' => $payment->id,
                                'obligation_type' => (new CompanyBalance)->getMorphClass(),
                                'obligation_id' => $payment->company_balance_id,
                                'allocated_amount' => FinancialAmount::fromDecimalString((string) $payment->paid_amount)->toDecimalString(),
                                'status' => PaymentAllocation::STATUS_POSTED,
                                'idempotency_key' => 'legacy-payment-'.$payment->id,
                                'addedby' => $payment->addedby,
                            ]);
                        }
                    }

                    $reconciledPayables++;
                    $this->line(($dryRun ? '[dry-run] ' : '')."Reconciled legacy payable #{$payable->id}");
                });
        });

        $this->info("Company references: {$updatedCompany}");
        $this->info("Party references: {$updatedParty}");
        $this->info("Legacy payables reconciled: {$reconciledPayables}");
        $this->info("Ambiguous/unresolved: {$ambiguous}");

        return $ambiguous > 0 ? self::FAILURE : self::SUCCESS;
    }
}
