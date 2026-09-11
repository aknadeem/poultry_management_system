<?php

namespace App\Console\Commands;

use App\Services\FinancialReconciliationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('finance:reconcile {--report : Print a human-readable report}')]
#[Description('Reconcile financial balances, allocations, and payables')]
class FinanceReconcileCommand extends Command
{
    public function handle(FinancialReconciliationService $reconciliationService): int
    {
        $report = $reconciliationService->report();

        Log::info('finance.reconcile.completed', [
            'clean' => $report['clean'],
            'summary' => $report['summary'],
            'issue_count' => count($report['issues']),
        ]);

        if ($this->option('report') || ! $report['clean']) {
            $this->line($report['clean'] ? 'Financial reconciliation is clean.' : 'Financial reconciliation found drift:');
            foreach ($report['issues'] as $issue) {
                $this->warn("[{$issue['code']}] {$issue['message']}");
            }
        }

        if (! $report['clean'] && config('financial.reconciliation.notify')) {
            $emails = config('financial.reconciliation.notification_emails', []);
            if ($emails !== []) {
                try {
                    Mail::raw(
                        'Financial reconciliation found '.count($report['issues']).' issue(s). Check application logs for details.',
                        function ($message) use ($emails): void {
                            $message->to($emails)->subject('Financial reconciliation drift detected');
                        }
                    );
                } catch (\Throwable $exception) {
                    Log::warning('finance.reconcile.notify_failed', [
                        'message' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return $report['clean'] ? self::SUCCESS : self::FAILURE;
    }
}
