<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class SupplierController extends Controller
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
        $customers = Customer::where('type', 'supplier')->get();
        return view('partymanagement.customers.index', compact('customers'));
    }

    public function create()
    {
        //
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
                $message = 'A supplier Updated successfully!';
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
                'type' => 'supplier',
                'address' => $request->address,
                'image' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);
            if($customer){
                $message = 'New supplier created successfully!';
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
            $message = 'Supplier Detail Data';
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