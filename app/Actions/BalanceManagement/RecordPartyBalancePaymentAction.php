<?php

namespace App\Actions\BalanceManagement;

use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPartyBalancePaymentAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $chequeFile, ?object $imageFile, int $userId): PartyBalancePayment
    {
        return DB::transaction(function () use ($data, $chequeFile, $imageFile, $userId) {
            $partyBalance = PartyBalance::find($data['balance_id']);
            if (! $partyBalance) {
                throw ValidationException::withMessages([
                    'balance_id' => 'No Balance Found against this record',
                ]);
            }

            $chequePicture = $this->uploadService->store($chequeFile, 'parties/payment_receive/cheque');
            $imageName = $this->uploadService->store($imageFile, 'parties/payment_receive');

            $payment = PartyBalancePayment::create([
                'party_balance_id' => $data['balance_id'],
                'party_id' => $data['party_id'],
                'paid_amount' => $data['amount_payment'],
                'paid_date' => $data['paid_date'],
                'payment_option' => $data['payment_option'],
                'cheque_date' => $data['cheque_date'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'cheque_picture' => $chequePicture,
                'invoice_picture' => $imageName,
                'narration' => $data['description'] ?? null,
                'addedby' => $userId,
            ]);

            $this->balanceService->applyPartyBalancePayment(
                $partyBalance,
                (float) $data['amount_payment'],
                $userId
            );

            return $payment;
        });
    }
}
