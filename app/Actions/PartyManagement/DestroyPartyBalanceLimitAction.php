<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyBalanceLimit;
use Illuminate\Support\Facades\DB;

class DestroyPartyBalanceLimitAction
{
    public function execute(PartyBalanceLimit $limit): void
    {
        DB::transaction(function () use ($limit) {
            $limit->delete();
        });
    }
}
