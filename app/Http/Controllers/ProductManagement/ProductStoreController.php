<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use DataTables;
use App\Models\Country;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\PartyCompany;
use App\Models\PersonalFarm;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use App\Models\EmployeeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class ProductStoreController extends Controller
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
        $products = collect();
        return view('productmanagement.index', compact('products'));
    }

    public function create()
    {
        $product = new Employee();
        $companies = PartyCompany::get();
        return view('productmanagement.create', compact('product','companies'));
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);

        $message = 'Data created successfully';
        $success = 'yes';
        try {
            $product_store = ProductStore::create([
                'store_name' => $request->store_name,
                'store_type' => $request->store_type,
                'store_area' => $request->store_area,
                'total_racks' => $request->total_racks,
                'description' => $request->store_desciption,
                'addedby' => $this->auth_user_id,
            ]);
            $product_store = $product_store->toArray();
        }
        catch (\Throwable $e) {
            return $e;
            $message = 'Something went wrong';
            $success = 'no';
            $product_store = [];
        }

        return response()->json([
            'message' => $message,
            'success' => $success,
            'data' => $product_store,
        ], 201);
    }
    
    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);

        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {

            $Employee_data = Employee::findOrFail($id);

            if ($request->hasFile('employee_image')) {
                if($Employee_data?->employee_image != null && \Storage::disk('public')->exists('employee/'.$Employee_data?->employee_image)){
                    \Storage::disk('public')->delete('employee/'.$Employee_data?->employee_image);
                }
                $employee_image_file = $request->file('employee_image');
                $extension = $request->file('employee_image')->extension();
                $employee_image = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_image = null;
            }

            if ($request->hasFile('employee_signature')) {
                if($Employee_data?->employee_signature != null && \Storage::disk('public')->exists('employee/'.$Employee_data?->employee_signature)){
                    \Storage::disk('public')->delete('employee/'.$Employee_data?->employee_signature);
                }
                $employee_signature_file = $request->file('employee_signature');
                $extension = $request->file('employee_signature')->extension();
                $employee_signature = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_signature = null;
            }

            if($Employee_data->employee_image !='' && $employee_image == null){
                $employee_image = $Employee_data->employee_image;
            }
            
            if($Employee_data->employee_signature !='' && $employee_image == null){
                $employee_image = $Employee_data->employee_signature;
            }


            $Employee_data->update([
                'personal_farm_id' => $request->personal_farm_id,
                'employee_type_id' => $request->employee_type_id,
                'employee_level_id' => $request->employee_level_id,
                'name' =>  $request->name,
                'guardian_name' => $request->guardian_name,
                'contact_no' => $request->contact_no,
                'other_number' => $request->other_number,
                'other_number' => $request->other_number,
                'email' => $request->email,
                'cnic_no' => $request->cnic_no,
                'basic_salary' => $request->basic_salary,
                'other_amount' => $request->other_amount,
                'net_salary' => $request->net_salary,
                'contract_period' => $request->contract_period,
                'date_of_birth' => $request->date_of_birth,
                'joining_date' => $request->joining_date,
                'is_police_record' => $request->is_police_record,
                'address' => $request->address,
                'description' => $request->description,
                'blood_group' => $request->blood_group,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,

                'employee_image' => $employee_image,
                'employee_signature' => $employee_signature,
                'updatedby' => $this->auth_user_id,
            ]);

            if($Employee_data){
                if($request->hasFile('employee_image') && $employee_image != null){
                    $upload_to_folder = $employee_image_file->storeAs('employee/', $employee_image, 'public');
                }
                if($request->hasFile('employee_signature') && $employee_signature != null){
                    $upload_to_folder_sig = $employee_signature_file->storeAs('employee/', $employee_signature, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('employee.index');
    }

    public function show($id)
    {
        $customer = Employee::find($id);
        if($customer){
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();
            $message = 'Employee Detail Data';
            $success = 'yes';
        }else{
            $message = 'No employee detail found against this id';
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
        $pruchase = new Employee();
        $companies = PartyCompany::get();
        return view('productmanagement.purchases.index', compact('pruchase','companies'));
    }

    public function validationRules($request, $id)
    {
        $validator = Validator::make($request->all(),[
        // $this->validate($request, [
            'store_name' => 'bail|required|string',
            'store_type' => 'bail|required|string',
            'total_racks' => 'bail|required|integer',
            'store_area' => 'bail|required|numeric',
            'store_desciption' => 'bail|nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        // $img_path = 'employees/'.$employee?->employee_image;
        // if($employee?->employee_image != null && \Storage::disk('public')->exists($img_path)){
        //     \Storage::disk('public')->delete($img_path);
        // }
        $employee->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        
        return redirect()->route('employee.index');
    }
    
    public function forceDelete($id)
    {
        $employee = Employee::findOrFail($id);
        $img_path = 'employee/'.$employee?->employee_image;
        $img_path_sign = 'employee/'.$employee?->employee_signature;
        if($employee?->employee_image != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        
        if($employee?->employee_signature != null && \Storage::disk('public')->exists($img_path_sign)){
            \Storage::disk('public')->delete($img_path_sign);
        }
        $employee->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('employee.index');
    }
}