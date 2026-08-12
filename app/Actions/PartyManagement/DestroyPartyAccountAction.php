<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyAccount;
use Illuminate\Support\Facades\DB;

class DestroyPartyAccountAction
{
    public function execute(PartyAccount $account): void
    {
        DB::transaction(function () use ($account) {
            $account->delete();
        });
    }
}
