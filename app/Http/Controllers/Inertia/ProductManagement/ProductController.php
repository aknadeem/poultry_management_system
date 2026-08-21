<?php

namespace App\Http\Controllers\Inertia\ProductManagement;

use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Actions\ProductManagement\DestroyProductAction;
use App\Actions\ProductManagement\StoreProductAction;
use App\Actions\ProductManagement\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\StoreProductRequest;
use App\Http\Requests\ProductManagement\UpdateProductRequest;
use App\Models\Product;
use App\Queries\ProductQuery;
use App\Support\ProductLookups;
use App\Support\ProductPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(ProductQuery $query): Response
    {
        $this->authorize('viewAny', Product::class);

        $products = $query->paginate();

        return Inertia::render('Products/Index', [
            'products' => $products->through(fn (Product $product): array => ProductPresenter::product($product)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Products/Create', ProductLookups::formOptions());
    }

    public function store(StoreProductRequest $request, StoreProductAction $action): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $action->execute(
            $request->validated(),
            $request->file('product_picture'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.products.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        return Inertia::render('Products/Show', [
            'product' => ProductPresenter::product($product),
        ]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Products/Edit', array_merge(ProductLookups::formOptions(), [
            'product' => ProductPresenter::product($product),
        ]));
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
        UpdateProductAction $action,
    ): RedirectResponse {
        $this->authorize('update', $product);

        $action->execute(
            $product,
            $request->validated(),
            $request->file('product_picture'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.products.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(Product $product, DestroyProductAction $action): RedirectResponse
    {
        $this->authorize('delete', $product);
        $action->execute($product);

        return redirect()
            ->route('inertia.products.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    public function toggleStatus(Product $product, UpdateActiveStatusAction $action): RedirectResponse
    {
        $this->authorize('update', $product);
        $action->execute($product->id, 'products', (int) Auth::id());

        return redirect()
            ->route('inertia.products.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
