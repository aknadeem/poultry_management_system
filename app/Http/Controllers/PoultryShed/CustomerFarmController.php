<?php

namespace App\Http\Controllers\PoultryShed;

use Session;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\PartyFarm;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\FarmManagement\UpdateCustomerFarmAction;
use App\Actions\FarmManagement\DestroyCustomerFarmAction;
use App\Http\Requests\FarmManagement\UpdateCustomerFarmRequest;

class CustomerFarmController extends Controller
{
    private $authUserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index()
    {
        $farms = PartyFarm::query()
            ->whereHas('party', fn ($query) => $query->where('is_customer', 1))
            ->with('party:id,name,cnic_no', 'type:id,name', 'subtype:id,name')
            ->get();

        $farm_types = FarmType::get(['id', 'name']);
        $farm_subtypes = FarmSubtype::get(['id', 'name']);

        return view('farmmanagement.customerfarms.index', compact('farms', 'farm_types', 'farm_subtypes'));
    }

    public function create()
    {
        return view('farmmanagement.customerfarms.create');
    }

    public function store()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $farm = PartyFarm::query()
            ->whereHas('party', fn ($query) => $query->where('is_customer', 1))
            ->with('party:id,name,cnic_no')
            ->find($id);

        if (! $farm) {
            return response()->json(['message' => 'no'], 201);
        }

        return response()->json([
            'message' => 'yes',
            'farm' => $farm->toArray(),
            'party' => [
                'name' => $farm->party?->name,
                'cnic_no' => $farm->party?->cnic_no,
            ],
        ], 200);
    }

    public function update(UpdateCustomerFarmRequest $request, $id, UpdateCustomerFarmAction $action)
    {
        try {
            $farm = PartyFarm::query()
                ->whereHas('party', fn ($query) => $query->where('is_customer', 1))
                ->findOrFail($id);

            $farm = $action->execute(
                $farm,
                $request->validated(),
                $request->file('farm_image'),
                $this->authUserId
            );

            return response()->json([
                'message' => 'Data updated successfully',
                'success' => 'yes',
                'data' => $farm->toArray(),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 200);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }

            return response()->json([
                'message' => 'Something went wrong',
                'success' => 'no',
            ], 200);
        }
    }

    public function destroy($id, DestroyCustomerFarmAction $action)
    {
        try {
            $farm = PartyFarm::query()
                ->whereHas('party', fn ($query) => $query->where('is_customer', 1))
                ->findOrFail($id);

            $action->execute($farm);

            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => collect($e->errors())->flatten()->first() ?? 'Unable to delete this farm.',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('customerfarms.index');
    }
}
