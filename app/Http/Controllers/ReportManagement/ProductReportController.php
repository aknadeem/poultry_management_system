<?php

namespace App\Http\Controllers\ReportManagement;

use Carbon\Carbon;
use App\Models\Product;
use App\Helpers\Constant;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use App\Models\ProductPurchase;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ProductReportController extends Controller
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
        return view('reportmanagement.products.index');
    }

    public function makeProductReport($from_date, $to_date)
    {
        $from_date = Carbon::parse($from_date); 
        $to_date = Carbon::parse($to_date); 

        $products = Product::whereBetween('created_at', [$from_date, $to_date])->with('company:id,company_name','category:id,name')->orderBy('id','DESC')->get(['id','product_name','product_group','product_code','party_company_id','product_category_id','quantity']);

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('DateFrom', function($row) use ($from_date){
                return '<span> '.$from_date->format('Y-m-d').'</span>';
            })
            ->addColumn('DateTo', function($row) use ($to_date){
                return '<span> '.$to_date->format('Y-m-d').'</span>';
            })
            ->addColumn('ProductGroup', function($row){
                return '<span> '. Constant::PRODUCT_GROUP_VAL[$row->product_group].'</span>';
            })
            ->addColumn('party_company_id', function($row){
                return '<span> '.$row?->company?->company_name.'</span>';
            })
            ->rawColumns(['DateFrom','DateTo','party_company_id','ProductGroup'])
            ->make(true);
    }

    public function purchase_report()
    {
        return view('reportmanagement.products.purchase_report');
    }
    
    public function makeProductPurchaseReport($from_date, $to_date)
    {
        // $from_date = Carbon::parse($from_date); 
        // $to_date = Carbon::parse($to_date); 

        $products = ProductPurchase::whereBetween('purchase_date', [$from_date, $to_date])->with('company:id,company_name,party_id')->orderBy('id','DESC')->get();

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('DateFrom', function($row) use ($from_date){
                return '<span> '.$from_date.'</span>';
            })
            ->addColumn('DateTo', function($row) use ($to_date){
                return '<span> '.$to_date.'</span>';
            })
            ->addColumn('party_company_id', function($row){
                return '<span> '.$row?->company?->company_name.'</span>';
            })
            ->rawColumns(['DateFrom','DateTo','party_company_id'])
            ->make(true);
    }

    public function sale_report()
    {
        //  $from_date = '2022-02-01'; 
        //  $to_date = '2022-02-06'; 
        // $products = ProductSale::whereBetween('sale_date', [$from_date, $to_date])->with('company:id,party_id,company_name', 'party:id,name,is_customer')->orderBy('id','DESC')->get();
        // dd($products->toArray());
        return view('reportmanagement.products.sale_report');
    }
    
    public function makeProductSaleReport($from_date, $to_date)
    {
        // $from_date = Carbon::parse($from_date); 
        // $to_date = Carbon::parse($to_date); 

        $products = ProductSale::whereBetween('sale_date', [$from_date, $to_date])->with('party:id,name,is_customer','company:id,company_name,party_id')->orderBy('id','DESC')->get();

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('DateFrom', function($row) use ($from_date){
                return '<span> '.$from_date.'</span>';
            })
            ->addColumn('DateTo', function($row) use ($to_date){
                return '<span> '.$to_date.'</span>';
            })
            ->addColumn('party_company_id', function($row){
                return '<span> '.$row?->company?->company_name.'</span>';
            })
            ->addColumn('Party', function($row){
                return '<span> '.$row?->party?->name.'</span>';
            })
            // ->rawColumns(['DateFrom','DateTo'])
            ->rawColumns(['DateFrom','DateTo','party_company_id','Party'])
            ->make(true);
    }
}
