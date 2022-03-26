<?php

namespace App\Http\Controllers\PartyManagement;

use DataTables;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CompanyBalancePayment;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class CompaniesBalanceController extends Controller
{
    private $auth_user_id;
    private $today_is;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
        $this->today_is = \Carbon\Carbon::now();
    }

    public function index()
    {
        return view('partymanagement.company.balance.index');
    }

    public function getCompaniesBalanceList()
    {
        $balances = CompanyBalance::with('company:id,company_name,company_address')->orderBy('id','DESC')->withCasts([
            'created_at' => 'date:d M, Y'
        ])->get();
        return DataTables::of($balances)
            ->addIndexColumn()
            ->addColumn('company_id', function($row){
                return '<span> '.$row?->company?->company_name.' </span>';
            })->addColumn('type', function($row){
                return ucfirst(str_replace('_', ' ', $row?->type));
            })
            ->addColumn('Action', function($row){


                if($row->remaining_amount > 0){
                    $payment_button = '<a class="btn btn-success btn-bold btn-sm openAddPaymentModal" data-id="'.$row["id"].'" href="javascript:void(0);" title="Click to add payment"><i
                        class="fa fa-plus"></i>
                    Payment
                    </a>';
                }else{
                    $payment_button = '';
                }
                return $payment_button.'<a class="btn btn-primary btn-sm"
                FeedId="'.$row["id"].'" href="'.route("companybalance.show", $row["id"]).'"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
                </a>';
            })
            ->rawColumns(['company_id', 'type','Action'])
            ->make(true);
    }

    public function getBalanceWithCompany($id)
    {
        $balance = CompanyBalance::with('productpurchase')->find($id);
        if($balance){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'balance' => $balance->toArray(),
            ], 201);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'company_balance_id' => 'bail|required|integer',
            'party_company_id' => 'bail|required|integer',
            'amount_payment' => 'bail|required|numeric',
            'payment_option' => 'bail|required|string',
            'cheque_date' => 'bail|required_if:payment_option,cheque|date',
            'bank_name' => 'bail|required_if:payment_option,cheque|string',
            'cheque_picture' => 'bail|required_if:payment_option,cheque',
            'description' => 'nullable',
            'image_file' => 'nullable',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('cheque_picture')) {
            $path = 'companies/payment/cheque/';
            $cheque_picture_file = $request->file('cheque_picture');
            $extension = $request->file('cheque_picture')->extension();
            $cheque_picture = time().mt_rand(10,99).'.'.$extension;
        }else{
            $cheque_picture = null;
        }
        if ($request->hasFile('image_file')) {
            $path = 'companies/payment/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
        }else{
            $imageName = null;
        }

        $CompanyBalance = CompanyBalance::find($request->company_balance_id);
        if($CompanyBalance !=''){
            $payment = CompanyBalancePayment::create([
                'company_balance_id' => $request->company_balance_id,
                'party_company_id' => $request->party_company_id,
                'paid_amount' => $request->amount_payment,
                'payment_option' => $request->payment_option,
                'cheque_date' => $request->cheque_date,
                'bank_name' => $request->bank_name,
                'cheque_picture' => $cheque_picture,
                'invoice_picture' => $imageName,
                'description' => $request->description,
                'addedby' => $this->auth_user_id,
            ]);
            
            if($payment){
                $ex_paid = $CompanyBalance->paid_amount;
                $new_paid = $ex_paid + $request->amount_payment;
                $ex_remaining = $CompanyBalance->remaining_amount;
                $new_rem = $ex_remaining - $request->amount_payment;
                if($new_rem < 1){
                    $balance_status = 'paid';
                }else{
                    $balance_status = 'pending';
                }
                $CompanyBalance->update([
                    'paid_amount' =>  $new_paid,
                    'remaining_amount' => $new_rem,
                    'status' => $balance_status,
                    'updatedby' => $this->auth_user_id,
                ]);

                $client = DB::table('account_payables')
            ->where('product_id', '=', $request->get('product'))
            ->first();

                $ac_payable = DB::table('account_payables')->insert([
                    'amount_type' => 'company_balance_payment',
                    'amount_status' => 'paid',
                    'entry_date' => today()->format('Y-m-d'),
                    'model_id' => $payment->id,
                    'total_amount' => $request->amount_payment,
                    'paid_amount' => $request->amount_payment,
                    'cr' => $request->amount_payment,
                    'created_at' => $this->today_is,
                    'addedby' => $this->auth_user_id,
                ]);

                if($cheque_picture != null){
                    $cheque_picture_file->storeAs($path, $cheque_picture, 'public');
                }
                if($imageName != null){
                    $image_file->storeAs($path, $imageName, 'public');
                }
                $message = 'Payment added successfully!';
                $success = 'yes';
            }else{
                $message = 'Something went wrong';
                $success = 'no';
            }
        }else{
            $message = 'No Balance Found against this record';
            $success = 'no';
        }

        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }

    public function show($id)
    {   
        $balance_payments = CompanyBalancePayment::where('company_balance_id', $id)->with('company:id,company_name,company_logo','addedBy:id,name,user_level_id')->get();
        return view('partymanagement.company.balancepayments.index', compact('balance_payments'));
    }

    public function edit($id)
    {
        $company = Company::find($id);
        if($company){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'company' => $company->toArray(),
            ], 201);
        }
    }
    
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $img_path = 'companies/'.$company?->company_logo;
        if($company?->company_logo != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $company->delete();
        return redirect()->route('company.index');
    }
}

