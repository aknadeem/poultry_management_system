<?php

namespace App\Http\Controllers\Inertia\BalanceManagement;

use App\Actions\PartyManagement\RecordCompanyBalancePaymentAction;
use App\Actions\PartyManagement\ReverseCompanyBalancePaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\ReverseCompanyBalancePaymentRequest;
use App\Http\Requests\PartyManagement\StoreCompanyBalancePaymentRequest;
use App\Models\CompanyBalance;
use App\Models\CompanyBalancePayment;
use App\Models\PartyCompany;
use App\Queries\CompanyBalanceQuery;
use App\Support\CompanyBalancePresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CompanyBalanceController extends Controller
{
    public function index(CompanyBalanceQuery $query): Response
    {
        $this->authorize('viewAny', CompanyBalance::class);

        $balances = $query->paginate();

        return Inertia::render('CompanyBalances/Index', [
            'balances' => $balances->through(fn (CompanyBalance $balance): array => CompanyBalancePresenter::listItem($balance)),
            'filters'  => $query->filters(),
        ]);
    }

    public function show(CompanyBalance $companyBalance): Response
    {
        $this->authorize('view', $companyBalance);

        $companyBalance->load('company:id,company_name,company_logo');

        $payments = CompanyBalancePayment::where('company_balance_id', $companyBalance->id)
            ->with('company:id,company_name', 'addedBy:id,name')
            ->orderBy('id', 'DESC')
            ->get();

        return Inertia::render('CompanyBalances/Show', [
            'balance'  => CompanyBalancePresenter::detail($companyBalance),
            'payments' => $payments->map(fn (CompanyBalancePayment $p): array => CompanyBalancePresenter::payment($p))->values()->all(),
            'companies' => PartyCompany::query()->get(['id', 'company_name']),
        ]);
    }

    public function store(StoreCompanyBalancePaymentRequest $request, RecordCompanyBalancePaymentAction $action): RedirectResponse
    {
        $this->authorize('create', CompanyBalance::class);

        $action->execute(
            $request->validated(),
            $request->file('cheque_picture'),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->back()
            ->with('swal_notification', [
                'title'     => 'Payment Added',
                'icon_type' => 'success',
                'message'   => 'Payment recorded successfully',
            ]);
    }

    public function reverse(
        ReverseCompanyBalancePaymentRequest $request,
        CompanyBalancePayment $payment,
        ReverseCompanyBalancePaymentAction $action,
    ): RedirectResponse {
        $balance = CompanyBalance::query()->findOrFail($payment->company_balance_id);
        $this->authorize('update', $balance);

        $action->execute(
            $payment,
            (int) Auth::id(),
            $request->validated('reversal_reason'),
        );

        return redirect()
            ->back()
            ->with('swal_notification', [
                'title' => 'Payment Reversed',
                'icon_type' => 'success',
                'message' => 'Payment reversed successfully',
            ]);
    }
}
