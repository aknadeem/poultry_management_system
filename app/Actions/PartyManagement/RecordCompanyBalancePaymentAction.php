<?php

namespace App\Actions\PartyManagement;

use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Services\FileUploadService;
use App\Services\FinancialTransactionService;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordCompanyBalancePaymentAction
{
    public function __construct(
        private FileUploadService $uploadService,
        private PaymentAllocationService $allocationService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(array $data, ?object $chequeFile, ?object $imageFile, int $userId): CompanyBalancePayment
    {
        $stagedCheque = null;
        $stagedImage = null;

        try {
            $stagedCheque = $this->uploadService->store($chequeFile, 'companies/payment/cheque');
            $stagedImage = $this->uploadService->store($imageFile, 'companies/payment');

            return DB::transaction(function () use ($data, $stagedCheque, $stagedImage, $userId) {
                $idempotencyKey = $data['idempotency_key'] ?? null;
                if (is_string($idempotencyKey) && $idempotencyKey !== '') {
                    $existing = CompanyBalancePayment::query()
                        ->where('idempotency_key', $idempotencyKey)
                        ->first();
                    if ($existing) {
                        return $existing;
                    }
                }

                $companyBalance = CompanyBalance::query()
                    ->lockForUpdate()
                    ->find($data['company_balance_id']);

                if (! $companyBalance) {
                    throw ValidationException::withMessages([
                        'company_balance_id' => 'No Balance Found against this record',
                    ]);
                }

                if ((int) $data['party_company_id'] !== (int) $companyBalance->company_id) {
                    throw ValidationException::withMessages([
                        'party_company_id' => 'Payment company does not match the selected balance.',
                    ]);
                }

                $payment = CompanyBalancePayment::create([
                    'company_balance_id' => $companyBalance->id,
                    'party_company_id' => $companyBalance->company_id,
                    'paid_amount' => $data['amount_payment'],
                    'payment_option' => $data['payment_option'],
                    'cheque_date' => $data['cheque_date'] ?? null,
                    'bank_name' => $data['bank_name'] ?? null,
                    'cheque_picture' => $stagedCheque,
                    'invoice_picture' => $stagedImage,
                    'description' => $data['description'] ?? null,
                    'reference_no' => $data['reference_no'] ?? null,
                    'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== '' ? $idempotencyKey : null,
                    'payment_status' => 'posted',
                    'addedby' => $userId,
                ]);

                $this->allocationService->allocateToCompanyBalance(
                    $payment,
                    (int) $companyBalance->id,
                    [
                        'amount' => $data['amount_payment'],
                        'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== ''
                            ? 'allocation-'.$idempotencyKey
                            : null,
                    ],
                    $userId,
                );

                $this->transactionService->post($payment, [
                    'transaction_type' => 'company_balance_payment',
                    'direction' => 'credit',
                    'amount' => $data['amount_payment'],
                    'transaction_date' => now()->toDateString(),
                    'narration' => $data['description'] ?? 'Company balance payment',
                    'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== ''
                        ? 'txn-'.$idempotencyKey
                        : null,
                ], $userId);

                return $payment->fresh();
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('companies/payment/cheque', $stagedCheque);
            $this->uploadService->delete('companies/payment', $stagedImage);

            throw $exception;
        }
    }
}
