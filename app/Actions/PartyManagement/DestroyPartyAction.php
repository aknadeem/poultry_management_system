<?php

namespace App\Actions\PartyManagement;

use App\Models\Party;

class DestroyPartyAction
{
    public function execute(Party $party): void
    {
        $party->delete();
    }
}
