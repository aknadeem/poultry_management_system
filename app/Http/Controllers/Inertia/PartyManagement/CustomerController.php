<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAction;
use App\Actions\PartyManagement\StorePartyAction;
use App\Actions\PartyManagement\UpdatePartyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyRequest;
use App\Http\Requests\PartyManagement\UpdatePartyRequest;
use App\Models\Party;
use App\Queries\PartyQuery;
use App\Support\PartyLookups;
use App\Support\PartyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(PartyQuery $query): Response
    {
        $this->authorize('viewAny', Party::class);

        $customers = $query->paginate('customer');

        return Inertia::render('Customers/Index', [
            'customers' => $customers->through(fn (Party $party): array => PartyPresenter::listItem($party)),
            'filters' => $query->filters('customer'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Party::class);

        return Inertia::render('Customers/Create', PartyLookups::formOptions());
    }

    public function store(StorePartyRequest $request, StorePartyAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute(
            $request->validated(),
            $this->partyFiles($request),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.customers.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'New customer created successfully!',
            ]);
    }

    public function show(Party $customer): Response
    {
        abort_unless((int) $customer->is_customer === 1, 404);
        $this->authorize('view', $customer);

        return Inertia::render('Customers/Show', [
            'party' => PartyPresenter::form($customer),
        ]);
    }

    public function edit(Party $customer): Response
    {
        abort_unless((int) $customer->is_customer === 1, 404);
        $this->authorize('update', $customer);

        return Inertia::render('Customers/Edit', array_merge(PartyLookups::formOptions(), [
            'party' => PartyPresenter::form($customer),
        ]));
    }

    public function update(UpdatePartyRequest $request, Party $customer, UpdatePartyAction $action): RedirectResponse
    {
        abort_unless((int) $customer->is_customer === 1, 404);
        $this->authorize('update', $customer);

        $action->execute(
            $customer,
            $request->validated(),
            $this->partyFiles($request),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.customers.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(Party $customer, DestroyPartyAction $action): RedirectResponse
    {
        abort_unless((int) $customer->is_customer === 1, 404);
        $this->authorize('delete', $customer);
        $action->execute($customer);

        return redirect()
            ->route('inertia.customers.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function partyFiles(Request $request): array
    {
        return [
            'profile_picture' => $request->file('profile_picture'),
            'cnic_front' => $request->file('cnic_front'),
            'cnic_back' => $request->file('cnic_back'),
            'signature_image' => $request->file('signature_image'),
            'farm_image' => $request->file('farm_image'),
            'company_logo' => $request->file('company_logo'),
        ];
    }
}
