<?php

namespace App\Support;

use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;

class CompanyBalancePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(CompanyBalance $balance): array
    {
        $balance->loadMissing('company:id,company_name,company_logo');

        return [
            'id' => $balance->id,
            'company_id' => $balance->company_id,
            'company_name' => $balance->company?->company_name,
            'type' => ucfirst(str_replace('_', ' ', (string) $balance->type)),
            'type_raw' => $balance->type,
            'total_amount' => $balance->total_amount,
            'paid_amount' => $balance->paid_amount,
            'remaining_amount' => $balance->remaining_amount,
            'status' => self::statusLabel($balance->status),
            'status_raw' => $balance->status,
            'dr' => $balance->dr,
            'cr' => $balance->cr,
            'created_at' => $balance->created_at?->format('d M, Y'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(CompanyBalance $balance): array
    {
        $balance->loadMissing('company:id,company_name,company_logo');

        return array_merge(self::listItem($balance), [
            'company_logo' => $balance->company?->company_logo,
            'company_logo_url' => $balance->company?->company_logo
                ? asset('storage/party/company/'.$balance->company->company_logo)
                : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function payment(CompanyBalancePayment $payment): array
    {
        $payment->loadMissing(['company:id,company_name', 'addedBy:id,name']);

        return [
            'id' => $payment->id,
            'company_name' => $payment->company?->company_name,
            'paid_amount' => $payment->paid_amount,
            'payment_option' => $payment->payment_option,
            'payment_option_label' => self::paymentOptionLabel($payment->payment_option),
            'cheque_date' => $payment->cheque_date,
            'bank_name' => $payment->bank_name,
            'cheque_picture' => $payment->cheque_picture
                ? asset('storage/companies/payment/cheque/'.$payment->cheque_picture)
                : null,
            'invoice_picture' => $payment->invoice_picture
                ? asset('storage/companies/payment/'.$payment->invoice_picture)
                : null,
            'description' => $payment->description,
            'payment_status' => $payment->payment_status ?? 'posted',
            'reversal_reason' => $payment->reversal_reason,
            'added_by' => $payment->addedBy?->name,
            'created_at' => $payment->created_at?->format('d M, Y h:i:s A'),
        ];
    }

    private static function statusLabel(mixed $status): string
    {
        return match (strtolower((string) $status)) {
            'paid' => 'Paid',
            'pending' => 'Pending',
            'unpaid' => 'Unpaid',
            default => (string) $status,
        };
    }

    private static function paymentOptionLabel(mixed $option): string
    {
        return match (strtolower((string) $option)) {
            'cash', '1' => 'Cash',
            'cheque', '2' => 'Cheque',
            'other' => 'Other',
            default => (string) $option,
        };
    }
}
