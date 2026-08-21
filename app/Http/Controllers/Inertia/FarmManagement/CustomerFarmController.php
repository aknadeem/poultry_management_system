<?php

namespace App\Http\Controllers\Inertia\FarmManagement;

use App\Actions\FarmManagement\DestroyCustomerFarmAction;
use App\Actions\FarmManagement\UpdateCustomerFarmAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\UpdateCustomerFarmRequest;
use App\Models\PartyFarm;
use App\Queries\CustomerFarmQuery;
use App\Support\FarmLookups;
use App\Support\FarmPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CustomerFarmController extends Controller
{
    public function index(CustomerFarmQuery $query): Response
    {
        $farms = $query->paginate();

        return Inertia::render('CustomerFarms/Index', array_merge(FarmLookups::formOptions(), [
            'farms' => $farms->through(fn (PartyFarm $farm): array => FarmPresenter::customerFarm($farm)),
            'filters' => $query->filters(),
        ]));
    }

    public function update(
        UpdateCustomerFarmRequest $request,
        PartyFarm $customerFarm,
        UpdateCustomerFarmAction $action,
    ): RedirectResponse {
        $this->customerFarmOrFail($customerFarm);

        $action->execute(
            $customerFarm,
            $request->validated(),
            $request->file('farm_image'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.customer-farms.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(PartyFarm $customerFarm, DestroyCustomerFarmAction $action): RedirectResponse
    {
        $this->customerFarmOrFail($customerFarm);

        try {
            $action->execute($customerFarm);

            return redirect()
                ->route('inertia.customer-farms.index')
                ->with('swal_notification', [
                    'title' => 'Deleted',
                    'icon_type' => 'success',
                    'message' => 'Data Deleted Successfully!',
                ]);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('inertia.customer-farms.index')
                ->with('swal_notification', [
                    'title' => 'Error',
                    'icon_type' => 'warning',
                    'message' => collect($exception->errors())->flatten()->first() ?? 'Unable to delete this farm.',
                ]);
        }
    }

    private function customerFarmOrFail(PartyFarm $farm): void
    {
        $farm->loadMissing('party:id,is_customer');

        abort_unless((int) $farm->party?->is_customer === 1, 404);
    }
}
