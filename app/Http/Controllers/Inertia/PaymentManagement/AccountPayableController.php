<?php

namespace App\Http\Controllers\Inertia\PaymentManagement;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable;
use App\Queries\AccountPayableQuery;
use App\Support\AccountPayablePresenter;
use Inertia\Inertia;
use Inertia\Response;

class AccountPayableController extends Controller
{
    public function index(AccountPayableQuery $query): Response
    {
        $this->authorize('viewAny', AccountPayable::class);

        $payables = $query->paginate();

        return Inertia::render('AccountPayables/Index', [
            'payables' => $payables->through(
                fn (AccountPayable $payable): array => AccountPayablePresenter::listItem($payable)
            ),
            'filters' => $query->filters(),
        ]);
    }
}
