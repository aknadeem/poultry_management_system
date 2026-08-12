<?php

namespace App\Http\Controllers\ChickenModule;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Party;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Helpers\Constant;
use App\Models\ChickenSale;
use App\Models\PartyBalance;
use Illuminate\Http\Request;
use App\Models\BrokerBalance;
use App\Models\ChickenPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

use App\Http\Requests\ChickenModule\StoreChickenSaleRequest;
use App\Http\Requests\ChickenModule\UpdateChickenSaleRequest;
use App\Actions\ChickenModule\StoreChickenSaleAction;
use App\Actions\ChickenModule\UpdateChickenSaleAction;
use App\Actions\ChickenModule\DestroyChickenSaleAction;

class ChickenSaleController extends Controller
{
    private $auth_user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id = \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        return view('chickens.sale.index');
    }

    public function getSalesList()
    {
        $chicken_sales = ChickenSale::orderBy('id', 'DESC')->with('customer:id,name')->get();
        return DataTables::of($chicken_sales)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url = asset('storage/chickens/' . $row?->picture);
                return '<img class="rounded-circle avatar-lg" src="' . $url . '" alt="No image" />';
            })->addColumn('customer_id', function($row){
                return '<span> ' . $row?->customer?->name . ' </span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm"
                PurchaseId="' . $row["id"] . '" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm" href="' . route("sale.edit", $row["id"]) . '"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="' . route("sale.destroy", $row["id"]) . '"
                del_title="Sale id: ' . $row["id"] . '" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture', 'customer_id', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $sale = new ChickenSale();
        $customers = Party::where('is_customer', 1)->with('farm:id,farm_name,party_id')->get(['id', 'name', 'contact_no', 'cnic_no']);
        $brokers = Broker::where('is_active', 1)->get(['id', 'name', 'contact_no', 'cnic_no']);
        return view('chickens.sale.create', compact('sale', 'customers', 'brokers'));
    }

    public function store(StoreChickenSaleRequest $request, StoreChickenSaleAction $action)
    {
        try {
            $action->execute($request->validated(), $request->file('image_file'), $this->auth_user_id);
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

        return redirect()->route('sale.index');
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
        $sale = ChickenSale::with('customer:id,name,contact_no,farm_name')->findOrFail($id);
        $customers = Customer::get(['id', 'name', 'contact_no', 'farm_name']);
        $brokers = Broker::where('is_active', 1)->get(['id', 'name', 'contact_no', 'cnic_no']);
        return view('chickens.sale.create', compact('sale', 'customers', 'brokers'));
    }

    public function update(UpdateChickenSaleRequest $request, $id, UpdateChickenSaleAction $action)
    {
        try {
            $sale = ChickenSale::findOrFail($id);
            $action->execute($sale, $request->validated(), $request->file('image_file'), $this->auth_user_id);
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

        return redirect()->route('sale.index');
    }

    public function destroy($id, DestroyChickenSaleAction $action)
    {
        try {
            $sale = ChickenSale::findOrFail($id);
            $action->execute($sale);
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

        return redirect()->route('sale.index');
    }
}