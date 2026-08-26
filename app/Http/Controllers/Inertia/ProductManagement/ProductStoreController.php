<?php

namespace App\Http\Controllers\Inertia\ProductManagement;

use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Actions\ProductManagement\DestroyProductStoreAction;
use App\Actions\ProductManagement\StoreProductStoreAction;
use App\Actions\ProductManagement\UpdateProductStoreAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\StoreProductStoreRequest;
use App\Http\Requests\ProductManagement\UpdateProductStoreRequest;
use App\Models\ProductStore;
use App\Queries\ProductStoreQuery;
use App\Support\ProductStorePresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductStoreController extends Controller
{
    public function index(ProductStoreQuery $query): Response
    {
        $this->authorize('viewAny', ProductStore::class);

        $stores = $query->paginate();

        return Inertia::render('ProductStores/Index', [
            'stores' => $stores->through(fn (ProductStore $store): array => ProductStorePresenter::store($store)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ProductStore::class);

        return Inertia::render('ProductStores/Create');
    }

    public function store(StoreProductStoreRequest $request, StoreProductStoreAction $action): RedirectResponse
    {
        $this->authorize('create', ProductStore::class);

        $action->execute(
            $request->validated(),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.product-stores.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(ProductStore $productStore): Response
    {
        $this->authorize('view', $productStore);

        return Inertia::render('ProductStores/Show', [
            'store' => ProductStorePresenter::store($productStore),
        ]);
    }

    public function edit(ProductStore $productStore): Response
    {
        $this->authorize('update', $productStore);

        return Inertia::render('ProductStores/Edit', [
            'store' => ProductStorePresenter::store($productStore),
        ]);
    }

    public function update(
        UpdateProductStoreRequest $request,
        ProductStore $productStore,
        UpdateProductStoreAction $action,
    ): RedirectResponse {
        $this->authorize('update', $productStore);

        $action->execute(
            $productStore,
            $request->validated(),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.product-stores.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(ProductStore $productStore, DestroyProductStoreAction $action): RedirectResponse
    {
        $this->authorize('delete', $productStore);
        $action->execute($productStore);

        return redirect()
            ->route('inertia.product-stores.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    public function toggleStatus(ProductStore $productStore, UpdateActiveStatusAction $action): RedirectResponse
    {
        $this->authorize('update', $productStore);
        $action->execute($productStore->id, 'product_stores', (int) Auth::id());

        return redirect()
            ->route('inertia.product-stores.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
