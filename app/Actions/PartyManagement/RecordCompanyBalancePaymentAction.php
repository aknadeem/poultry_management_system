<?php

namespace App\Actions\PartyManagement;

use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use App\Services\PayableService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordCompanyBalancePaymentAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
        private PayableService $payableService,
    ) {
    }

    public function execute(array $data, ?object $chequeFile, ?object $imageFile, int $userId): CompanyBalancePayment
    {
        return DB::transaction(function () use ($data, $chequeFile, $imageFile, $userId) {
            $companyBalance = CompanyBalance::find($data['company_balance_id']);
            if (! $companyBalance) {
                throw ValidationException::withMessages([
                    'company_balance_id' => 'No Balance Found against this record',
                ]);
            }

            $chequePicture = $this->uploadService->store($chequeFile, 'companies/payment/cheque');
            $imageName = $this->uploadService->store($imageFile, 'companies/payment');

            $payment = CompanyBalancePayment::create([
                'company_balance_id' => $data['company_balance_id'],
                'party_company_id' => $data['party_company_id'],
                'paid_amount' => $data['amount_payment'],
                'payment_option' => $data['payment_option'],
                'cheque_date' => $data['cheque_date'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'cheque_picture' => $chequePicture,
                'invoice_picture' => $imageName,
                'description' => $data['description'] ?? null,
                'addedby' => $userId,
            ]);

            $this->balanceService->applyCompanyBalancePayment(
                $companyBalance,
                (float) $data['amount_payment'],
                $userId
            );

            $this->payableService->recordPaidPayable(
                $payment->id,
                (float) $data['amount_payment'],
                'company_balance_payment',
                $userId,
                Carbon::today()
            );

            return $payment;
        });
    }
}
