<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Http\Controllers\Controller;
use App\Models\PartyBalance;
use App\Queries\PartyBalanceQuery;
use Inertia\Inertia;
use Inertia\Response;

class PartyBalanceController extends Controller
{
    public function index(PartyBalanceQuery $query): Response
    {
        $this->authorize('viewAny', PartyBalance::class);

        $balances = $query->paginate();

        return Inertia::render('PartyBalances/Index', [
            'balances' => $balances->through(fn (PartyBalance $balance): array => $this->listItem($balance)),
            'filters' => $query->filters(),
        ]);
    }

    public function show(PartyBalance $partybalance): Response
    {
        $this->authorize('view', $partybalance);
        $partybalance->load('party:id,name,cnic_no');

        return Inertia::render('PartyBalances/Show', [
            'balance' => $this->listItem($partybalance),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function listItem(PartyBalance $balance): array
    {
        return [
            'id' => $balance->id,
            'party_name' => $balance->party?->name,
            'party_cnic' => $balance->party?->cnic_no,
            'total_amount' => $balance->total_amount,
            'paid_amount' => $balance->paid_amount,
            'remaining_amount' => $balance->remaining_amount,
            'amount_type' => $balance->type_value['val'] ?? null,
            'amount_type_color' => $balance->type_value['color'] ?? 'secondary',
            'payment_status' => $balance->payment_status_val['val'] ?? null,
            'payment_status_color' => $balance->payment_status_val['color'] ?? 'secondary',
            'transaction_date' => optional($balance->transaction_date)->format('d M, Y'),
            'narration' => $balance->narration,
        ];
    }
}
