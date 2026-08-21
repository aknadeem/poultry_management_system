<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyDocumentAction;
use App\Actions\PartyManagement\StorePartyDocumentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyDocumentRequest;
use App\Models\Party;
use App\Models\PartyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PartyDocumentController extends Controller
{
    public function store(StorePartyDocumentRequest $request, StorePartyDocumentAction $action): RedirectResponse
    {
        $this->authorize('create', Party::class);

        $action->execute(
            $request->validated(),
            $request->file('document_name'),
            (int) Auth::id(),
        );

        return back()->with('swal_notification', [
            'title' => 'Saved',
            'icon_type' => 'success',
            'message' => 'Data created successfully!',
        ]);
    }

    public function destroy(PartyDocument $partydocument, DestroyPartyDocumentAction $action): RedirectResponse
    {
        $this->authorize('delete', new Party);
        $action->execute($partydocument);

        return back()->with('swal_notification', [
            'title' => 'Deleted',
            'icon_type' => 'success',
            'message' => 'Data Deleted Successfully!',
        ]);
    }
}
