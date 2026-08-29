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
        return [
            'id'               => $balance->id,
            'company_id'       => $balance->company_id,
            'company_name'     => $balance->company?->company_name,
            'type'             => ucfirst(str_replace('_', ' ', (string) $balance->type)),
            'type_raw'         => $balance->type,
            'total_amount'     => $balance->total_amount,
            'paid_amount'      => $balance->paid_amount,
            'remaining_amount' => $balance->remaining_amount,
            'status'           => $balance->status,
            'dr'               => $balance->dr,
            'cr'               => $balance->cr,
            'created_at'       => $balance->created_at?->format('d M, Y'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(CompanyBalance $balance): array
    {
        return array_merge(self::listItem($balance), [
            'company_logo' => $balance->company?->company_logo,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function payment(CompanyBalancePayment $payment): array
    {
        return [
            'id'               => $payment->id,
            'company_name'     => $payment->company?->company_name,
            'paid_amount'      => $payment->paid_amount,
            'payment_option'   => $payment->payment_option,
            'cheque_date'      => $payment->cheque_date,
            'bank_name'        => $payment->bank_name,
            'cheque_picture'   => $payment->cheque_picture,
            'invoice_picture'  => $payment->invoice_picture,
            'description'      => $payment->description,
            'added_by'         => $payment->addedBy?->name,
            'created_at'       => $payment->created_at?->format('d M, Y'),
        ];
    }
}
