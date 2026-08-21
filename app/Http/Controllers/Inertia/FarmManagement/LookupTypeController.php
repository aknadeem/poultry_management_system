<?php

namespace App\Http\Controllers\Inertia\FarmManagement;

use App\Actions\PartyManagement\StoreLookupTypeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreLookupTypeRequest;
use Illuminate\Http\RedirectResponse;

class LookupTypeController extends Controller
{
    public function store(StoreLookupTypeRequest $request, StoreLookupTypeAction $action): RedirectResponse
    {
        $data = $request->validated();
        $result = $action->execute($data);

        return back()->with([
            'swal_notification' => [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ],
            'created_lookup' => [
                'table' => $data['tag_name'],
                'id' => $result['id'],
                'name' => $result['name'],
            ],
        ]);
    }
}
