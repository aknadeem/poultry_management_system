<?php

namespace App\Http\Controllers\PaymentManagement;

use DataTables;
use Illuminate\Http\Request;
use App\Models\AccountPayable;
use App\Http\Controllers\Controller;

class AccountPayableController extends Controller
{
    public function index()
    {
        $payables = AccountPayable::orderBy('id','DESC')->get();
        return view('paymentmanagement.accountpayable.index', compact('payables'));
    }

    public function getCompaniesBalanceList()
    {
        $balances = AccountPayable::orderBy('id','DESC')->withCasts([
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
                return '<a class="btn btn-success btn-bold btn-sm openAddPaymentModal"
                data-id="'.$row["id"].'" href="javascript:void(0);" title="Click to add payment"><i
                    class="fa fa-plus"></i>
                Payment
                </a>
                <a class="btn btn-primary btn-sm"
                FeedId="'.$row["id"].'" href="'.route("companybalance.show", $row["id"]).'"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
                </a>';
            })
            ->rawColumns(['company_id', 'type','Action'])
            ->make(true);
    }
}
