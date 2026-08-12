<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePartyAccountAction
{
    public function execute(array $data, int $userId): PartyAccount
    {
        return DB::transaction(function () use ($data, $userId) {
            $accountId = (int) ($data['party_account_id'] ?? 0);

            if ($accountId > 0) {
                $account = PartyAccount::find($accountId);
                if (! $account) {
                    throw ValidationException::withMessages([
                        'party_account_id' => 'No data found against this id',
                    ]);
                }

                $account->update([
                    'party_id' => $data['party_id'],
                    'account_title' => $data['account_title'],
                    'account_number' => $data['account_number'],
                    'bank_name' => $data['bank_name'],
                    'opening_balance' => $data['opening_balance'],
                    'updatedby' => $userId,
                ]);

                return $account->fresh();
            }

            return PartyAccount::create([
                'party_id' => $data['party_id'],
                'account_title' => $data['account_title'],
                'account_number' => $data['account_number'],
                'bank_name' => $data['bank_name'],
                'opening_balance' => $data['opening_balance'],
                'dr' => $data['opening_balance'],
                'addedby' => $userId,
            ]);
        });
    }
}
