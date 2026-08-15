<?php

namespace App\Http\Controllers\PoultryShed;

use Session;
use DataTables;
use App\Models\Country;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\PersonalFarm;
use App\Models\EmployeeLevel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Actions\FarmManagement\StoreEmployeeAction;
use App\Actions\FarmManagement\UpdateEmployeeAction;
use App\Actions\FarmManagement\DestroyEmployeeAction;
use App\Http\Requests\FarmManagement\StoreEmployeeRequest;
use App\Http\Requests\FarmManagement\UpdateEmployeeRequest;

class EmployeeController extends Controller
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
        $employees = Employee::get();

        return view('farmmanagement.employee.index', compact('employees'));
    }

    public function create()
    {
        $employee = new Employee();
        $employee_types = EmployeeType::get();
        $employee_levels = EmployeeLevel::get();
        $farms = PersonalFarm::get(['id', 'farm_name', 'farm_address']);
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('farmmanagement.employee.create', compact(
            'employee',
            'countries',
            'employee_types',
            'employee_levels',
            'farms'
        ));
    }

    public function getEmployeeList()
    {
        $employees = Employee::orderBy('id', 'DESC')->get();

        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('employee_image', function ($row) {
                $url = asset('storage/employee/'.$row->employee_image);

                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })
            ->addColumn('Actions', function ($row) {
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                EmployeeId="'.$row['id'].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm"
                href="'.route('employee.edit', $row['id']).'"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route('employee.destroy', $row['id']).'"
                del_title="Employee" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['employee_image', 'Actions'])
            ->make(true);
    }

    public function store(StoreEmployeeRequest $request, StoreEmployeeAction $action)
    {
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $request->file('employee_image'),
                $request->file('employee_signature'),
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

        Session::flash('swal_notification', [
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);

        return redirect()->route('employee.index');
    }

    public function update(UpdateEmployeeRequest $request, $id, UpdateEmployeeAction $action)
    {
        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $employee = Employee::findOrFail($id);
            $action->execute(
                $employee,
                $request->validated(),
                $request->file('employee_image'),
                $request->file('employee_signature'),
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

        Session::flash('swal_notification', [
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);

        return redirect()->route('employee.index');
    }

    public function show($id)
    {
        $customer = Employee::find($id);
        if ($customer) {
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();
            $message = 'Employee Detail Data';
            $success = 'yes';
        } else {
            $message = 'No employee detail found against this id';
            $success = 'no';
            $html_data = '';
        }

        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);
    }

    public function edit($id)
    {
        $employee = Employee::with('country:id,name', 'province:id,name', 'city:id,name')->find($id);
        $employee_types = EmployeeType::get();
        $employee_levels = EmployeeLevel::get();
        $farms = PersonalFarm::get(['id', 'farm_name', 'farm_address']);
        $countries = Country::with(
            'provinces:id,name,country_id',
            'provinces.cities:id,name,province_id'
        )->get(['id', 'name']);

        return view('farmmanagement.employee.create', compact(
            'employee',
            'countries',
            'employee_types',
            'employee_levels',
            'farms'
        ));
    }

    public function destroy($id, DestroyEmployeeAction $action)
    {
        try {
            $employee = Employee::findOrFail($id);
            $action->execute($employee);
            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('employee.index');
    }

    public function forceDelete($id, FileUploadService $uploadService)
    {
        try {
            $employee = Employee::findOrFail($id);
            $uploadService->delete('employee', $employee->employee_image);
            $uploadService->delete('employee', $employee->employee_signature);
            $employee->forceDelete();
            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('employee.index');
    }
}
