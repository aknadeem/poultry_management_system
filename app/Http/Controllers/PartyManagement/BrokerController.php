<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyBrokerAction;
use App\Actions\PartyManagement\StoreBrokerAction;
use App\Actions\PartyManagement\UpdateBrokerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreBrokerRequest;
use App\Http\Requests\PartyManagement\UpdateBrokerRequest;
use App\Models\Broker;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use Session;

class BrokerController extends Controller
{
    private $auth_user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index()
    {
        $brokers = Broker::with('country:id,name', 'province:id,name', 'city:id,name')->get();

        return view('partymanagement.brokers.index', compact('brokers'));
    }

    public function create()
    {
        $broker = new Broker();
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('partymanagement.brokers.create', compact('broker', 'countries'));
    }

    public function edit($id)
    {
        $broker = Broker::with('province:id,name', 'city:id,name')->findOrFail($id);
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('partymanagement.brokers.create', compact('broker', 'countries'));
    }

    public function store(StoreBrokerRequest $request, StoreBrokerAction $action)
    {
        $message = 'Data created successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $request->file('image_file'),
                $this->auth_user_id
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

        return redirect()->route('brokers.index');
    }

    public function update(UpdateBrokerRequest $request, $id, UpdateBrokerAction $action)
    {
        $message = 'Data Updated successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        try {
            $broker = Broker::findOrFail($id);
            $action->execute(
                $broker,
                $request->validated(),
                $request->file('image_file'),
                $this->auth_user_id
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

        return redirect()->route('brokers.index');
    }

    public function destroy($id, DestroyBrokerAction $action)
    {
        $broker = Broker::findOrFail($id);
        $action->execute($broker);

        return redirect()->route('brokers.index');
    }
}
