<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyConductPersonAction;
use App\Actions\PartyManagement\StoreConductPersonAction;
use App\Actions\PartyManagement\UpdateConductPersonAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreConductPersonRequest;
use App\Http\Requests\PartyManagement\UpdateConductPersonRequest;
use App\Models\ConductPerson;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use Session;

class ConductPersonController extends Controller
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
        $conduct_people = ConductPerson::with('country:id,name', 'province:id,name', 'city:id,name')->get();

        return view('partymanagement.conductperson.index', compact('conduct_people'));
    }

    public function create()
    {
        $conductperson = new ConductPerson();
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('partymanagement.conductperson.create', compact('conductperson', 'countries'));
    }

    public function edit($id)
    {
        $conductperson = ConductPerson::with('province:id,name', 'city:id,name')->findOrFail($id);
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('partymanagement.conductperson.create', compact('conductperson', 'countries'));
    }

    public function store(StoreConductPersonRequest $request, StoreConductPersonAction $action)
    {
        $message = 'Data created successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $request->file('image_file'),
                $this->authUserId
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

        return redirect()->route('conductpersons.index');
    }

    public function update(UpdateConductPersonRequest $request, $id, UpdateConductPersonAction $action)
    {
        $message = 'Data Updated successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try {
            $person = ConductPerson::findOrFail($id);
            $action->execute(
                $person,
                $request->validated(),
                $request->file('image_file'),
                $this->authUserId
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

        return redirect()->route('conductpersons.index');
    }

    public function destroy($id, DestroyConductPersonAction $action)
    {
        $person = ConductPerson::findOrFail($id);
        $action->execute($person);

        return redirect()->route('conductpersons.index');
    }
}
