<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\PartyCompany;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\VaccinationGroup;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
        // $num = 10000200;
        // if($num >= 99999)
        //     $length = 6;
        // else if($num >= 999999)
        //     $length = 7;
        // else if($num >= 9999999)
        //     $length = 8;
        // else
        //     $length = 5;

        // $gn = str_pad($num, $length, 0, STR_PAD_LEFT)+1;
        // dd(str_pad($gn, $length, 0, STR_PAD_LEFT));
        $products = Product::with('company:id,company_name','category:id,name')->get(['id','product_name','product_group','product_code','bar_code','party_company_id','product_category_id','quantity','purchase_date','is_active']);

        return view('productmanagement.index', compact('products'));
    }

    public function create()
    {
        $product = new Product();
        $companies = PartyCompany::where('is_active', 1)->get(['id','company_name','company_code']);
        $product_stores = ProductStore::get(['id','store_name','store_code','store_area','total_racks']);
        $vaccination_groups = VaccinationGroup::get(['id','name','slug']);
        $product_categories = ProductCategory::get(['id','name','slug']);

        return view('productmanagement.create', compact('product','companies', 'product_stores', 'vaccination_groups', 'product_categories'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $companies = PartyCompany::where('is_active', 1)->get(['id','company_name','company_code']);
        $product_stores = ProductStore::get(['id','store_name','store_code','store_area','total_racks']);
        $vaccination_groups = VaccinationGroup::get(['id','name','slug']);
        $product_categories = ProductCategory::get(['id','name','slug']);

        return view('productmanagement.create', compact('product','companies', 'product_stores', 'vaccination_groups', 'product_categories'));
    }

    public function store(Request $request)
    {
        $id = null;
        $this->validationRules($request, $id);

        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {
            if ($request->hasFile('product_picture')) {
                $product_image_file = $request->file('product_picture');
                $extension = $request->file('product_picture')->extension();
                $product_picture = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $product_picture = null;
            }

            if ($request->has('warranty_period')) {
                $currentDateTime = Carbon::now();
                $reorder_level_date = Carbon::now()->addDays(5)->format('Y-m-d');
            }else{
                $reorder_level_date = null;
            }
        
            $product = Product::create([
                'party_company_id' => $request->company_id,
                'product_category_id' => $request->product_category_id,
                'product_group' => $request->product_group,
                'product_name' => $request->product_name,
                'batch_number' => $request->batch_number,
                'serial_number' => $request->serial_number,
                'product_type' => $request->product_type,
                'vaccination_group_id' => $request->vaccination_group,
                'pack_size' => $request->pack_size_unit,
                'pack_size_unit_type' => $request->pack_size_unit_type,
                'product_store_id' => $request->store_id,
                'rack_number' => $request->rack_number,
                'min_inventory_level' => $request->min_level,
                'max_inventory_level' => $request->max_level,
                'reorder_level_period' => $request->warranty_period,
                'reorder_level_date' => $reorder_level_date,
                'mrp_price' => $request->mrp_price,

                'whole_sale_price' => $request->whole_sale_price,
                'full_less_price' => $request->full_less_price,
                'store_price' => $request->store_price,
                'trade_price' => $request->trade_price,
                'retail_price' => $request->retail_price,
                'purchase_price' => $request->purchase_price,
                'sale_price' => $request->sale_price,
                'discount_amount' => $request->discount_amount,

                'tax_percentage' => $request->tax_percentage,
                'tax_amount' => $request->tax_amount,
                'discount_percentage' => $request->discount_percentage,
                'warranty_period' => $request->warranty_period,

                'is_taxable' => $request->is_taxable,
                'is_sale_on_tp' => $request->is_sale_on_tp,
                'is_claimable' => $request->is_claimable,
                'is_fridged' => $request->is_fridged,
                'is_narcotic' => $request->is_narcotic,
                'is_unwarranted' => $request->is_unwaranted,
                'description' => $request->description,
                'product_picture' => $product_picture,
                'addedby' => $this->auth_user_id,
            ]);

            if($product){
                if($product_picture != null){
                    $upload_to_folder = $product_image_file->storeAs('products/', $product_picture, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            Log::error($e);
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('products.index');
    }

    public function companyAndCategoryFilter($party_id, $category_id)
    {
        $products = Product::where('is_active',1)->where([['party_company_id','=', $party_id],['product_category_id','=',$category_id]])->get(['id','product_name','product_code', 'product_type','total_quantity','quantity','remaining_quantity','purchase_price','sale_price', 'discount_amount','tax_amount','max_inventory_level','discount_percentage','tax_percentage','warranty_period']);

        if($products !=''){
            return response()->json([
                'success' => 'yes',
                'data' => $products->toArray(),
            ]);
        }else{
            return response()->json([
                'success' => 'no',
                'data' => [],
            ]);
        }
    }
    
    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);

        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {

            $product_data = Product::findOrFail($id);

            if ($request->hasFile('product_picture')) {
                if($product_data?->product_picture != null && \Storage::disk('public')->exists('products/'.$product_data?->product_picture)){
                    \Storage::disk('public')->delete('products/'.$product_data?->product_picture);
                }
                $product_picture_file = $request->file('product_picture');
                $extension = $request->file('product_picture')->extension();
                $product_picture = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $product_picture = null;
            }

            if($product_data->employee_image !='' && $employee_image == null){
                $employee_image = $product_data->employee_image;
            }


            $product_data->update([
                'party_company_id' => $request->company_id,
                'product_category_id' => $request->product_category_id,
                'product_group' => $request->product_group,
                'product_name' => $request->product_name,
                'batch_number' => $request->batch_number,
                'serial_number' => $request->serial_number,
                'product_type' => $request->product_type,
                'vaccination_group_id' => $request->vaccination_group,
                'pack_size' => $request->pack_size_unit,
                'pack_size_unit_type' => $request->pack_size_unit_type,
                'product_store_id' => $request->store_id,
                'rack_number' => $request->rack_number,
                'min_inventory_level' => $request->min_level,
                'max_inventory_level' => $request->max_level,
                'mrp_price' => $request->mrp_price,
                'purchase_price' => $request->purchase_price,
                'sale_price' => $request->sale_price,

                'whole_sale_price' => $request->whole_sale_price,
                'full_less_price' => $request->full_less_price,
                'store_price' => $request->store_price,
                'trade_price' => $request->trade_price,
                'retail_price' => $request->retail_price,
                'discount_amount' => $request->discount_amount,

                'tax_percentage' => $request->tax_percentage,
                'tax_amount' => $request->tax_amount,
                'discount_percentage' => $request->discount_percentage,
                'warranty_period' => $request->warranty_period,
                'is_taxable' => $request->is_taxable,
                'is_sale_on_tp' => $request->is_sale_on_tp,
                'is_claimable' => $request->is_claimable,
                'is_fridged' => $request->is_fridged,
                'is_narcotic' => $request->is_narcotic,
                'is_unwarranted' => $request->is_unwaranted,
                'description' => $request->description,
                // 'cnic_no' => 'bail|required|numeric|unique:employees,cnic_no,'.$id,
                'product_picture' => $product_picture,
                'updatedby' => $this->auth_user_id,
            ]);

            if($product_data){
                if($request->hasFile('product_picture') && $product_picture != null){
                    $upload_to_folder = $product_picture_file->storeAs('products/', $product_picture, 'public');
                }
            }
        }
        catch (\Throwable $e) {
            return $e;
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('employee.index');
    }

    public function show($id)
    {
        $result = Product::with('company:id,company_name,company_code','productstore:id,store_name')->find($id);
        if($result){
            $html_data = \View::make('layouts._partial.productdetail', compact('result'))->render();
            $message = 'Item Detail Data';
            $success = 'yes';
        }else{
            $message = 'No data found against this id';
            $success = 'no';
            $html_data = '';
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);
    }


    public function validationRules($request, $id)
    {
        // $validator = Validator::make($request->all(),[
        $this->validate($request, [
            'company_id' => 'bail|nullable|integer',
            'product_category_id' => 'bail|nullable|integer',
            'product_name' => 'bail|required|string',
            'batch_number' => 'bail|required|string',
            'serial_number' => 'bail|required|string',
            'product_type' => 'bail|required|string',
            'vaccination_group' => 'bail|required|string',
            'pack_size_unit' => 'bail|required|string',
            'pack_size_unit_type' => 'bail|required|string',
            'store_id' => 'bail|required|integer',
            'rack_number' => 'bail|nullable|numeric',
            'min_level' => 'bail|nullable|numeric',
            'max_level' => 'bail|nullable|numeric',
            'mrp_price' => 'bail|nullable|numeric',
            'purchase_price' => 'bail|required|numeric',
            'sale_price' => 'bail|required|numeric',

            'whole_sale_price' => 'bail|nullable|numeric',
            'full_less_price' => 'bail|nullable|numeric',
            'store_price' => 'bail|nullable|numeric',
            'retail_price' => 'bail|nullable|numeric',
            'trade_price' => 'bail|nullable|numeric',
            'discount_amount' => 'bail|nullable|numeric',

            'tax_percentage' => 'bail|nullable|numeric',
            'tax_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'warranty_period' => 'bail|nullable|numeric',

            'is_taxable' => 'bail|nullable|boolean',
            'is_sale_on_tp' => 'bail|nullable|boolean',
            'is_claimable' => 'bail|nullable|boolean',
            'is_fridged' => 'bail|nullable|boolean',
            'is_narcotic' => 'bail|nullable|boolean',
            'is_unwaranted' => 'bail|nullable|boolean',
            'description' => 'bail|nullable',
            // 'cnic_no' => 'bail|required|numeric|unique:employees,cnic_no,'.$id,
            'product_picture' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',

        ]);

        // if($validator->fails()){
        //     return response()->json([
        //         'error' => $validator->errors()->toArray(),
        //         'success' => 'no',
        //     ], 201);
        // }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        
        return redirect()->route('products.index');
    }
    
    public function forceDelete($id)
    {
        $product = Product::findOrFail($id);
        $img_path = 'products/'.$product?->product_picture;
        if($product?->product_picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $product->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('products.index');
    }
}
