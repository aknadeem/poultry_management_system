<?php

namespace App\Http\Controllers\ChickenModule;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Party;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ChickGrade;
use App\Models\PartyCompany;
use Illuminate\Http\Request;
use App\Models\CompanyBalance;
use App\Models\ChickenPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class ChickenPurchaseController extends Controller
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
        return view('chickens.purchase.index');
    }

    public function getPurchaseList()
    {
        $chicken_purchases = ChickenPurchase::orderBy('id','DESC')->with('company:id,company_name')->get();
        return DataTables::of($chicken_purchases)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url= asset('storage/chicks/'.$row?->picture);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })->addColumn('company_id', function($row){
                return '<span> '.$row?->company?->company_name.' </span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm"
                PurchaseId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm" href="'.route("purchase.edit", $row["id"]).'"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("purchase.destroy", $row["id"]).'"
                del_title="Chicken Purchase" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture','company_id','Actions'])
            ->make(true);
    }

    public function create()
    {
        $purchase = new ChickenPurchase();
        dd('hello');

        // $vendors = Party::where([['is_vendor', 1], ['is_active', 1]])->with('company:id,party_id,company_name')->get(['id','name','guardian_name','cnic_no', 'contact_no', 'vendor_division_id']);
        $chick_grades = ChickGrade::get();

        $compaines = PartyCompany::where('is_active', 1)->with('vendor:id,name,guardian_name')->get(['id','party_id','company_name','company_address']);


        $customers = Party::where([['is_active', 1],['is_customer', 1]])->whereHas('farm')->with('farm:id,party_id,farm_name,farm_code,farm_capacity')->get(['id','is_customer','name','cnic_no','contact_no']);

        // dd($compaines->toArray());
        return view('chickens.purchase.create', compact('purchase','compaines', 'chick_grades','customers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_date' => 'bail|required|date',
            'chick_grade_id' => 'bail|required|integer',
            'company_id' => 'bail|required|integer',
            'chick_weight' => 'bail|required|numeric',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|nullable|string',
            'driver_name' => 'bail|nullable|string',
            'driver_contact' => 'bail|nullable|numeric',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]); 
        // $country = $session?->user?->getAddress()?->country;
        $message = 'Data created successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        // dd($request->toArray());
        
        try {
            $purchase = null;
            DB::transaction(function () use ($request) {

                if ($request->hasFile('image_file')) {
                    $path = 'chicks/';
                    $image_file = $request->file('image_file');
                    $extension = $request->file('image_file')->extension();
                    $imageName = time().mt_rand(10,99).'.'.$extension;
                    
                }else{
                    $imageName = null;
                }

                $purchase = ChickenPurchase::create([
                    'purchase_date' => $request->purchase_date,
                    'chick_grade_id' => $request->chick_grade_id,
                    'company_id' => $request->company_id,
                    'weight' => $request->chick_weight,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'discount_amount' => $request->discount_amount,
                    'discount_percentage' => $request->discount_percentage,
                    'total_price' => $request->total_price,

                    'bilty_number' => $request->bilty_number,
                    'bilty_charges' => $request->bilty_charges,
                    'sale_order_number' => $request->sale_order_number,
                    'delivery_order_number' => $request->delivery_order_number,

                    'vehicle_number' => $request->vehicle_number,
                    'driver_name' => $request->driver_name,
                    'driver_contact' => $request->driver_contact,

                    'picture' => $imageName,
                    'addedby' => $this->auth_user_id,
                ]);

                if($purchase){
                    $upload = $image_file->storeAs($path, $imageName, 'public');

                    $companyBalance = CompanyBalance::create([
                        'type' => 'chicken',
                        'company_id' => $request->company_id,
                        'chicken_purchase_id' => $purchase->id,
                        'total_amount' => $request->total_price,
                        'remaining_amount' => $request->total_price,
                        'dr' => $request->total_price,
                        'addedby' => $this->auth_user_id,
                    ]);
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

        return redirect()->route('purchase.index');
    }

    public function old_store(Request $request)
    {
        $this->validate($request, [
            'purchase_date' => 'bail|required|date',
            'chick_grade_id' => 'bail|required|integer',
            'company_id' => 'bail|required|integer',
            'chick_weight' => 'bail|required|numeric',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|nullable|string',
            'driver_name' => 'bail|nullable|string',
            'driver_contact' => 'bail|nullable|numeric',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]); 
        // $country = $session?->user?->getAddress()?->country;
        $message = 'Data created successfully!';
        $title = 'Saved';
        $icon_type = 'success';

        // dd($request->toArray());
        
        try {
            $purchase = null;
            DB::transaction(function () use ($request) {

                if ($request->hasFile('image_file')) {
                    $path = 'chicks/';
                    $image_file = $request->file('image_file');
                    $extension = $request->file('image_file')->extension();
                    $imageName = time().mt_rand(10,99).'.'.$extension;
                    
                }else{
                    $imageName = null;
                }

                $purchase = ChickenPurchase::create([
                    'purchase_date' => $request->purchase_date,
                    'chick_grade_id' => $request->chick_grade_id,
                    'company_id' => $request->company_id,
                    'weight' => $request->chick_weight,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'discount_amount' => $request->discount_amount,
                    'discount_percentage' => $request->discount_percentage,
                    'total_price' => $request->total_price,

                    'bilty_number' => $request->bilty_number,
                    'bilty_charges' => $request->bilty_charges,
                    'sale_order_number' => $request->sale_order_number,
                    'delivery_order_number' => $request->delivery_order_number,

                    'vehicle_number' => $request->vehicle_number,
                    'driver_name' => $request->driver_name,
                    'driver_contact' => $request->driver_contact,

                    'picture' => $imageName,
                    'addedby' => $this->auth_user_id,
                ]);

                if($purchase){
                    $upload = $image_file->storeAs($path, $imageName, 'public');

                    $companyBalance = CompanyBalance::create([
                        'type' => 'chicken',
                        'company_id' => $request->company_id,
                        'chicken_purchase_id' => $purchase->id,
                        'total_amount' => $request->total_price,
                        'remaining_amount' => $request->total_price,
                        'dr' => $request->total_price,
                        'addedby' => $this->auth_user_id,
                    ]);
                }     
            });
        }
        catch (\Throwable $e) {
            return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);

        return redirect()->route('purchase.index');
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
            'purchase_date' => 'bail|required|date',
            'company_id' => 'bail|required|integer',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|required|numeric',
            'discount_percentage' => 'bail|required|numeric',
            'total_price' => 'bail|required|numeric',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]);

        $chickenPurchase = ChickenPurchase::findOrFail($id);
        if ($request->hasFile('image_file')) {
            if($chickenPurchase?->picture != null && \Storage::disk('public')->exists('chickens/'.$chickenPurchase?->picture)){
                \Storage::disk('public')->delete('chickens/'.$chickenPurchase?->picture);
            }
            $path = 'chickens/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($chickenPurchase !=''){
            if($feed_data->picture !='' && $imageName == null){
                $imageName = $chickenPurchase->picture;
            }
            $update_chick = $chickenPurchase->update([
                'purchase_date' => $request->purchase_date,
                'vehicle_number' => $request->vehicle_number,
                'driver_name' => $request->driver_name,
                'driver_contact' => $request->driver_contact,
                'company_id' => $request->company_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
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

        return redirect()->route('purchase.index');
    }

    public function edit($id)
    {
        $purchase = ChickenPurchase::with('company:id,company_name,party_id','company.vendor:id,name,guardian_name')->find($id);;
        $chick_grades = ChickGrade::get();
        $compaines = PartyCompany::where('is_active', 1)->with('vendor:id,name,guardian_name')->get(['id','party_id','company_name','company_address']);

        return view('chickens.purchase.create', compact('purchase','compaines', 'chick_grades'));
    }

    public function destroy($id)
    {
        $purchase = ChickenPurchase::findOrFail($id);
        $img_path = 'chickens/'.$purchase?->picture;
        if($purchase?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $purchase->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        // return back();
        return redirect()->route('purchase.index');
    }
}

