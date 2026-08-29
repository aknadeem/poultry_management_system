<?php

namespace App\Http\Controllers\Inertia\ProductManagement;

use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Actions\ProductManagement\DestroyProductPurchaseAction;
use App\Actions\ProductManagement\RecordProductSaleRebateAction;
use App\Actions\ProductManagement\StoreProductPurchaseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\RecordProductSaleRebateRequest;
use App\Http\Requests\ProductManagement\StoreProductPurchaseRequest;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseRebate;
use App\Queries\ProductPurchaseQuery;
use App\Support\ProductLookups;
use App\Support\ProductPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductPurchaseController extends Controller
{
    public function index(ProductPurchaseQuery $query): Response
    {
        $this->authorize('viewAny', ProductPurchase::class);

        $purchases = $query->paginate();

        return Inertia::render('ProductPurchases/Index', [
            'purchases' => $purchases->through(
                fn (ProductPurchase $purchase): array => ProductPresenter::purchaseList($purchase)
            ),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ProductPurchase::class);

        return Inertia::render('ProductPurchases/Create', ProductLookups::formOptions());
    }

    public function store(StoreProductPurchaseRequest $request, StoreProductPurchaseAction $action): RedirectResponse
    {
        $this->authorize('create', ProductPurchase::class);

        $action->execute($request->validated(), (int) Auth::id());

        return redirect()
            ->route('inertia.product-purchases.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(ProductPurchase $productPurchase): Response
    {
        $this->authorize('view', $productPurchase);

        return Inertia::render('ProductPurchases/Show', [
            'purchase' => ProductPresenter::purchase($productPurchase),
        ]);
    }

    public function invoice(ProductPurchase $productPurchase): Response
    {
        $this->authorize('view', $productPurchase);

        return Inertia::render('ProductPurchases/Invoice', [
            'purchase' => ProductPresenter::purchase($productPurchase),
        ]);
    }

    public function destroy(
        ProductPurchase $productPurchase,
        DestroyProductPurchaseAction $action,
    ): RedirectResponse {
        $this->authorize('delete', $productPurchase);
        $action->execute($productPurchase, (int) Auth::id());

        return redirect()
            ->route('inertia.product-purchases.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    public function rebates(): Response
    {
        $this->authorize('viewAny', ProductPurchase::class);

        $rebates = ProductPurchaseRebate::query()->latest('id')->get();

        return Inertia::render('ProductPurchases/Rebates', [
            'rebates' => $rebates->map(fn (ProductPurchaseRebate $rebate): array => ProductPresenter::rebate($rebate)),
        ]);
    }

    public function rebate(
        RecordProductSaleRebateRequest $request,
        RecordProductSaleRebateAction $action,
    ): RedirectResponse {
        $this->authorize('update', new ProductPurchase);

        $data = $request->validated();
        abort_unless($data['from_page'] === 'ProductPurchaseDetail', 403);

        $action->execute($data, (int) Auth::id());

        return back()->with('swal_notification', [
            'title' => 'Updated',
            'icon_type' => 'success',
            'message' => 'Rebate recorded successfully',
        ]);
    }

    public function toggleStatus(
        ProductPurchase $productPurchase,
        UpdateActiveStatusAction $action,
    ): RedirectResponse {
        $this->authorize('update', $productPurchase);
        $action->execute($productPurchase->id, 'product_purchases', (int) Auth::id());

        return redirect()
            ->route('inertia.product-purchases.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
