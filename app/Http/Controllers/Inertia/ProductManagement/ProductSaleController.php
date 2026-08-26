<?php

namespace App\Http\Controllers\Inertia\ProductManagement;

use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Actions\ProductManagement\DestroyProductSaleAction;
use App\Actions\ProductManagement\RecordProductSaleRebateAction;
use App\Actions\ProductManagement\StoreProductSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\RecordProductSaleRebateRequest;
use App\Http\Requests\ProductManagement\StoreProductSaleRequest;
use App\Models\ProductSale;
use App\Models\ProductSaleRebate;
use App\Queries\ProductSaleQuery;
use App\Support\ProductLookups;
use App\Support\ProductPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductSaleController extends Controller
{
    public function index(ProductSaleQuery $query): Response
    {
        $this->authorize('viewAny', ProductSale::class);

        $sales = $query->paginate();

        return Inertia::render('ProductSales/Index', [
            'sales' => $sales->through(
                fn (ProductSale $sale): array => ProductPresenter::saleList($sale)
            ),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ProductSale::class);

        return Inertia::render('ProductSales/Create', ProductLookups::saleFormOptions());
    }

    public function store(StoreProductSaleRequest $request, StoreProductSaleAction $action): RedirectResponse
    {
        $this->authorize('create', ProductSale::class);

        $action->execute($request->validated(), $request->file('invoice_picture'), (int) Auth::id());

        return redirect()
            ->route('inertia.product-sales.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(ProductSale $productSale): Response
    {
        $this->authorize('view', $productSale);

        return Inertia::render('ProductSales/Show', [
            'sale' => ProductPresenter::sale($productSale),
        ]);
    }

    public function destroy(
        ProductSale $productSale,
        DestroyProductSaleAction $action,
    ): RedirectResponse {
        $this->authorize('delete', $productSale);
        $action->execute($productSale, (int) Auth::id());

        return redirect()
            ->route('inertia.product-sales.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    public function rebates(): Response
    {
        $this->authorize('viewAny', ProductSale::class);

        $rebates = ProductSaleRebate::query()->latest('id')->get();

        return Inertia::render('ProductSales/Rebates', [
            'rebates' => $rebates->map(fn (ProductSaleRebate $rebate): array => ProductPresenter::saleRebate($rebate)),
        ]);
    }

    public function rebate(
        RecordProductSaleRebateRequest $request,
        RecordProductSaleRebateAction $action,
    ): RedirectResponse {
        $this->authorize('update', new ProductSale);

        $data = $request->validated();
        abort_unless($data['from_page'] === 'ProductSaleDetail', 403);

        $action->execute($data, (int) Auth::id());

        return back()->with('swal_notification', [
            'title' => 'Updated',
            'icon_type' => 'success',
            'message' => 'Rebate recorded successfully',
        ]);
    }

    public function toggleStatus(
        ProductSale $productSale,
        UpdateActiveStatusAction $action,
    ): RedirectResponse {
        $this->authorize('update', $productSale);
        $action->execute($productSale->id, 'product_sales', (int) Auth::id());

        return redirect()
            ->route('inertia.product-sales.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
