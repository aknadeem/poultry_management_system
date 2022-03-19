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
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class ChickenSaleController extends Controller
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
        return view('chickens.sale.index');
    }

    public function getSalesList()
    {
        $chicken_sales = ChickenSale::orderBy('id','DESC')->with('customer:id,name')->get();
        return DataTables::of($chicken_sales)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url= asset('storage/chickens/'.$row?->picture);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })->addColumn('customer_id', function($row){
                return '<span> '.$row?->customer?->name.' </span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm"
                PurchaseId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm" href="'.route("sale.edit", $row["id"]).'"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("sale.destroy", $row["id"]).'"
                del_title="Sale id: '.$row["id"].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture','customer_id','Actions'])
            ->make(true);
    }

    public function create()
    {
        $sale = new ChickenSale();
        $customers = Party::where('is_customer', 1)->with('farm:id,farm_name,party_id')->get(['id','name','contact_no','cnic_no']);
        $brokers = Broker::where('is_active', 1)->get(['id','name','contact_no','cnic_no']);
        // dd($brokers);
        return view('chickens.sale.create', compact('sale','customers','brokers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'manual_number' => 'bail|required|string',
            'sale_date' => 'bail|required|date',
            'customer_id' => 'bail|required|integer',
            'broker_id' => 'bail|required|integer',
            'first_weight' => 'bail|nullable|numeric',
            'second_weight' => 'bail|nullable|numeric',
            'net_weight' => 'bail|nullable|numeric',
            'total_weight' => 'bail|required|numeric',
            'per_kg_price' => 'bail|required|numeric',
            'discount_amount' => 'bail|required|numeric',
            'discount_percentage' => 'bail|required|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|required|string',
            'driver_name' => 'bail|required|string',
            'driver_contact' => 'bail|required|numeric',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]);
        try {
            $message = 'Data created successfully!';
            $title = 'Saved';
            $icon_type = 'success';
            DB::transaction(function () use ($request) {
                if ($request->hasFile('image_file')) {
                    $path = 'chickens/sales/';
                    $image_file = $request->file('image_file');
                    $extension = $request->file('image_file')->extension();
                    $imageName = time().mt_rand(10,99).'.'.$extension;
                }else{
                    $imageName = null;
                }
                $sale = ChickenSale::create([
                    'manual_number' => $request->manual_number,
                    'sale_date' => $request->sale_date,
                    'vehicle_number' => $request->vehicle_number,
                    'driver_name' => $request->driver_name,
                    'driver_contact' => $request->driver_contact,
                    // 'customer_id' => $request->customer_id,
                    'party_id' => $request->customer_id,
                    'broker_id' => $request->broker_id,
                    'broker_commission' => $request->broker_commission,
                    'first_weight' => $request->first_weight,
                    'second_weight' => $request->second_weight,
                    'net_weight' => $request->net_weight,
                    'total_weight' => $request->total_weight,
                    'per_kg_price' => $request->per_kg_price,
                    'discount_amount' => $request->discount_amount,
                    'discount_percentage' => $request->discount_percentage,
                    'total_price' => $request->total_price,
                    'picture' => $imageName,
                    'addedby' => $this->auth_user_id,
                ]);
                if($sale){
                    if($imageName != null){
                        $upload = $image_file->storeAs($path, $imageName, 'public');
                    }

                    $partyBalance = PartyBalance::create([
                        'party_id' => $request->customer_id,
                        'total_amount' => $request->total_price,
                        'remaining_amount' => $request->total_price,
                        'transaction_date' => $request->sale_date,
                        'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                        'narration' => 'Chicken sale balance',
                        'addedby' => $this->auth_user_id,
                    ]);

                    $broker_balance = BrokerBalance::create([
                        'broker_id' => $request->broker_id,
                        'dr' => $request->broker_commission,
                        'total_amount' => $request->broker_commission,
                        'narration' => 'chicken sale commession',
                        'addedby' => $this->auth_user_id,
                    ]);
                    
                }else{
                    $message = 'Something went wrong';
                    $title = 'Error';
                    $icon_type = 'warning';
                }
            });
        } catch (\Throwable $e) {
            return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }
        
        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('sale.index');
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

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'vehicle_number' => 'bail|required|string',
            'driver_name' => 'bail|required|string',
            'driver_contact' => 'bail|required|numeric',
            'sale_date' => 'bail|required|date',
            'customer_id' => 'bail|required|integer',
            'total_weight' => 'bail|nullable|numeric',
            'per_kg_price' => 'bail|required|numeric',
            'discount_amount' => 'bail|required|numeric',
            'discount_percentage' => 'bail|required|numeric',
            'total_price' => 'bail|required|numeric',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]);

        $chickenSale = ChickenSale::findOrFail($id);
        if ($request->hasFile('image_file')) {
            if($chickenSale?->picture != null && \Storage::disk('public')->exists('chickens/'.$chickenSale?->picture)){
                \Storage::disk('public')->delete('chickens/'.$chickenSale?->picture);
            }
            $path = 'chickens/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($chickenSale !=''){
            if($chickenSale->picture !='' && $imageName == null){
                $imageName = $chickenSale->picture;
            }
            $update_sale = $chickenSale->update([
                'sale_date' => $request->sale_date,
                'vehicle_number' => $request->vehicle_number,
                'driver_name' => $request->driver_name,
                'driver_contact' => $request->driver_contact,
                'customer_id' => $request->customer_id,
                'total_weight' => $request->total_weight,
                'per_kg_price' => $request->per_kg_price,
                'discount_amount' => $request->discount_amount,
                'discount_percentage' => $request->discount_percentage,
                'total_price' => $request->total_price,
                'picture' => $imageName,
                'updatedby' => $this->auth_user_id,
            ]);
            $message = 'Data Updated Successfully!';
            $title = 'Updated';
            $icon_type = 'success';

        }else{
            $message = 'No entry found against this id';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('sale.index');
    }

    public function edit($id)
    {
        $sale = ChickenSale::with('customer:id,name,contact_no,farm_name')->findOrFail($id);
        $customers = Customer::get(['id','name','contact_no','farm_name']);
        $brokers = Broker::where('is_active', 1)->get(['id','name','contact_no','cnic_no']);
        return view('chickens.sale.create', compact('sale','customers','brokers'));
    }

    public function destroy($id)
    {
        $sale = ChickenSale::findOrFail($id);
        $img_path = 'chickens/'.$sale?->picture;
        if($sale?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $sale->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('sale.index');
    }
}