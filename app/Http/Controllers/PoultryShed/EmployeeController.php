<?php

namespace App\Http\Controllers\PoultryShed;

use App\Models\Employee;
use Illuminate\Http\Request;
use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;
use Session;

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
        // return DataTables::of($employees)->make(true);
        return view('partymanagement.employee.index', compact('employees'));
    }

    public function getEmployeeList()
    {
        $employees = Employee::orderBy('id','DESC')->get();
        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('employee_image', function($row){
                $url= asset('storage/employees/'.$row?->employee_image);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                EmployeeId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openEmployeeModal"
                EmployeeId="'.$row["id"].'" data-id="'.$row["id"].'" id="editEmployeeModal" href="javascript:void(0);"
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
        $validator = Validator::make($request->all(),[
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'cnic' => 'bail|numeric',
            'date_of_birth' => 'bail|date',
            'designation' => 'bail|string',
            'department' => 'bail|string',
            'address' => 'bail|required|string',
            'image_file' => 'nullable',
            'description' => 'nullable|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        
        if($request->employee_id_modal > 0){
            $employee_data = Employee::find($request->employee_id_modal);
        }else{
            $employee_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($employee_data?->empoyee_image != null && \Storage::disk('public')->exists('empoyees/'.$employee_data?->empoyee_image)){
                \Storage::disk('public')->delete('empoyees/'.$employee_data?->empoyee_image);
            }
            $path = 'employees/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->employee_id_modal > 0){
            if($employee_data !=''){
                $message = 'A Company Data Updated successfully!';
                $success = 'yes';
                if($employee_data->employee_image !='' && $imageName == null){
                    $imageName = $employee_data->employee_image;
                }
                $update_company = $employee_data->update([
                    'name' => $request->name,
                    'contact_no' => $request->contact_no,
                    'email' => $request->email,
                    'address' => $request->address,
                    'cnic' => $request->cnic,
                    'date_of_birth' => $request->date_of_birth,
                    'designation' => $request->designation,
                    'department' => $request->department,
                    'employee_image' => $imageName,
                    'description' => $request->description,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No Employee detail found against this id';
                $success = 'no';
            }
        }else{
            $employee = Employee::create([
                'name' => $request->name,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'date_of_birth' => $request->date_of_birth,
                'designation' => $request->designation,
                'department' => $request->department,
                'employee_image' => $imageName,
                'description' => $request->description,
                'addedby' => $this->auth_user_id,
            ]);
            if($employee){
                $message = 'New Employee Data created successfully!';
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
        $employee = Employee::find($id);
        if($employee){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'employee' => $employee->toArray(),
            ], 201);
        }
    }
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $img_path = 'employees/'.$employee?->employee_image;
        if($employee?->employee_image != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $employee->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        
        return redirect()->route('employee.index');
    }
}