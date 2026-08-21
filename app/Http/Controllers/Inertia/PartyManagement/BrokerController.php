<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyBrokerAction;
use App\Actions\PartyManagement\StoreBrokerAction;
use App\Actions\PartyManagement\UpdateBrokerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreBrokerRequest;
use App\Http\Requests\PartyManagement\UpdateBrokerRequest;
use App\Models\Broker;
use App\Models\Party;
use App\Queries\BrokerQuery;
use App\Support\PartyLookups;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BrokerController extends Controller
{
    public function index(BrokerQuery $query): Response
    {
        $this->authorize('viewAny', Party::class);

        $brokers = $query->paginate();

        return Inertia::render('Brokers/Index', [
            'brokers' => $brokers->through(fn (Broker $broker): array => [
                'id' => $broker->id,
                'name' => $broker->name,
                'guardian_name' => $broker->guardian_name,
                'cnic_no' => $broker->cnic_no,
                'contact_no' => $broker->contact_no,
                'province' => $broker->province?->name,
                'city' => $broker->city?->name,
            ]),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Party::class);

        return Inertia::render('Brokers/Create', [
            'countries' => PartyLookups::formOptions()['countries'],
        ]);
    }

    public function store(StoreBrokerRequest $request, StoreBrokerAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.brokers.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ]);
    }

    public function show(Broker $broker): Response
    {
        $this->authorize('viewAny', Party::class);
        $broker->load('country:id,name', 'province:id,name', 'city:id,name');

        return Inertia::render('Brokers/Show', [
            'broker' => $this->formPayload($broker),
        ]);
    }

    public function edit(Broker $broker): Response
    {
        $this->authorize('update', new Party);

        return Inertia::render('Brokers/Edit', [
            'broker' => $this->formPayload($broker),
            'countries' => PartyLookups::formOptions()['countries'],
        ]);
    }

    public function update(UpdateBrokerRequest $request, Broker $broker, UpdateBrokerAction $action): RedirectResponse
    {
        $this->authorize('update', new Party);

        $action->execute(
            $broker,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.brokers.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data Updated successfully!',
            ]);
    }

    public function destroy(Broker $broker, DestroyBrokerAction $action): RedirectResponse
    {
        $this->authorize('delete', new Party);
        $action->execute($broker);

        return redirect()
            ->route('inertia.brokers.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(Broker $broker): array
    {
        return [
            'id' => $broker->id,
            'name' => $broker->name,
            'guardian_name' => $broker->guardian_name,
            'cnic_no' => $broker->cnic_no,
            'email' => $broker->email,
            'contact_number' => $broker->contact_no,
            'country_id' => $broker->country_id,
            'province_id' => $broker->province_id,
            'city_id' => $broker->city_id,
            'address' => $broker->address,
            'picture' => $broker->picture,
            'country' => $broker->country?->name,
            'province' => $broker->province?->name,
            'city' => $broker->city?->name,
        ];
    }
}
