<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAction;
use App\Actions\PartyManagement\StorePartyAction;
use App\Actions\PartyManagement\UpdatePartyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyRequest;
use App\Http\Requests\PartyManagement\UpdatePartyRequest;
use App\Models\BusinessType;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Party;
use App\Models\VendorType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PartyController extends Controller
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
        $parties = Party::orderBy('id','desc')->get();

        return view('partymanagement.party.index', compact('parties'));
    }

    public function create()
    {
        $party = new Party();
        $countries = Country::with('provinces:id,name,country_id',
            'provinces.cities:id,name,province_id')->get(['id', 'name']);

        $divisions = Division::get();
        $customer_types = CustomerType::get();
        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        $contact_persons = ConductPerson::get();

        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();

        return view('partymanagement.party.create', compact('countries', 'party', 'divisions', 'customer_types', 'farm_types', 'farm_subtypes', 'business_types', 'vendor_types', 'contact_persons'));
    }

    public function edit($id)
    {
        $party = Party::with('country:id,name', 'province:id,name', 'city:id,name', 'farm:id,party_id,farm_name,farm_noc,farm_address', 'company:id,party_id,company_name')->findOrFail($id);

        $countries = Country::with('provinces:id,name,country_id',
            'provinces.cities:id,name,province_id')->get(['id', 'name']);

        $divisions = Division::get();
        $customer_types = CustomerType::get();
        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        $contact_persons = ConductPerson::get();

        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();

        return view('partymanagement.party.create', compact('countries', 'party', 'divisions', 'customer_types', 'farm_types', 'farm_subtypes', 'business_types', 'vendor_types', 'contact_persons'));
    }

    public function customersWithDivision($division_id)
    {
        $customers = Party::where([['customer_division_id', $division_id], ['is_customer', 1]])->get(['id', 'is_customer', 'name', 'cnic_no', 'customer_division_id']);
        if ($customers->count() > 0) {
            return response()->json([
                'success' => 'yes',
                'data' => $customers->toArray(),
            ]);
        }

        return response()->json([
            'success' => 'no',
            'data' => [],
        ]);
    }

    public function store(StorePartyRequest $request, StorePartyAction $action)
    {
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $this->partyFiles($request),
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

        return $this->redirectAfterPartyMutation($request);
    }

    public function update(UpdatePartyRequest $request, $id, UpdatePartyAction $action)
    {
        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $party = Party::findOrFail($id);
            $action->execute(
                $party,
                $request->validated(),
                $this->partyFiles($request),
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

        return $this->redirectAfterPartyMutation($request);
    }

    public function destroy($id, DestroyPartyAction $action)
    {
        $party = Party::findOrFail($id);
        $action->execute($party);

        return redirect()->route('parties.index');
    }

    private function partyFiles($request): array
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

    private function redirectAfterPartyMutation($request)
    {
        if ($request->has('from_vendor')) {
            return redirect()->route('vendors.index');
        }

        if ($request->has('from_customer')) {
            return redirect()->route('customers.index');
        }

        return redirect()->route('parties.index');
    }
}
