<?php

namespace App\Http\Controllers\ChickenModule;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Party;
use App\Models\Company;
use App\Models\Employee;
use App\Helpers\Constant;
use App\Models\PartyFarm;
use App\Models\ChickGrade;
use App\Models\PartyBalance;
use App\Models\PartyCompany;
use App\Models\PersonalFarm;
use Illuminate\Http\Request;
use App\Models\ChickPurchase;
use App\Models\CompanyBalance;
use App\Models\ChickenPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PartyFarmChickHistory;
use App\Models\PersonalFarmChickHistory;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

use App\Http\Requests\ChickenModule\StoreChickPurchaseRequest;
use App\Http\Requests\ChickenModule\UpdateChickPurchaseRequest;
use App\Actions\ChickenModule\StoreChickPurchaseAction;
use App\Actions\ChickenModule\UpdateChickPurchaseAction;
use App\Actions\ChickenModule\DestroyChickPurchaseAction;

class ChickPurchaseController extends Controller
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
        return view('chickens.purchase.index');
    }

    public function getPurchaseList()
    {
        $chick_purchases = ChickPurchase::orderBy('id', 'DESC')->with('company:id,company_name')->get();
        return DataTables::of($chick_purchases)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url = asset('storage/chicks/' . $row?->picture);
                return '<img class="rounded-circle avatar-lg" src="' . $url . '" alt="No image" />';
            })->addColumn('company_id', function($row){
                return '<span> ' . $row?->company?->company_name . ' </span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm"
                PurchaseId="' . $row["id"] . '" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm" href="' . route("purchase.edit", $row["id"]) . '"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="' . route("purchase.destroy", $row["id"]) . '"
                del_title="Chicken Purchase" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture', 'company_id', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $purchase = new ChickPurchase();
        $chick_grades = ChickGrade::get();
        $personal_farms = PersonalFarm::where('is_active', 1)->get();
        $compaines = PartyCompany::where('is_active', 1)->with('vendor:id,name,guardian_name')->get(['id', 'party_id', 'company_name', 'company_address']);
        $customers = Party::where([['is_active', 1], ['is_customer', 1]])->whereHas('farm')->with('farm:id,party_id,farm_name,farm_code,farm_capacity')->get(['id', 'is_customer', 'name', 'cnic_no', 'contact_no']);

        return view('chickens.purchase.create', compact('purchase', 'compaines', 'chick_grades', 'personal_farms', 'customers'));
    }

    public function store(StoreChickPurchaseRequest $request, StoreChickPurchaseAction $action)
    {
        try {
            $action->execute($request->validated(), $request->file('image_file'), $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

        return redirect()->route('purchase.index');
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
        $purchase = ChickPurchase::with('company:id,company_name,party_id', 'company.vendor:id,name,guardian_name')->find($id);
        $personal_farms = PersonalFarm::where('is_active', 1)->get();
        $chick_grades = ChickGrade::get();
        $compaines = PartyCompany::where('is_active', 1)->with('vendor:id,name,guardian_name')->get(['id', 'party_id', 'company_name', 'company_address']);

        return view('chickens.purchase.create', compact('purchase', 'compaines', 'chick_grades', 'personal_farms'));
    }

    public function update(UpdateChickPurchaseRequest $request, $id, UpdateChickPurchaseAction $action)
    {
        try {
            $purchase = ChickPurchase::findOrFail($id);
            $action->execute($purchase, $request->validated(), $request->file('image_file'), $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data Updated Successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('purchase.index');
    }

    public function destroy($id, DestroyChickPurchaseAction $action)
    {
        try {
            $purchase = ChickPurchase::findOrFail($id);
            $action->execute($purchase);
            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('purchase.index');
    }
}