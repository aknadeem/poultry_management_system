<?php

namespace App\Support;

use App\Models\BrokerBalance;

class BrokerBalancePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(BrokerBalance $balance): array
    {
        $balance->loadMissing('broker:id,name');

        return [
            'id' => $balance->id,
            'broker_id' => $balance->broker_id,
            'broker_name' => $balance->broker?->name,
            'narration' => $balance->narration,
            'total_amount' => $balance->total_amount,
            'paid_amount' => $balance->paid_amount,
            'remaining_amount' => $balance->remaining_amount,
            'status' => ucfirst((string) ($balance->status ?? 'unpaid')),
            'created_at' => $balance->created_at?->format('d M, Y'),
        ];
    }
}
