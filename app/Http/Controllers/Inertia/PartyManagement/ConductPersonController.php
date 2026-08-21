<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyConductPersonAction;
use App\Actions\PartyManagement\StoreConductPersonAction;
use App\Actions\PartyManagement\UpdateConductPersonAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreConductPersonRequest;
use App\Http\Requests\PartyManagement\UpdateConductPersonRequest;
use App\Models\ConductPerson;
use App\Models\Party;
use App\Queries\ConductPersonQuery;
use App\Support\PartyLookups;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConductPersonController extends Controller
{
    public function index(ConductPersonQuery $query): Response
    {
        $this->authorize('viewAny', Party::class);

        $people = $query->paginate();

        return Inertia::render('ContactPersons/Index', [
            'people' => $people->through(fn (ConductPerson $person): array => [
                'id' => $person->id,
                'name' => $person->name,
                'guardian_name' => $person->guardian_name,
                'cnic_no' => $person->cnic_no,
                'contact_number' => $person->contact_number,
                'province' => $person->province?->name,
                'city' => $person->city?->name,
            ]),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Party::class);

        return Inertia::render('ContactPersons/Create', [
            'countries' => PartyLookups::formOptions()['countries'],
        ]);
    }

    public function store(StoreConductPersonRequest $request, StoreConductPersonAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.conduct-persons.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ]);
    }

    public function show(ConductPerson $conductperson): Response
    {
        $this->authorize('viewAny', Party::class);
        $conductperson->load('country:id,name', 'province:id,name', 'city:id,name');

        return Inertia::render('ContactPersons/Show', [
            'person' => $this->formPayload($conductperson),
        ]);
    }

    public function edit(ConductPerson $conductperson): Response
    {
        $this->authorize('update', new Party);

        return Inertia::render('ContactPersons/Edit', [
            'person' => $this->formPayload($conductperson),
            'countries' => PartyLookups::formOptions()['countries'],
        ]);
    }

    public function update(
        UpdateConductPersonRequest $request,
        ConductPerson $conductperson,
        UpdateConductPersonAction $action,
    ): RedirectResponse {
        $this->authorize('update', new Party);

        $action->execute(
            $conductperson,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.conduct-persons.index')
            ->with('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data Updated successfully!',
            ]);
    }

    public function destroy(ConductPerson $conductperson, DestroyConductPersonAction $action): RedirectResponse
    {
        $this->authorize('delete', new Party);
        $action->execute($conductperson);

        return redirect()
            ->route('inertia.conduct-persons.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(ConductPerson $person): array
    {
        return [
            'id' => $person->id,
            'name' => $person->name,
            'guardian_name' => $person->guardian_name,
            'cnic_no' => $person->cnic_no,
            'email' => $person->email,
            'contact_number' => $person->contact_number,
            'country_id' => $person->country_id,
            'province_id' => $person->province_id,
            'city_id' => $person->city_id,
            'address' => $person->address,
            'picture' => $person->picture,
            'country' => $person->country?->name,
            'province' => $person->province?->name,
            'city' => $person->city?->name,
        ];
    }
}
