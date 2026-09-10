<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\BalanceManagement\RecordPartyBalancePaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BalanceManagement\StorePartyBalancePaymentRequest;
use App\Models\PartyBalance;
use App\Models\PartyBalancePayment;
use App\Queries\PartyBalanceQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
            'today' => now()->toDateString(),
        ]);
    }

    public function show(PartyBalance $partybalance): Response
    {
        $this->authorize('view', $partybalance);
        $partybalance->load('party:id,name,cnic_no');

        $payments = PartyBalancePayment::query()
            ->where('party_balance_id', $partybalance->id)
            ->with(['party:id,name', 'user:id,name'])
            ->orderByDesc('id')
            ->get();

        return Inertia::render('PartyBalances/Show', [
            'balance' => $this->listItem($partybalance),
            'payments' => $payments->map(fn (PartyBalancePayment $payment): array => $this->paymentItem($payment))->values()->all(),
            'today' => now()->toDateString(),
        ]);
    }

    public function store(
        StorePartyBalancePaymentRequest $request,
        RecordPartyBalancePaymentAction $action,
    ): RedirectResponse {
        $this->authorize('create', PartyBalance::class);

        $action->execute(
            $request->validated(),
            $request->file('cheque_picture'),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->back()
            ->with('swal_notification', [
                'title' => 'Payment Added',
                'icon_type' => 'success',
                'message' => 'Payment recorded successfully',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function listItem(PartyBalance $balance): array
    {
        $balance->loadMissing('party:id,name,cnic_no');

        return [
            'id' => $balance->id,
            'party_id' => $balance->party_id,
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

    /**
     * @return array<string, mixed>
     */
    private function paymentItem(PartyBalancePayment $payment): array
    {
        return [
            'id' => $payment->id,
            'party_name' => $payment->party?->name,
            'paid_amount' => $payment->paid_amount,
            'paid_date' => $payment->paid_date,
            'payment_option' => $payment->payment_option,
            'payment_option_label' => match (strtolower((string) $payment->payment_option)) {
                'cash', '1' => 'Cash',
                'cheque', '2' => 'Cheque',
                'other' => 'Other',
                default => (string) $payment->payment_option,
            },
            'cheque_date' => $payment->cheque_date,
            'bank_name' => $payment->bank_name,
            'narration' => $payment->narration,
            'added_by' => $payment->user?->name,
            'created_at' => $payment->created_at?->format('d M, Y h:i:s A'),
        ];
    }
}
