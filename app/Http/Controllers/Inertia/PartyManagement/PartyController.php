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

class PartyController extends Controller
{
    public function index(PartyQuery $query): Response
    {
        $this->authorize('viewAny', Party::class);

        $parties = $query->paginate();

        return Inertia::render('Parties/Index', [
            'parties' => $parties->through(fn (Party $party): array => PartyPresenter::listItem($party)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Party::class);

        return Inertia::render('Parties/Create', PartyLookups::formOptions());
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
            ->route('inertia.parties.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(Party $party): Response
    {
        $this->authorize('view', $party);

        return Inertia::render('Parties/Show', [
            'party' => PartyPresenter::form($party),
        ]);
    }

    public function edit(Party $party): Response
    {
        $this->authorize('update', $party);

        return Inertia::render('Parties/Edit', array_merge(PartyLookups::formOptions(), [
            'party' => PartyPresenter::form($party),
        ]));
    }

    public function update(UpdatePartyRequest $request, Party $party, UpdatePartyAction $action): RedirectResponse
    {
        $this->authorize('update', $party);

        $action->execute(
            $party,
            $request->validated(),
            $this->partyFiles($request),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.parties.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(Party $party, DestroyPartyAction $action): RedirectResponse
    {
        $this->authorize('delete', $party);
        $action->execute($party);

        return redirect()
            ->route('inertia.parties.index')
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
