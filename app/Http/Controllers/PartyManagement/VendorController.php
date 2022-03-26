<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Party;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Division;
use App\Models\VendorType;
use Illuminate\Support\Str;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\ConductPerson;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class VendorController extends Controller
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
        $customers = Party::where('is_vendor', 1)->with('farm:id,party_id,farm_name,farm_type_id')->get(['id','is_customer','name', 'guardian_name','cnic_no', 'contact_no', 'customer_type_id','customer_division_id', 'profile_picture']);
        return view('partymanagement.vendors.index', compact('customers'));
    }

    public function create()
    {
        $party = new Party();
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        $divisions = Division::get();
        $contact_persons = ConductPerson::get();

        $vendor_types = VendorType::get();
        $business_types = BusinessType::get();
        
        return view('partymanagement.vendors.create', compact('countries', 'party',  'business_types','divisions' , 'vendor_types','contact_persons'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'farm_name' => 'bail|string',
            'address' => 'bail|required|string',
            'image_file' => 'nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        if($request->customer_id_modal > 0){
            $customer_data = Customer::find($request->customer_id_modal);
        }else{
            $customer_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($customer_data?->image != null && \Storage::disk('public')->exists('customers/'.$customer_data?->image)){
                \Storage::disk('public')->delete('customers/'.$customer_data?->image);
            }
            $path = 'customers/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->customer_id_modal > 0){
            if($customer_data !=''){
                $message = 'A customer Updated successfully!';
                $success = 'yes';
                if($customer_data->image !='' && $imageName == null){
                    $imageName = $customer_data->image;
                }
                $update_customer = $customer_data->update([
                    'name' => $request->name,
                    'contact_no' => $request->contact_no,
                    'email' => $request->email,
                    'farm_name' => $request->farm_name,
                    'address' => $request->address,
                    'updatedby' => $this->auth_user_id,
                    'image' => $imageName,
                ]);
            }else{
                $message = 'No customer found against thi id';
                $success = 'no';
            }
        }else{
            $customer = Customer::create([
                'name' => $request->name,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'farm_name' => $request->farm_name,
                'type' => 'customer',
                'address' => $request->address,
                'image' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);
            if($customer){
                $message = 'New customer created successfully!';
                $success = 'yes';
            }else{
                $message = 'Something went wrong';
                $success = 'no';
            }
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if($customer){
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();
            $message = 'Cutomer Detail Data';
            $success = 'yes';
        }else{
            $message = 'No customer found against this id';
            $success = 'no';
            $html_data = '';
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);

        return response()->json($data, 200, $headers);
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        if($customer){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'customer' => $customer->toArray(),
            ], 201);
        }
    }

    public function storeAllType(Request $request)
    {
        $table_name = $request->tag_name;
        $validator = Validator::make($request->all(),[
            'name' => 'bail|required|string|unique:'.$table_name.',name',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }

        if ($table_name !='' && Schema::hasTable($table_name) ) {
            $save_data = DB::table($table_name)->insertGetId([
                'name' => $request->name,
                'slug' => Str::of($request->name)->slug('-'),
            ]);
            if($save_data){
                $message = 'Data created successfully!';
                $success = 'yes';
                $icon_type = 'success';
                $result = ['id' => $save_data, 'name'  => $request->name];
            }else{
                $message = 'Data not saved, Something went wrong';
                $success = 'no';
                $icon_type = 'warning';
                $result = [];
            }
        }else{
            $message = 'Something went wrong';
            $success = 'no';
            $icon_type = 'danger';
            $result = [];
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'data' => $result,
        ], 201);
    }
    
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $img_path = 'customers/'.$customer?->image;
        if($customer?->image != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $customer->delete();
        return redirect()->route('customer.index');
    }

}
