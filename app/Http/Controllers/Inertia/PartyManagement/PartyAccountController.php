<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAccountAction;
use App\Actions\PartyManagement\StorePartyAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyAccountRequest;
use App\Models\Party;
use App\Models\PartyAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PartyAccountController extends Controller
{
    public function store(StorePartyAccountRequest $request, StorePartyAccountAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute($request->validated(), (int) Auth::id());

        return back()->with('swal_notification', [
            'title' => 'Saved',
            'icon_type' => 'success',
            'message' => 'Data created successfully!',
        ]);
    }

    public function destroy(PartyAccount $partyaccount, DestroyPartyAccountAction $action): RedirectResponse
    {
        $this->authorize('delete', new Party);
        $action->execute($partyaccount);

        return back()->with('swal_notification', [
            'title' => 'Deleted',
            'icon_type' => 'success',
            'message' => 'Data Deleted Successfully!',
        ]);
    }
}
