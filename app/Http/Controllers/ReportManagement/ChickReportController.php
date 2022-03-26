<?php

namespace App\Http\Controllers\ReportManagement;

use Carbon\Carbon;
use App\Models\Party;
use App\Models\ChickenSale;
use Illuminate\Http\Request;
use App\Models\ChickPurchase;
use App\Models\ChickenPurchase;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ChickReportController extends Controller
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
        $customers = Party::where('is_customer', 1)->get(['id','name','is_customer','cnic_no','contact_no']);
        return view('reportmanagement.chicks.sale.index', compact('customers'));
    }

    public function makeSalesReport($from_date, $to_date)
    {
        // $from_date = Carbon::parse($from_date)->format('y-m-d'); 
        // $to_date = Carbon::parse($to_date); 

        // 2022-02-06
        $from_date = $from_date; 
        $to_date = $to_date; 

        $chicken_sales = ChickenSale::whereBetween('sale_date', [$from_date, $to_date])->orderBy('id','DESC')->with('customer:id,name,cnic_no,contact_no,is_customer')->get();
        return DataTables::of($chicken_sales)
            ->addIndexColumn()
            ->addColumn('DateFrom', function($row) use ($from_date){
                return '<span> '.$from_date.'</span>';
            })->addColumn('DateTo', function($row) use ($to_date){
                return '<span> '.$to_date.'</span>';
            })
            ->addColumn('Customer', function($row){
                return '<span> '.$row?->customer?->name.' <br> <b> '.$row?->customer?->cnic_no.'</b> </span>';
            })
            ->rawColumns(['DateFrom','DateTo','Customer'])
            ->make(true);
    }

    public function purchase_index()
    {
        $customers = Party::where('is_customer', 1)->get(['id','name','is_customer','cnic_no','contact_no']);
        return view('reportmanagement.chicks.purchase.index', compact('customers'));
    }

    public function makePurchaseReport($from_date, $to_date)
    {
        $chicken_purchases = ChickPurchase::whereBetween('purchase_date', [$from_date, $to_date])->orderBy('id','DESC')->with('company:id,company_name')->get();
        return DataTables::of($chicken_purchases)
            ->addIndexColumn()
            ->addColumn('DateFrom', function($row) use ($from_date){
                return '<span> '.$from_date.'</span>';
            })
            ->addColumn('DateTo', function($row) use ($to_date){
                return '<span> '.$to_date.'</span>';
            })
            ->addColumn('company_id', function($row){
                return '<span> '.$row?->company?->company_name.' </span>';
            })
            ->rawColumns(['DateFrom','DateTo','company_id'])
            ->make(true);
    }
}
