<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::get();
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
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'farm_name' => $request->farm_name,
            'address' => $request->address
        ]);

        if($customer){
            $message = 'yes';
            return response()->json([
                'message' => 'New customer created successfully!',
                'success' => 'true',
            ], 201);
        }
        

    }

    public function show($id)
    {
        //
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


    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => 'update',
            'customer' => 'update method',
        ], 201);
    }

    public function destroy($id)
    {
        //
    }
}
