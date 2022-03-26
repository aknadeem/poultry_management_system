<?php

namespace App\Http\Controllers\BalanceManagement;

use Validator;
use App\Models\Party;
use App\Helpers\Constant;
use App\Models\PartyBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartyBalancePayment;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PartyBalanceController extends Controller
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
        $balances = PartyBalance::with('party:id,name,cnic_no')->orderBy('id','DESC')->get();

        // dd($balances->toArray());
        return view('balancemanagement.party_balances.index', compact('balances'));
    }

    public function getPartyBalances()
    {
        $balances = PartyBalance::with('party:id,name,cnic_no')->orderBy('id','DESC')->get();
        // dd($balances->toArray());
        if($balances->count() > 0){
            $message = 'yes';
            $balances = $balances->toArray();
        }else{
            $message = 'no';
            $balances = collect();
        }

        return response()->json([
            'message' => $message,
            'balances' => $balances,
        ], 201);
    }

    public function getParties()
    {
        $parties = Party::whereHas('balances')->orderBy('id','DESC')->get();
        // dd($parties->toArray());
        if($parties->count() > 0){
            $message = 'yes';
            $parties = $parties->toArray();
        }else{
            $message = 'no';
            $parties = collect();
        }

        return response()->json([
            'message' => $message,
            'parties' => $parties,
        ], 201);
    }

    public function getBalanceList()
    {
        $balances = PartyBalance::with('party:id,name,cnic_no')->orderBy('id','DESC')->withCasts([
            'created_at' => 'date:d M, Y'
        ])->get();
        return DataTables::of($balances)
            ->addIndexColumn()
            ->addColumn('party_id', function($row){
                return '<span> '.$row?->party?->name.' </span>';
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
            ->rawColumns(['party_id', 'Action'])
            ->make(true);
    }

    public function show($id)
    {
        $balance = PartyBalance::find($id);
        if($balance){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'balance' => $balance->toArray(),
            ], 201);
        }
    }

    public function getBalancePayments($id)
    {
        $payments = PartyBalancePayment::where('party_balance_id',$id)->with('party:id,name,email,is_vendor,is_customer,profile_picture,contact_no','user:id,name')->orderBy('id', 'DESC')->get();
        return view('balancemanagement.party_balances.balance_payments', compact('payments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'balance_id' => 'bail|required|integer',
            'party_id' => 'bail|required|integer',
            'amount_payment' => 'bail|required|numeric',
            'paid_date' => 'bail|required|date',
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
    
        $message = 'Payment added successfully!';
        $success = 'yes';

        try {
            $saved = DB::transaction(function () use ($request) {
                if ($request->hasFile('cheque_picture')) {
                    $path = 'parties/payment_receive/cheque/';
                    $cheque_picture_file = $request->file('cheque_picture');
                    $extension = $request->file('cheque_picture')->extension();
                    $cheque_picture = time().mt_rand(10,99).'.'.$extension;
                }else{
                    $cheque_picture = null;
                }
                if ($request->hasFile('image_file')) {
                    $path = 'parties/payment_receive/';
                    $image_file = $request->file('image_file');
                    $extension = $request->file('image_file')->extension();
                    $imageName = time().mt_rand(10,99).'.'.$extension;
                }else{
                    $imageName = null;
                }
                $partyBalance = PartyBalance::find($request->balance_id);
                if($partyBalance !=''){
                    $payment = PartyBalancePayment::create([
                        'party_balance_id' => $request->balance_id,
                        'party_id' => $request->party_id,
                        'paid_amount' => $request->amount_payment,
                        'paid_date' => $request->paid_date,
                        'payment_option' => $request->payment_option,
                        'cheque_date' => $request->cheque_date,
                        'bank_name' => $request->bank_name,
                        'cheque_picture' => $cheque_picture,
                        'invoice_picture' => $imageName,
                        'narration' => $request->description,
                        'addedby' => $this->auth_user_id,
                    ]);
                    
                    if($payment){
                        $ex_paid = $partyBalance->paid_amount;
                        $new_paid = $ex_paid + $request->amount_payment;
                        $ex_remaining = $partyBalance->remaining_amount;
                        $new_rem = $ex_remaining - $request->amount_payment;
                        if($new_rem < 1){
                            $balance_status = Constant::PAYMENT_STATUS['Paid'];
                        }else{
                            $balance_status = Constant::PAYMENT_STATUS['Pending'];
                        }
                        $partyBalance->update([
                            'paid_amount' =>  $new_paid,
                            'remaining_amount' => $new_rem,
                            'payment_status' => $balance_status,
                            'updatedby' => $this->auth_user_id,
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
            });
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        

        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }
}
