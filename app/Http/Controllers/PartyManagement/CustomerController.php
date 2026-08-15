<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAction;
use App\Actions\PartyManagement\StorePartyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyRequest;
use App\Models\ConductPerson;
use App\Models\Country;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\FarmSubtype;
use App\Models\FarmType;
use App\Models\Party;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
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
        $customers = Party::where('is_customer', 1)
            ->orderBy('id','desc')
            ->with('farm:id,party_id,farm_name,farm_type_id')
            ->get([
                'id', 'is_customer', 'name', 'guardian_name', 'cnic_no', 'contact_no',
                'customer_type_id', 'customer_division_id', 'profile_picture',
            ]);

        return view('partymanagement.customers.index', compact('customers'));
    }

    public function create()
    {
        $party = new Party();
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);
        $divisions = Division::get();
        $customer_types = CustomerType::get();
        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        $contact_persons = ConductPerson::get();

        return view('partymanagement.customers.create', compact(
            'countries',
            'party',
            'divisions',
            'customer_types',
            'farm_types',
            'farm_subtypes',
            'contact_persons'
        ));
    }

    public function store(StorePartyRequest $request, StorePartyAction $action)
    {
        try {
            $action->execute(
                $request->validated(),
                $this->partyFiles($request),
                $this->authUserId
            );

            return response()->json([
                'message' => 'New customer created successfully!',
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
        $customer = Party::customer(1)->with('farm')->find($id);
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
        $party = Party::with('farm:id,party_id,farm_name')->find($id);
        if ($party) {
            return response()->json([
                'message' => 'yes',
                'customer' => [
                    'id' => $party->id,
                    'name' => $party->name,
                    'contact_no' => $party->contact_no,
                    'email' => $party->email,
                    'farm_name' => $party->farm?->farm_name,
                    'address' => $party->address,
                    'image' => $party->profile_picture,
                ],
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyAction $action)
    {
        $party = Party::customer(1)->findOrFail($id);
        $action->execute($party);

        return redirect()->route('customers.index');
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
}
