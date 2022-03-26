<?php

namespace App\Http\Controllers\PartyManagement;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Party;
use App\Models\Country;
use App\Models\Division;
use App\Models\FarmType;
use App\Models\PartyFarm;
use App\Models\VendorType;
use App\Models\FarmSubtype;
use App\Models\BusinessType;
use App\Models\CustomerType;
use App\Models\PartyBalance;
use App\Models\PartyCompany;
use Illuminate\Http\Request;
use App\Models\ConductPerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PartyController extends Controller
{
    private $auth_user_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        $parties = Party::get();
        return view('partymanagement.party.index', compact('parties'));
    }

    public function create()
    {   
        $party = new Party();
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        $divisions = Division::get();
        $customer_types = CustomerType::get();
        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        $contact_persons = ConductPerson::get();

        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();
        return view('partymanagement.party.create', compact('countries', 'party', 'divisions', 'customer_types', 'farm_types', 'farm_subtypes', 'business_types', 'vendor_types','contact_persons'));
    }

    public function edit($id)
    {   
        $party = Party::with('country:id,name','province:id,name','city:id,name', 'farm:id,party_id,farm_name', 'company:id,party_id,company_name')->findOrFail($id);

        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        $divisions = Division::get();
        $customer_types = CustomerType::get();
        $farm_types = FarmType::get();
        $farm_subtypes = FarmSubtype::get();
        $contact_persons = ConductPerson::get();

        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();
        return view('partymanagement.party.create', compact('countries', 'party', 'divisions', 'customer_types', 'farm_types', 'farm_subtypes', 'business_types', 'vendor_types','contact_persons'));
    }

    public function customersWithDivision($division_id)
    {
        $customers = Party::where([['customer_division_id', $division_id], ['is_customer', 1]])->get(['id','is_customer','name','cnic_no','customer_division_id']);
        if($customers->count() > 0){
            return response()->json([
                'success' => 'yes',
                'data' => $customers->toArray(),
            ]);
        }else{
            return response()->json([
                'success' => 'no',
                'data' => [],
            ]);
        }
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {
            DB::transaction(function () use ($request) {

                $party = Party::create([
                    'is_vendor' => $request->is_vendor,
                    'is_customer' => $request->is_customer,
                    'name' => $request->name,
                    'guardian_name' => $request->guardian_name,
                    'cnic_no' => $request->cnic_no,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'business_no' => $request->business_no,
                    'manual_number' => $request->manual_number,
                    'address' => $request->address,
                    'customer_type_id' => $request->customer_type_id,
                    'vendor_type_id' => $request->vendor_type_id,
                    'customer_division_id' => $request->customer_division_id,
                    'vendor_division_id' => $request->vendor_division_id,
                    'address' => $request->address,
                    'description' => $request->description,
                    'country_id' => $request->country_id,
                    'province_id' => $request->province_id,
                    'city_id' => $request->city_id,
                    'contact_person_id' => $request->contact_person_id,
                    'balance' => $request->opening_balance,
                    'balance_type' => $request->balance_type,
                    'addedby' => $this->auth_user_id,
                ]);

                if($party){
                    $images = $this->uploadPartyImages($request);

                    $update_party = DB::table('parties')
                    ->where('id', $party->id)
                    ->update([
                        'profile_picture' => $images['profile_picture'],
                        'cnic_front' => $images['cnic_front'],
                        'cnic_back' => $images['cnic_back'],
                        'signature' => $images['signature_image'],
                        'addedby' => $this->auth_user_id,
                    ]);
                    if($request->is_customer){
                        if ($request->hasFile('farm_image')) {
                            $farm_image_file = $request->file('farm_image');
                            $extension = $request->file('farm_image')->extension();
                            $farm_image = time().mt_rand(10,99).'.'.$extension;
                            
                        }else{
                            $farm_image = null;
                        }

                        $party_farm = PartyFarm::create([
                            'party_id' =>  $party->id,
                            'farm_type_id' => $request->farm_type_id,
                            'farm_subtype_id' => $request->farm_subtype_id,
                            'farm_name' => $request->farm_name,
                            'farm_noc' => $request->farm_noc,
                            'farm_image' => $farm_image,
                            'farm_address' => $request->farm_address,
                            'addedby' => $this->auth_user_id,
                        ]);

                        if($party_farm){
                            $upload_to_folder = $farm_image_file->storeAs('party/farm/', $farm_image, 'public');
                        }
                    }
                    if($request->is_vendor){
                        if ($request->hasFile('company_logo')) {
                            $company_logo_file = $request->file('company_logo');
                            $extension = $request->file('company_logo')->extension();
                            $company_logo = time().mt_rand(10,99).'.'.$extension;
                            // $upload_to_folder = $company_logo_file->storeAs('party/company/', $company_logo, 'public');
                        }else{
                            $company_logo = null;
                        }

                        $party_farm = PartyCompany::create([
                            'party_id' =>  $party->id,
                            'company_name' => $request->company_name,
                            'business_type_id' => $request->business_type_id,
                            'company_logo' => $company_logo,
                            'company_address' => $request->company_address,
                            'addedby' => $this->auth_user_id,
                        ]);

                        if($party_farm){
                            $upload_to_folder = $company_logo_file->storeAs('party/company/', $company_logo, 'public');
                        }
                    }

                    if($request->has('opening_balance') && $request->opening_balance > 0){
                        $partyBalance = PartyBalance::create([
                            'party_id' => $party->id,
                            'total_amount' => $request->opening_balance,
                            'remaining_amount' =>  $request->opening_balance,
                            'transaction_date' => today()->format('Y-m-d'),
                            'amount_type' => $request->balance_type,
                            'narration' => 'Opening Balance',
                            'addedby' => $this->auth_user_id,
                        ]);
                    }
                }
            });
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        if($request->has('from_vendor')){
            return redirect()->route('vendors.index');
        }elseif($request->has('from_customer')){
            return redirect()->route('customers.index');
        }else{
            return redirect()->route('parties.index');
        }
    }


    public function uploadPartyImages($request)
    {
        if ($request->hasFile('profile_picture')) {
            $image_file = $request->file('profile_picture');
            $extension = $request->file('profile_picture')->extension();
            $profile_picture = time().mt_rand(10,99).'.'.$extension;
            $upload_to_folder = $image_file->storeAs('party/', $profile_picture, 'public');
        }else{
            $profile_picture = null;
        }

        if ($request->hasFile('cnic_front')) {
            $cnic_front_file = $request->file('cnic_front');
            $extension = $request->file('cnic_front')->extension();
            $cnic_front = time().mt_rand(10,99).'.'.$extension;
            $upload_to_folder = $cnic_front_file->storeAs('party/', $cnic_front, 'public');
        }else{
            $cnic_front = null;
        }
        
        if ($request->hasFile('cnic_back')) {
            $cnic_back_file = $request->file('cnic_back');
            $extension = $request->file('cnic_back')->extension();
            $cnic_back = time().mt_rand(10,99).'.'.$extension;
            $upload_to_folder = $cnic_back_file->storeAs('party/', $cnic_back, 'public');
        }else{
            $cnic_back = null;
        }

        if ($request->hasFile('signature_image')) {
            $signature_image_file = $request->file('signature_image');
            $extension = $request->file('signature_image')->extension();
            $signature_image = time().mt_rand(10,99).'.'.$extension;
            // $upload_to_folder = $signature_image_file->storeAs('party/', $signature_image, 'public');
        }else{
            $signature_image = null;
        }
        $data = [
            'profile_picture' => $profile_picture,
            'cnic_front' => $cnic_front,
            'cnic_back' => $cnic_back,
            'signature_image' => $signature_image,
        ];
        return $data;
    }

    public function validationRules($request, $id)
    {
        // $validator = Validator::make($request->all(),[
        $this->validate($request, [
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'cnic_no' => 'bail|required|string|min:13|max:13|unique:parties,cnic_no,'.$id,
            'email' => 'bail|nullable|string',
            'contact_no' => 'bail|required|string|min:11|max:11',
            'business_number' => 'bail|nullable|string|min:11|max:11',
            'manual_number' => 'bail|required|string',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'address' => 'bail|nullable|string',
            'profile_picture' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'cnic_front' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'cnic_back' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'signature' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',

            'customer_type_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_type_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_subtype_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_name' => 'bail|required_if:is_customer,==,1|string',
            'farm_noc' => 'bail|required_if:is_customer,==,1|string',
            'farm_image' => 'bail|required_if:is_customer,==,1|mimes:jpeg,jpg,png|max:5000',
            'farm_address' => 'bail|required_if:is_customer,==,1|string',

            'vendor_division_id' => 'bail|required_if:is_vendor,==,1|integer',
            'vendor_type_id' => 'bail|required_if:is_vendor,==,1|integer',
            'company_name' => 'bail|required_if:is_vendor,==,1|string',
            'business_type_id' => 'bail|required_if:is_vendor,==,1|integer',
            'company_logo' => 'bail|required_if:is_vendor,==,1|mimes:jpeg,jpg,png|max:5000',
            'company_address' => 'bail|required_if:is_vendor,==,1|string',
        ],[
            'cnic_no.min'=> 'The CNIC Number must be at least 13 Digits',
            'cnic_no.max'=> 'The CNIC Number must not be greater than 13 Digits',
            'contact_no.min'=> 'The Contact number must be at least 11 Digits',
            'contact_no.max'=> 'The Contact number must not be greater than 11 Digits',
            'farm_image.required_if'=> 'The farm image field is required',
            'company_logo.required_if'=> 'The company logo field is required',
        ]);

        // if($validator->fails()){
        //     return response()->json([
        //         'error' => $validator->errors()->toArray(),
        //         'success' => 'no',
        //     ], 201);
        // }
    }
}
