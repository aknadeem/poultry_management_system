<?php

namespace App\Http\Controllers\ChickenModule;

use Session;
use DataTables;
use App\Models\Feed;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\ChickenPurchase;
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
        $chicken_purchases = ChickenPurchase::orderBy('id','DESC')->with('company:id,name')->get();
        return DataTables::of($chicken_purchases)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url= asset('storage/chickens/'.$row?->picture);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })->addColumn('company_id', function($row){
                return '<span> '.$row?->company?->name.' </span>';
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
        $compaines = Company::get(['id','name','contact_no','address']);
        return view('chickens.purchase.create', compact('purchase','compaines'));
    }

    public function store(Request $request)
    {
        // dd($request->toArray());

        $this->validate($request, [
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
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            $path = 'chickens/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }
        $purchase = ChickenPurchase::create([
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
            'addedby' => $this->auth_user_id,
        ]);

        if($purchase){
            $message = 'Data created successfully!';
            $title = 'Saved';
            $icon_type = 'success';

        }else{
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
        $purchase = ChickenPurchase::with('company')->find($id);
        $compaines = Company::get(['id','name','contact_no','address']);
        return view('chickens.purchase.create', compact('purchase','compaines'));

        // $feed = Feed::with('company:id,name,address,contact_no')->find($id);
        // if($feed){
        //     $message = 'yes';
        //     return response()->json([
        //         'message' => $message,
        //         'feed' => $feed->toArray(),
        //     ], 201);
        // }
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

