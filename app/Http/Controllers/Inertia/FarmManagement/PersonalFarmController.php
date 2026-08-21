<?php

namespace App\Http\Controllers\Inertia\FarmManagement;

use App\Actions\FarmManagement\DestroyPersonalFarmAction;
use App\Actions\FarmManagement\StorePersonalFarmAction;
use App\Actions\FarmManagement\UpdatePersonalFarmAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\StorePersonalFarmRequest;
use App\Http\Requests\FarmManagement\UpdatePersonalFarmRequest;
use App\Models\PersonalFarm;
use App\Queries\PersonalFarmQuery;
use App\Support\FarmLookups;
use App\Support\FarmPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonalFarmController extends Controller
{
    public function index(PersonalFarmQuery $query): Response
    {
        $farms = $query->paginate();

        return Inertia::render('PersonalFarms/Index', [
            'farms' => $farms->through(fn (PersonalFarm $farm): array => FarmPresenter::personalFarm($farm)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PersonalFarms/Create', FarmLookups::formOptions());
    }

    public function store(StorePersonalFarmRequest $request, StorePersonalFarmAction $action): RedirectResponse
    {
        $action->execute(
            $request->validated(),
            $request->file('farm_image'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.personal-farms.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function edit(PersonalFarm $personalFarm): Response
    {
        return Inertia::render('PersonalFarms/Edit', array_merge(FarmLookups::formOptions(), [
            'farm' => FarmPresenter::personalFarm($personalFarm),
        ]));
    }

    public function update(
        UpdatePersonalFarmRequest $request,
        PersonalFarm $personalFarm,
        UpdatePersonalFarmAction $action,
    ): RedirectResponse {
        $action->execute(
            $personalFarm,
            $request->validated(),
            $request->file('farm_image'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.personal-farms.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(PersonalFarm $personalFarm, DestroyPersonalFarmAction $action): RedirectResponse
    {
        $action->execute($personalFarm);

        return redirect()
            ->route('inertia.personal-farms.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
