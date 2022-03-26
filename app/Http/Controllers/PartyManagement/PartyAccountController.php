<?php

namespace App\Http\Controllers\PartyManagement;

use App\Models\Company;
use App\Models\PartyAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class PartyAccountController extends Controller
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
        $party_accounts = PartyAccount::get();
        return view('partymanagement.company.index', compact('party_accounts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'party_id' => 'bail|required|integer',
            'account_title' => 'bail|required|string',
            'account_number' => 'bail|required|string',
            'bank_name' => 'bail|required|string',
            'branch_code' => 'bail|nullable|string',
            'opening_balance' => 'bail|required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        
        if($request->party_account_id > 0){
            $party_account = PartyAccount::find($request->party_account_id);
        }else{
            $party_account = null;
        }

        if($request->party_account_id > 0){
            if($party_account !=''){
                $message = 'A Data Updated successfully!';
                $success = 'yes';
                $update_account = $party_account->update([
                    'party_id' => $request->party_id,
                    'account_title' => $request->account_title,
                    'account_number' => $request->account_number,
                    'bank_name' => $request->bank_name,
                    // 'branch_code' => $request->branch_code,
                    'opening_balance' => $request->opening_balance,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No data found against this id';
                $success = 'no';
            }
        }else{
            $PartyAccount = PartyAccount::create([
                'party_id' => $request->party_id,
                'account_title' => $request->account_title,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                // 'branch_code' => $request->branch_code,
                'opening_balance' => $request->opening_balance,
                'dr' => $request->opening_balance,
                'addedby' => $this->auth_user_id,
            ]);
            if($PartyAccount){
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
        $party_account = PartyAccount::find($id);
        if($party_account){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'party_account' => $party_account->toArray(),
            ], 201);
        }
    }
    
    public function destroy($id)
    {
        $party_account = PartyAccount::findOrFail($id);
        $party_account->delete();
        return redirect()->route('company.index');
    }
}
