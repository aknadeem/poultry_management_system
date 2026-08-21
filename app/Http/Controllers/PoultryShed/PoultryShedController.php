<?php

namespace App\Http\Controllers\PoultryShed;

use App\Actions\FarmManagement\DestroyPersonalFarmAction;
use App\Actions\FarmManagement\StorePersonalFarmAction;
use App\Actions\FarmManagement\UpdatePersonalFarmAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\StorePersonalFarmRequest;
use App\Http\Requests\FarmManagement\UpdatePersonalFarmRequest;
use App\Models\Country;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\PersonalFarm;
use App\Services\FileUploadService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PoultryShedController extends Controller
{
    public function index(): View
    {
        $farms = PersonalFarm::with('type:id,name', 'subtype:id,name')->get();

        return view('farmmanagement.personalfarms.index', compact('farms'));
    }

    public function create(): View
    {
        $farm = new PersonalFarm();
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id',
        )->get(['id', 'name']);

        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();

        return view('farmmanagement.personalfarms.create', compact('farm', 'countries', 'farm_types', 'farm_subtypes'));
    }

    public function store(StorePersonalFarmRequest $request, StorePersonalFarmAction $action): RedirectResponse
    {
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $request->file('farm_image'),
                $request->user()->id,
            );
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('personalfarms.index');
    }

    public function show(int $id): void
    {
    }

    public function edit(int $id): View
    {
        $farm = PersonalFarm::with('country:id,name', 'province:id,name', 'city:id,name')->findOrFail($id);
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id',
        )->get(['id', 'name']);

        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();

        return view('farmmanagement.personalfarms.create', compact('farm', 'countries', 'farm_types', 'farm_subtypes'));
    }

    public function update(
        UpdatePersonalFarmRequest $request,
        int $id,
        UpdatePersonalFarmAction $action,
    ): RedirectResponse {
        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $farm = PersonalFarm::findOrFail($id);
            $action->execute(
                $farm,
                $request->validated(),
                $request->file('farm_image'),
                $request->user()->id,
            );
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('personalfarms.index');
    }

    public function destroy(int $id, DestroyPersonalFarmAction $action): RedirectResponse
    {
        $farm = PersonalFarm::findOrFail($id);
        $action->execute($farm);
        $message = 'Data deleted successfully';
        $title = 'Deleted!';
        $icon_type = 'success';
        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('personalfarms.index');
    }

    public function forceDelete(int $id, FileUploadService $uploadService): RedirectResponse
    {
        $farm = PersonalFarm::findOrFail($id);
        $uploadService->delete('personalfarms', $farm->farm_image);
        $farm->forceDelete();
        $message = 'Data deleted successfully';
        $title = 'Deleted!';
        $icon_type = 'success';
        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('personalfarms.index');
    }
}
