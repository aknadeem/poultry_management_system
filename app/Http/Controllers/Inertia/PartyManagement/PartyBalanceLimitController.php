<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyBalanceLimitAction;
use App\Actions\PartyManagement\StorePartyBalanceLimitAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyBalanceLimitRequest;
use App\Models\Party;
use App\Models\PartyBalanceLimit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PartyBalanceLimitController extends Controller
{
    public function store(StorePartyBalanceLimitRequest $request, StorePartyBalanceLimitAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute($request->validated(), (int) Auth::id());

        return back()->with('swal_notification', [
            'title' => 'Saved',
            'icon_type' => 'success',
            'message' => 'Data created successfully!',
        ]);
    }

    public function destroy(PartyBalanceLimit $balancelimit, DestroyPartyBalanceLimitAction $action): RedirectResponse
    {
        $this->authorize('delete', new Party);
        $action->execute($balancelimit);

        return back()->with('swal_notification', [
            'title' => 'Deleted',
            'icon_type' => 'success',
            'message' => 'Data Deleted Successfully!',
        ]);
    }
}
