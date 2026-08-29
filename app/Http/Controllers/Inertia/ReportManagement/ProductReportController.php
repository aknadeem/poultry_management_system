<?php

namespace App\Http\Controllers\Inertia\ReportManagement;

use App\Http\Controllers\Controller;
use App\Models\PartyCompany;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductSale;
use App\Support\ReportPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductReportController extends Controller
{
    public function productReport(Request $request): Response
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $products = [];
        if ($fromDate && $toDate) {
            $products = Product::query()
                ->whereBetween('created_at', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
                ->with('company:id,company_name', 'category:id,name')
                ->orderByDesc('id')
                ->get(['id', 'product_name', 'product_group', 'product_code', 'party_company_id', 'product_category_id', 'quantity'])
                ->map(fn (Product $product): array => ReportPresenter::product($product, $fromDate, $toDate))
                ->all();
        }

        return Inertia::render('Reports/ProductReport', [
            'products' => $products,
            'companies' => $this->activeCompanies(),
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
            $purchases = ProductPurchase::query()
                ->whereBetween('purchase_date', [$fromDate, $toDate])
                ->with('company:id,company_name,party_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn (ProductPurchase $purchase): array => ReportPresenter::productPurchase($purchase, $fromDate, $toDate))
                ->all();
        }

        return Inertia::render('Reports/ProductPurchaseReport', [
            'purchases' => $purchases,
            'companies' => $this->activeCompanies(),
            'filters' => [
                'from_date' => $fromDate ?? '',
                'to_date' => $toDate ?? '',
                'searched' => (bool) ($fromDate && $toDate),
            ],
        ]);
    }

    public function saleReport(Request $request): Response
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sales = [];
        if ($fromDate && $toDate) {
            $sales = ProductSale::query()
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->with('party:id,name,is_customer', 'company:id,company_name,party_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn (ProductSale $sale): array => ReportPresenter::productSale($sale, $fromDate, $toDate))
                ->all();
        }

        return Inertia::render('Reports/ProductSaleReport', [
            'sales' => $sales,
            'companies' => $this->activeCompanies(),
            'filters' => [
                'from_date' => $fromDate ?? '',
                'to_date' => $toDate ?? '',
                'searched' => (bool) ($fromDate && $toDate),
            ],
        ]);
    }

    /**
     * @return list<array{id: int, company_name: string}>
     */
    private function activeCompanies(): array
    {
        return PartyCompany::query()
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->all();
    }
}
