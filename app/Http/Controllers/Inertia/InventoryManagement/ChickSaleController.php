<?php

namespace App\Http\Controllers\Inertia\InventoryManagement;

use App\Actions\ChickenModule\DestroyChickenSaleAction;
use App\Actions\ChickenModule\StoreChickenSaleAction;
use App\Actions\ChickenModule\UpdateChickenSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChickenModule\StoreChickenSaleRequest;
use App\Http\Requests\ChickenModule\UpdateChickenSaleRequest;
use App\Models\ChickenSale;
use App\Queries\ChickSaleQuery;
use App\Support\InventoryLookups;
use App\Support\InventoryPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChickSaleController extends Controller
{
    public function index(ChickSaleQuery $query): Response
    {
        $this->authorize('viewAny', ChickenSale::class);

        $sales = $query->paginate();

        return Inertia::render('ChickSales/Index', [
            'sales' => $sales->through(fn (ChickenSale $sale): array => InventoryPresenter::chickSale($sale)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ChickenSale::class);

        return Inertia::render('ChickSales/Create', InventoryLookups::saleOptions());
    }

    public function store(StoreChickenSaleRequest $request, StoreChickenSaleAction $action): RedirectResponse
    {
        $this->authorize('create', ChickenSale::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.chick-sales.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ]);
    }

    public function show(ChickenSale $chickSale): Response
    {
        $this->authorize('view', $chickSale);

        return Inertia::render('ChickSales/Show', [
            'sale' => InventoryPresenter::chickSale($chickSale),
        ]);
    }

    public function edit(ChickenSale $chickSale): Response
    {
        $this->authorize('update', $chickSale);

        return Inertia::render('ChickSales/Edit', array_merge(InventoryLookups::saleOptions(), [
            'sale' => InventoryPresenter::chickSale($chickSale),
        ]));
    }

    public function update(
        UpdateChickenSaleRequest $request,
        ChickenSale $chickSale,
        UpdateChickenSaleAction $action,
    ): RedirectResponse {
        $this->authorize('update', $chickSale);

        $action->execute(
            $chickSale,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.chick-sales.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully!',
            ]);
    }

    public function destroy(ChickenSale $chickSale, DestroyChickenSaleAction $action): RedirectResponse
    {
        $this->authorize('delete', $chickSale);
        $action->execute($chickSale);

        return redirect()
            ->route('inertia.chick-sales.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
