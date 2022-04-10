<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use DataTables;
use Carbon\Carbon;
use App\Models\Party;
use App\Models\Product;
use App\Models\Division;
use App\Helpers\Constant;
use App\Models\ProductSale;
use Illuminate\Support\Arr;
use App\Models\PartyBalance;
use App\Models\PartyCompany;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductPurchase;
use App\Models\ProductSaleDetail;
use App\Models\ProductSaleRebate;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
use App\Models\Validators\ProductSaleValidator;

class ProductSaleController extends Controller
{
    private $auth_user_id;
    private $today_is;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
        $this->today_is = Carbon::now();
    }

    public function index()
    {
        $product_sales = ProductSale::with('party:id,name,cnic_no,customer_division_id','party.division:id,name','company:id,company_name','productcategory:id,name')->get();
        return view('productmanagement.sales.index', compact('product_sales'));
    }

    public function create()
    {
        $pruchase = new ProductSale();
        $divisions = Division::get(['id','name','slug']);
        // $customers = Party::where('is_customer', 1)->get();
        $customers = Party::where('is_customer', 1)->get(['id','is_customer','name','cnic_no','customer_division_id']);
        $companies = PartyCompany::where('is_active', 1)->get(['id','company_name','company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id','name','slug']);
        return view('productmanagement.sales.create', compact('pruchase','companies', 'categories','divisions','customers'));
    }

    public function store(Request $request)
    {
        $data = (new ProductSaleValidator())->validate($request->toArray());
        $data['addedby'] = $this->auth_user_id; 

        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $saved = DB::transaction(function () use ($data) {
                $sale = ProductSale::create(
                    Arr::except($data, 
                    [
                        'product_id','product_code', 'product_name','invoice_picture',
                        'product_sale_price','product_qty','product_bonus_qty','product_total_qty',
                        'product_discount','product_discount_percentage','product_total_price'
                    ])
                );
                // $sale = ProductSale::create([
                //     'division_id' => $data['division_id'],
                //     'party_id' => $data['party_id'],
                //     'product_category_id' => $data['product_category_id'],
                //     'party_company_id' => $data['party_company_id'],
                //     'sale_date' => $data['sale_date'],
                //     'due_date_option' => $data['due_date_option'],
                //     'manual_number' => $data['manual_number'],
                //     'sale_type' => $data['sale_type'],
                //     'total_amount' => $data['total_amount'],
                //     'discount_amount' => $data['discount_amount'],
                //     'discount_percentage' => 0,
                //     'other_charges' => $data['other_charges'],
                //     'final_amount' => $data['final_amount'],
                //     'invoice_picture' => 'testing',
                //     'description' => $data['description'],
                //     'addedby' => $data['addedby'],
                // ]);

                $number = count($data['product_name']);  
                if($number > 0)  
                {  
                    for($i=0; $i<$number; $i++)  
                    {
                        $saleDetail = ProductSaleDetail::create([
                            'product_sale_id' => $sale->id,
                            'product_id' => $data['product_id'][$i],
                            'product_code' => $data['product_code'][$i],
                            'product_name' => $data['product_name'][$i],

                            'product_sale_price' => $data['product_sale_price'][$i],
                            'product_qty' => $data['product_qty'][$i],
                            'product_bonus_qty' => $data['product_bonus_qty'][$i],

                            'product_total_qty' => $data['product_total_qty'][$i],
                            'product_discount' => $data['product_discount'][$i],
                            'product_discount_percentage' => $data['product_discount_percentage'][$i],

                            'product_total_price' => $data['product_total_price'][$i],
                            'addedby' => $data['addedby'],
                        ]);

                        $product_update = Product::where('id', $data['product_id'][$i])->first(['id','quantity','updatedby','updated_at','created_at']);

                        if($product_update != ''){
                            $ex_qty = $product_update->quantity;
                            $new_qty = $ex_qty - $data['product_total_qty'][$i];
                            $product_update->update([
                                'quantity' => $new_qty,
                                'updatedby' => $data['addedby'],
                            ]);
                        }    
                    }
                }
                
                if($sale){
                    $partyBalance = PartyBalance::create([
                        'party_id' => $data['party_id'],
                        'total_amount' => $data['final_amount'],
                        'remaining_amount' => $data['final_amount'],
                        'transaction_date' => $data['sale_date'],
                        'amount_type' => Constant::AMOUNT_TYPE['ToReceive'],
                        'narration' => 'sale product balance',
                        'addedby' => $data['addedby'],
                    ]);
                }
            });
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('productsales.index');
    }

    public function show($id)
    {   
        $sale = ProductSale::with('party:id,name,cnic_no,customer_division_id','party.division:id,name','company:id,company_name','productcategory:id,name','detail')->findOrFail($id);

        $items = ProductSaleDetail::where('product_sale_id', $id)->get();

        return view('productmanagement.sales.sale_detail', compact('sale','items'));
    }
    public function getInvoice($id)
    {   
        $sale = ProductSale::with('party:id,name,cnic_no,customer_division_id','party.division:id,name','company:id,company_name','productcategory:id,name','detail')->findOrFail($id);

        $items = ProductSaleDetail::where('product_sale_id', $id)->get();

        return view('productmanagement.sales.sale_invoice', compact('sale','items'));
    }
    
    public function destroy($id)
    {
        $purchase = ProductSale::findOrFail($id);
        $purchase->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('productpurchases.index');
    }
    
    public function forceDelete($id)
    {
        $purchase = ProductSale::findOrFail($id);
        // $img_path = 'products/purchases/'.$purchase?->purchase_invoice;
        // if($purchase?->purchase_invoice != null && \Storage::disk('public')->exists($img_path)){
        //     \Storage::disk('public')->delete($img_path);
        // }
        $purchase->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('productpurchases.index');
    }

    public function getProductDetailItem($id, $type)
    {
        if($type == 'ProductSaleDetail'){
            $item = ProductSaleDetail::find($id);
        }else{
            $item = ProductPurchaseDetail::find($id);
        }
        
        if($item != ''){
            $success = 'yes';
            $data = $item->toArray();
        }else{
            $success = 'no';
            $data = [];
        }
        
        return response()->json([
            'success' => $success,
            'data' => $data,
        ]);
    }

    public function productRebate(Request $request)
    {
        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';

        try{
            $rebates = DB::transaction(function () use ($request) {
                $product_detail_id = $request->product_detail_id;
                $from_page = $request->from_page;
                if($from_page == 'ProductSaleDetail'){
                    $item = ProductSaleDetail::find($product_detail_id);
                    $item_price = $item->product_sale_price;
                    $SalePurchase = ProductSale::find($item->product_sale_id);
                    $RebateModal = new ProductSaleRebate();
                }else{
                    $item = ProductPurchaseDetail::find($product_detail_id);
                    $item_price = $item->product_purchase_price;
                    $SalePurchase = ProductPurchase::find($item->product_purchase_id);
                    $RebateModal = new ProductPurchaseRebate();
                }
        
                $detail_id = $request->product_detail_id;
                $rebateQty = $request->rebate_qty;
                $rebateReason = $request->rebate_reason;
        
                $updateQty = $item->product_total_qty - $rebateQty;
                $rebateAmount = $item_price * $rebateQty;
                $updatePrice = $item->product_total_price - $rebateAmount;
        
                if($item != ''){
                    $item->product_total_qty = $updateQty;                    $item->product_total_price = $updatePrice;
                    $item->is_rebate = 1;
                    $item->rebate_qty = $rebateQty;
                    $item->updated_at = $this->today_is;
                    $item->updatedby = $this->auth_user_id;
                    $item->save();
                }
                if($SalePurchase != ''){
        
                    $final_amount = $SalePurchase->final_amount;
                    $updateFinalPrice = $final_amount - $rebateAmount;
        
                    $SalePurchase->final_amount = $updateFinalPrice;
                    $SalePurchase->is_rebate = 1;
                    $SalePurchase->rebate_amount = $rebateAmount;
                    $SalePurchase->updated_at = $this->today_is;
                    $SalePurchase->updatedby = $this->auth_user_id;
                    $SalePurchase->save();
                }
        
                $RebateModal->create([
                    'rebate_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'rebate_reason' => $rebateReason,
                    'rebate_qty' => $rebateQty,
                    'rebate_description' => $request->rebate_description,
                    'addedby' => $this->auth_user_id,
                ]);
            });
        }catch (\Throwable $e) {
            return $e;
            // Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return back();
    }
}
