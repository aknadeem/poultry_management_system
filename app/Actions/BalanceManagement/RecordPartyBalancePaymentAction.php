<?php

namespace App\Actions\BalanceManagement;

use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Services\FileUploadService;
use App\Services\FinancialTransactionService;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPartyBalancePaymentAction
{
    public function __construct(
        private FileUploadService $uploadService,
        private PaymentAllocationService $allocationService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(array $data, ?object $chequeFile, ?object $imageFile, int $userId): PartyBalancePayment
    {
        $stagedCheque = null;
        $stagedImage = null;

        try {
            $stagedCheque = $this->uploadService->store($chequeFile, 'parties/payment_receive/cheque');
            $stagedImage = $this->uploadService->store($imageFile, 'parties/payment_receive');

            return DB::transaction(function () use ($data, $stagedCheque, $stagedImage, $userId) {
                $idempotencyKey = $data['idempotency_key'] ?? null;
                if (is_string($idempotencyKey) && $idempotencyKey !== '') {
                    $existing = PartyBalancePayment::query()
                        ->where('idempotency_key', $idempotencyKey)
                        ->first();
                    if ($existing) {
                        return $existing;
                    }
                }

                $partyBalance = PartyBalance::query()
                    ->lockForUpdate()
                    ->find($data['balance_id']);

                if (! $partyBalance) {
                    throw ValidationException::withMessages([
                        'balance_id' => 'No Balance Found against this record',
                    ]);
                }

                if ((int) $data['party_id'] !== (int) $partyBalance->party_id) {
                    throw ValidationException::withMessages([
                        'party_id' => 'Payment party does not match the selected balance.',
                    ]);
                }

                $payment = PartyBalancePayment::create([
                    'party_balance_id' => $partyBalance->id,
                    'party_id' => $partyBalance->party_id,
                    'paid_amount' => $data['amount_payment'],
                    'paid_date' => $data['paid_date'],
                    'payment_option' => $data['payment_option'],
                    'cheque_date' => $data['cheque_date'] ?? null,
                    'bank_name' => $data['bank_name'] ?? null,
                    'reference_no' => $data['reference_no'] ?? null,
                    'cheque_picture' => $stagedCheque,
                    'invoice_picture' => $stagedImage,
                    'narration' => $data['description'] ?? null,
                    'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== '' ? $idempotencyKey : null,
                    'payment_status' => 'posted',
                    'addedby' => $userId,
                ]);

                $this->allocationService->allocateToPartyBalance(
                    $payment,
                    (int) $partyBalance->id,
                    [
                        'amount' => $data['amount_payment'],
                        'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== ''
                            ? 'allocation-'.$idempotencyKey
                            : null,
                    ],
                    $userId,
                );

                $this->transactionService->post($payment, [
                    'transaction_type' => 'party_balance_payment',
                    'direction' => 'credit',
                    'amount' => $data['amount_payment'],
                    'transaction_date' => $data['paid_date'],
                    'narration' => $data['description'] ?? 'Party balance payment',
                    'idempotency_key' => is_string($idempotencyKey) && $idempotencyKey !== ''
                        ? 'txn-'.$idempotencyKey
                        : null,
                ], $userId);

                return $payment->fresh();
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('parties/payment_receive/cheque', $stagedCheque);
            $this->uploadService->delete('parties/payment_receive', $stagedImage);

            throw $exception;
        }
    }
}
