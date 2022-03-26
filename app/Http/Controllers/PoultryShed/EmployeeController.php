<?php

namespace App\Http\Controllers\PoultryShed;

use Session;
use DataTables;
use App\Models\Country;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\PersonalFarm;
use Illuminate\Http\Request;
use App\Models\EmployeeLevel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class EmployeeController extends Controller
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
        $employees = Employee::get();
        return view('farmmanagement.employee.index', compact('employees'));
    }

    public function create()
    {
        $employee = new Employee();
        $employee_types = EmployeeType::get();
        $employee_levels = EmployeeLevel::get();
        $farms = PersonalFarm::get(['id','farm_name','farm_address']);

        // dd($farms->toArray());
        
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        return view('farmmanagement.employee.create', compact('employee','countries', 'employee_types', 'employee_levels', 'farms'));
    }

    public function getEmployeeList()
    {
        $employees = Employee::orderBy('id','DESC')->get();
        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('employee_image', function($row){
                $url = asset('storage/employee/'.$row->employee_image);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                EmployeeId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm"
                href="'.route("employee.edit", $row["id"]).'"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("employee.destroy", $row["id"]).'"
                del_title="Employee" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['employee_image','Actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);

        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {
            if ($request->hasFile('employee_image')) {
                $employee_image_file = $request->file('employee_image');
                $extension = $request->file('employee_image')->extension();
                $employee_image = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_image = null;
            }
            
            if ($request->hasFile('employee_signature')) {
                $employee_signature_file = $request->file('employee_signature');
                $extension = $request->file('employee_signature')->extension();
                $employee_signature = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_signature = null;
            }

            $employee = Employee::create([
                // 'personal_farm_id' => $request->personal_farm_id,
                'employee_type_id' => $request->employee_type_id,
                'employee_level_id' => $request->employee_level_id,
                'name' =>  $request->name,
                'guardian_name' => $request->guardian_name,
                'contact_no' => $request->contact_no,
                'other_number' => $request->other_number,
                'other_number' => $request->other_number,
                'email' => $request->email,
                'cnic_no' => $request->cnic_no,
                'father_cnic_no' => $request->father_cnic_no,
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
                'addedby' => $this->auth_user_id,
            ]);

            if($employee){

                if($employee_image != null){
                    $upload_to_folder = $employee_image_file->storeAs('employee/', $employee_image, 'public');
                }

                if($employee_signature != null){
                    $upload_to_folder_sig = $employee_signature_file->storeAs('employee/', $employee_signature, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('employee.index');
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
                // 'personal_farm_id' => $request->personal_farm_id,
                'employee_type_id' => $request->employee_type_id,
                'employee_level_id' => $request->employee_level_id,
                'name' =>  $request->name,
                'guardian_name' => $request->guardian_name,
                'contact_no' => $request->contact_no,
                'other_number' => $request->other_number,
                'other_number' => $request->other_number,
                'email' => $request->email,
                'father_cnic_no' => $request->father_cnic_no,
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
            Log::error($e);
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
        $employee = Employee::with('country:id,name','province:id,name','city:id,name')->find($id);
        $employee_types = EmployeeType::get();
        $employee_levels = EmployeeLevel::get();
        $farms = PersonalFarm::get(['id','farm_name','farm_address']);

        // dd($farms->toArray());
        
        $countries = Country::with('provinces:id,name,country_id',
        'provinces.cities:id,name,province_id')->get(['id','name']);

        return view('farmmanagement.employee.create', compact('employee','countries', 'employee_types', 'employee_levels', 'farms'));
    }

    public function validationRules($request, $id)
    {
        $validator = Validator::make($request->all(),[
        // $this->validate($request, [
            // 'personal_farm_id' => 'bail|nullable|integer',
            'employee_type_id' => 'bail|nullable|integer',
            'employee_level_id' => 'bail|required|integer',
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'other_number' => 'bail|nullable|numeric',
            'email' => 'bail|required|string',
            'cnic_no' => 'bail|required|numeric|unique:employees,cnic_no,'.$id,
            'father_cnic_no' => 'nullable',
            'basic_salary' => 'bail|required|numeric',
            'other_amount' => 'bail|nullable|numeric',
            'net_salary' => 'bail|required|numeric',
            'contract_period' => 'bail|numeric|numeric',
            'date_of_birth' => 'bail|date',
            'joining_date' => 'bail|date',
            'is_police_record' => 'bail|nullable',
            'address' => 'bail|required|string',
            'blood_group' => 'bail|nullable',
            'description' => 'nullable|string',

            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',

            'employee_signature' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'employee_image' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',

        ],[
            'farm_image.required'=> 'The farm image field is required',
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