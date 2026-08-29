<?php

namespace App\Support;

use App\Models\AccountPayable;
use Carbon\Carbon;

class AccountPayablePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(AccountPayable $payable): array
    {
        $entryDate = $payable->entry_date;

        return [
            'id' => $payable->id,
            'entry_date' => $entryDate
                ? Carbon::parse($entryDate)->format('Y-m-d')
                : null,
            'entry_date_label' => $entryDate
                ? Carbon::parse($entryDate)->format('d M, Y')
                : '—',
            'total_amount' => $payable->total_amount,
            'paid_amount' => $payable->paid_amount,
            'remaining_amount' => $payable->remaining_amount,
            'amount_status' => $payable->amount_status
                ? ucfirst((string) $payable->amount_status)
                : '—',
            'amount_type' => $payable->amount_type
                ? ucfirst((string) $payable->amount_type)
                : '—',
            'narration' => $payable->narration,
        ];
    }
}
