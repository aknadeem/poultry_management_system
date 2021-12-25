<?php

namespace App\Http\Controllers\PoultryShed;

use App\Models\PartyFarm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerFarmController extends Controller
{
    public function index()
    {
        $farms = PartyFarm::with('party:id,name,cnic_no','type:id,name', 'subtype:id,name')->get();
        // dd($farms->toArray());
        return view('farmmanagement.customerfarms.index', compact('farms'));
    }

    public function create()
    {
        return view('farmmanagement.customerfarms.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
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

  
    public function destroy($id)
    {
        //
    }
}

