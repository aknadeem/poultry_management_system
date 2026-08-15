<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAction;
use App\Actions\PartyManagement\StoreLookupTypeAction;
use App\Actions\PartyManagement\StorePartyQuickAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StoreLookupTypeRequest;
use App\Http\Requests\PartyManagement\StorePartyQuickRequest;
use App\Models\BusinessType;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\Division;
use App\Models\Party;
use App\Models\VendorType;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
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
        $customers = Party::where('is_vendor', 1)
            ->with('farm:id,party_id,farm_name,farm_type_id', 'company:id,party_id,company_name')
            ->get([
                'id', 'is_customer', 'is_vendor', 'name', 'guardian_name', 'cnic_no', 'contact_no',
                'customer_type_id', 'customer_division_id', 'profile_picture',
            ]);

        return view('partymanagement.vendors.index', compact('customers'));
    }

    public function create()
    {
        $party = new Party();
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);
        $divisions = Division::get();
        $contact_persons = ConductPerson::get();
        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();

        return view('partymanagement.vendors.create', compact(
            'countries',
            'party',
            'business_types',
            'divisions',
            'vendor_types',
            'contact_persons'
        ));
    }

    public function store(StorePartyQuickRequest $request, StorePartyQuickAction $action)
    {
        try {
            $partyId = (int) $request->input('customer_id_modal', 0);
            $action->execute(
                $request->validated(),
                $request->file('image_file'),
                $this->authUserId,
                ['is_customer' => 0, 'is_vendor' => 1]
            );

            return response()->json([
                'message' => $partyId > 0
                    ? 'A customer Updated successfully!'
                    : 'New customer created successfully!',
                'success' => 'yes',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 201);
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

    public function show($id)
    {
        $customer = Party::vendor(1)->with('farm', 'company')->find($id);
        if ($customer) {
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();

            return response()->json([
                'message' => 'Cutomer Detail Data',
                'success' => 'yes',
                'html_data' => $html_data,
            ], 201);
        }

        return response()->json([
            'message' => 'No customer found against this id',
            'success' => 'no',
            'html_data' => '',
        ], 201);
    }

    public function edit($id)
    {
        $party = Party::with('farm:id,party_id,farm_name', 'company:id,party_id,company_name')->find($id);
        if ($party) {
            return response()->json([
                'message' => 'yes',
                'customer' => [
                    'id' => $party->id,
                    'name' => $party->name,
                    'contact_no' => $party->contact_no,
                    'email' => $party->email,
                    'farm_name' => $party->company?->company_name ?? $party->farm?->farm_name,
                    'address' => $party->address,
                    'image' => $party->profile_picture,
                ],
            ], 201);
        }
    }

    public function storeAllType(StoreLookupTypeRequest $request, StoreLookupTypeAction $action)
    {
        try {
            $result = $action->execute($request->validated());

            return response()->json([
                'message' => 'Data created successfully!',
                'success' => 'yes',
                'data' => $result,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 201);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }

            return response()->json([
                'message' => 'Something went wrong',
                'success' => 'no',
                'data' => [],
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyAction $action)
    {
        $party = Party::vendor(1)->findOrFail($id);
        $action->execute($party);

        return redirect()->route('vendors.index');
    }
}
