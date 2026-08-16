<?php

namespace App\Http\Controllers\BalanceManagement;

use Validator;
use App\Models\Party;
use App\Helpers\Constant;
use App\Models\PartyBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PartyBalancePayment;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Actions\BalanceManagement\RecordPartyBalancePaymentAction;
use App\Http\Requests\BalanceManagement\StorePartyBalancePaymentRequest;

class PartyBalanceController extends Controller
{
    private $authUserId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId= \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        $balances = PartyBalance::with('party:id,name,cnic_no')->orderBy('id','DESC')->get();
        return view('balancemanagement.party_balances.index', compact('balances'));
    }

    public function getPartyBalances()
    {
        $balances = PartyBalance::with('party:id,name,cnic_no')->orderBy('id','DESC')->get();
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
        $payments = PartyBalancePayment::query()
            ->where('party_balance_id',$id)
            ->with('party:id,name,email,is_vendor,is_customer,profile_picture,contact_no','user:id,name')
            ->orderBy('id', 'DESC')
            ->get();
        return view('balancemanagement.party_balances.balance_payments', compact('payments'));
    }

    public function store(StorePartyBalancePaymentRequest $request, RecordPartyBalancePaymentAction $action)
    {
        try {
            $action->execute(
                $request->validated(),
                $request->file('cheque_picture'),
                $request->file('image_file'),
                $this->authUserId
            );

            return response()->json([
                'message' => 'Payment added successfully!',
                'success' => 'yes',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 201);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }

            return response()->json([
                'message' => 'Something went wrong',
                'success' => 'no',
            ], 200);
        }
    }
}
