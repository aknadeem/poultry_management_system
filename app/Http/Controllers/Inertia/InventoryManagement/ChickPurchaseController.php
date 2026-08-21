<?php

namespace App\Http\Controllers\Inertia\InventoryManagement;

use App\Actions\ChickenModule\DestroyChickPurchaseAction;
use App\Actions\ChickenModule\StoreChickPurchaseAction;
use App\Actions\ChickenModule\UpdateChickPurchaseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChickenModule\StoreChickPurchaseRequest;
use App\Http\Requests\ChickenModule\UpdateChickPurchaseRequest;
use App\Models\ChickPurchase;
use App\Queries\ChickPurchaseQuery;
use App\Support\InventoryLookups;
use App\Support\InventoryPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChickPurchaseController extends Controller
{
    public function index(ChickPurchaseQuery $query): Response
    {
        $this->authorize('viewAny', ChickPurchase::class);

        $purchases = $query->paginate();

        return Inertia::render('ChickPurchases/Index', [
            'purchases' => $purchases->through(
                fn (ChickPurchase $purchase): array => InventoryPresenter::chickPurchase($purchase)
            ),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ChickPurchase::class);

        return Inertia::render('ChickPurchases/Create', InventoryLookups::purchaseOptions());
    }

    public function store(StoreChickPurchaseRequest $request, StoreChickPurchaseAction $action): RedirectResponse
    {
        $this->authorize('create', ChickPurchase::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.chick-purchases.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ]);
    }

    public function show(ChickPurchase $chickPurchase): Response
    {
        $this->authorize('view', $chickPurchase);

        return Inertia::render('ChickPurchases/Show', [
            'purchase' => InventoryPresenter::chickPurchase($chickPurchase),
        ]);
    }

    public function edit(ChickPurchase $chickPurchase): Response
    {
        $this->authorize('update', $chickPurchase);

        return Inertia::render('ChickPurchases/Edit', array_merge(InventoryLookups::purchaseOptions(), [
            'purchase' => InventoryPresenter::chickPurchase($chickPurchase),
        ]));
    }

    public function update(
        UpdateChickPurchaseRequest $request,
        ChickPurchase $chickPurchase,
        UpdateChickPurchaseAction $action,
    ): RedirectResponse {
        $this->authorize('update', $chickPurchase);

        $action->execute(
            $chickPurchase,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.chick-purchases.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully!',
            ]);
    }

    public function destroy(ChickPurchase $chickPurchase, DestroyChickPurchaseAction $action): RedirectResponse
    {
        $this->authorize('delete', $chickPurchase);
        $action->execute($chickPurchase);

        return redirect()
            ->route('inertia.chick-purchases.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
