<?php

namespace App\Http\Controllers\Inertia\BalanceManagement;

use App\Http\Controllers\Controller;
use App\Models\BrokerBalance;
use App\Queries\BrokerBalanceQuery;
use App\Support\BrokerBalancePresenter;
use Inertia\Inertia;
use Inertia\Response;

class BrokerBalanceController extends Controller
{
    public function index(BrokerBalanceQuery $query): Response
    {
        $this->authorize('viewAny', BrokerBalance::class);

        $balances = $query->paginate();

        return Inertia::render('BrokerBalances/Index', [
            'balances' => $balances->through(
                fn (BrokerBalance $balance): array => BrokerBalancePresenter::listItem($balance)
            ),
            'filters' => $query->filters(),
        ]);
    }
}
