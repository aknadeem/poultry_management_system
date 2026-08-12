<?php

namespace App\Http\Controllers\PartyManagement;

use DataTables;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CompanyBalancePayment;
use App\Actions\PartyManagement\RecordCompanyBalancePaymentAction;
use App\Http\Requests\PartyManagement\StoreCompanyBalancePaymentRequest;

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

    public function store(StoreCompanyBalancePaymentRequest $request, RecordCompanyBalancePaymentAction $action)
    {
        try {
            $action->execute(
                $request->validated(),
                $request->file('cheque_picture'),
                $request->file('image_file'),
                $this->auth_user_id
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

    public function show($id)
    {   
        $balance_payments = CompanyBalancePayment::where('company_balance_id', $id)->with('company:id,company_name,company_logo','addedBy:id,name,user_role_id')->get();
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

