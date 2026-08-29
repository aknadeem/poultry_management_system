<?php

namespace App\Http\Controllers\Inertia\ReportManagement;

use App\Http\Controllers\Controller;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\Party;
use App\Support\ReportPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChickReportController extends Controller
{
    public function saleReport(Request $request): Response
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sales = [];
        if ($fromDate && $toDate) {
            $sales = ChickenSale::query()
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->orderByDesc('id')
                ->with('customer:id,name,cnic_no,contact_no,is_customer')
                ->get()
                ->map(fn (ChickenSale $sale): array => ReportPresenter::chickSale($sale, $fromDate, $toDate))
                ->all();
        }

        $customers = Party::query()
            ->where('is_customer', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'cnic_no', 'contact_no']);

        return Inertia::render('Reports/ChickSaleReport', [
            'sales' => $sales,
            'customers' => $customers,
            'filters' => [
                'from_date' => $fromDate ?? '',
                'to_date' => $toDate ?? '',
                'searched' => (bool) ($fromDate && $toDate),
            ],
        ]);
    }

    public function purchaseReport(Request $request): Response
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $purchases = [];
        if ($fromDate && $toDate) {
            $purchases = ChickPurchase::query()
                ->whereBetween('purchase_date', [$fromDate, $toDate])
                ->orderByDesc('id')
                ->with('company:id,company_name')
                ->get()
                ->map(fn (ChickPurchase $purchase): array => ReportPresenter::chickPurchase($purchase, $fromDate, $toDate))
                ->all();
        }

        $customers = Party::query()
            ->where('is_customer', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'cnic_no', 'contact_no']);

        return Inertia::render('Reports/ChickPurchaseReport', [
            'purchases' => $purchases,
            'customers' => $customers,
            'filters' => [
                'from_date' => $fromDate ?? '',
                'to_date' => $toDate ?? '',
                'searched' => (bool) ($fromDate && $toDate),
            ],
        ]);
    }
}
