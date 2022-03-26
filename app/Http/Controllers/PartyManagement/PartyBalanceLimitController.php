<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Company;
use App\Models\PartyAccount;
use Illuminate\Http\Request;
use App\Models\PartyBalanceLimit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class PartyBalanceLimitController extends Controller
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
        $balance_limits = PartyBalanceLimit::get();
        return view('partymanagement.company.index', compact('balance_limits'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'party_id' => 'bail|required|integer',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'debit_limit' => 'bail|required|numeric',
            'credit_limit' => 'bail|required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        
        if($request->party_balance_limit_id > 0){
            $balance_limit = PartyBalanceLimit::find($request->party_balance_limit_id);
        }else{
            $balance_limit = null;
        }

        if($request->party_balance_limit_id > 0){
            if($balance_limit !=''){
                $message = 'A Company Data Updated successfully!';
                $success = 'yes';
                $update_account = $balance_limit->update([
                    'party_id' => $request->party_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'debit_limit' => $request->debit_limit,
                    'credit_limit' => $request->credit_limit,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No data found against this id';
                $success = 'no';
            }
        }else{
            $balance_limit = PartyBalanceLimit::create([
                'party_id' => $request->party_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'debit_limit' => $request->debit_limit,
                'credit_limit' => $request->credit_limit,
                'addedby' => $this->auth_user_id,
            ]);
            if($balance_limit){
                $message = 'Data created successfully!';
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
       return 0;
    }

    public function edit($id)
    {
        $balance_limit = PartyBalanceLimit::find($id);
        if($balance_limit){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'balance_limit' => $balance_limit->toArray(),
            ], 201);
        }
    }
    
    public function destroy($id)
    {
        $balance_limit = PartyBalanceLimit::findOrFail($id);
        $balance_limit->delete();
        return redirect()->route('company.index');
    }
}
