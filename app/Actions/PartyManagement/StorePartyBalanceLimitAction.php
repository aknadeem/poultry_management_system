<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyBalanceLimit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePartyBalanceLimitAction
{
    public function execute(array $data, int $userId): PartyBalanceLimit
    {
        return DB::transaction(function () use ($data, $userId) {
            $limitId = (int) ($data['party_balance_limit_id'] ?? 0);

            if ($limitId > 0) {
                $limit = PartyBalanceLimit::find($limitId);
                if (! $limit) {
                    throw ValidationException::withMessages([
                        'party_balance_limit_id' => 'No data found against this id',
                    ]);
                }

                $limit->update([
                    'party_id' => $data['party_id'],
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'debit_limit' => $data['debit_limit'],
                    'credit_limit' => $data['credit_limit'],
                    'updatedby' => $userId,
                ]);

                return $limit->fresh();
            }

            return PartyBalanceLimit::create([
                'party_id' => $data['party_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'debit_limit' => $data['debit_limit'],
                'credit_limit' => $data['credit_limit'],
                'addedby' => $userId,
            ]);
        });
    }
}
