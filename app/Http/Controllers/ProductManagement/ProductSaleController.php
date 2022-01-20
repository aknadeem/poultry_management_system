<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use DataTables;
use Carbon\Carbon;
use App\Models\Party;
use App\Models\Country;
use App\Models\Product;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\PartyCompany;
use App\Models\PersonalFarm;
use Illuminate\Http\Request;
use App\Models\EmployeeLevel;
use App\Models\ProductCategory;
use App\Models\ProductPurchase;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

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
        $product_sales = collect();
        return view('productmanagement.sales.index', compact('product_sales'));
    }

    public function create()
    {
        $pruchase = new ProductPurchase();
        $divisions = Division::get(['id','name','slug']);
        // $customers = Party::where('is_customer', 1)->get();
        $customers = Party::where('is_customer', 1)->get(['id','is_customer','name','cnic_no','customer_division_id']);
        // dd($customers->toArray());
        $companies = PartyCompany::where('is_active', 1)->get(['id','company_name','company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id','name','slug']);
        return view('productmanagement.sales.create', compact('pruchase','companies', 'categories','divisions','customers'));
    }

    public function getEmployeeList()
    {
        $employees = Employee::orderBy('id','DESC')->get();
        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('employee_image', function($row){
                $url= asset('storage/employee/'.$row?->employee_image);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                EmployeeId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm"
                href="'.route("employee.edit", $row["id"]).'"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("employee.destroy", $row["id"]).'"
                del_title="Employee" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['employee_image','Actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        dd($request->toArray());
        $id = null;
        $this->validationRules($request, $id);

        // dd($request->toArray());
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        $product = Product::find($request->product_id);
        if($product !=''){
            if($request->purchase_price > $product->purchase_price ||  $request->discount_amount > $product->discount_amount){
                $message = 'Please make sure purchase and discount amount should be less then Product';
                $title = 'Error';
                $icon_type = 'danger';
            }else{
                $quantity = (int)$request->quantity;
                $purchase_price = (float)$request->purchase_price;
                $total_price = $quantity*$purchase_price;
                $discount_amount = (float)$request->discount_amount;
                $final_price =  $total_price;

                if($discount_amount < 1 || $discount_amount >= $total_price){
                    $discount_amount = 0;
                    $discount_percentage = 0;
                }else{
                    $discount_percentage = ($discount_amount / $total_price) * 100;
                    $final_price = $total_price - $discount_amount;
                }
                
                $tax_amount = (float)$request->tax_amount;
                if($tax_amount > 0){
                    $final_price = $final_price + $tax_amount;
                }

                try {
                    DB::transaction(function () use ($request, $product, $total_price, $discount_amount,$discount_percentage,$final_price, $tax_amount) {
                        if ($request->hasFile('invoice_picture')) {
                            $invoice_picture_file = $request->file('invoice_picture');
                            $extension = $request->file('invoice_picture')->extension();
                            $invoice_picture = time().mt_rand(10,99).'.'.$extension;
                            
                        }else{
                            $invoice_picture = null;
                        }
                        $save_purchase = ProductPurchase::create([
                            'party_company_id' => $request->company_id,
                            'product_id' => $request->product_id,
                            'purchase_date' => $request->purchase_date,
                            'expiry_date' => $request->expiry_date,
                            'quantity' => $request->quantity,
                            'bonus_quantity' => $request->bonus_quantity,
                            'total_quantity' => $request->quantity + $request->bonus_quantity,
                            'purchase_price' => $request->purchase_price,
                            'total_price' => $total_price,
                            'discount_amount' => $discount_amount,
                            'discount_percentage' => $discount_percentage,
                            'tax_amount' => $tax_amount,
                            'tax_percentage' => $request->tax_percentage,
                            'final_price' => $final_price,
                            'warranty_period' => $request->warranty_period,
                            'description' => $request->description,
                            'purchase_invoice' => $invoice_picture,
                            'addedby' => $this->auth_user_id,
                        ]);
                        if($save_purchase){
                            $upload_to_folder = $invoice_picture_file->storeAs('products/purchases/', $invoice_picture, 'public');
                            $newQty = $request->quantity + $request->bonus_quantity;
                            $exqty = $product->quantity;
                            $product_update = $product->update([
                                'quantity' => $exqty + $newQty,
                                'purchase_date' => $request->purchase_date,
                                'updatedby' => $this->auth_user_id,
                            ]);

                            $companyBalanceId = DB::table('company_balances')->insertGetId([
                                'type' => 'product_purchase',
                                'company_id' => $request->company_id,
                                'model_id' => $save_purchase->id,
                                'total_amount' => $request->final_price,
                                'remaining_amount' => $request->final_price,
                                'dr' => $request->final_price,
                                'created_at' => $this->today_is,
                                'addedby' => $this->auth_user_id,
                            ]);

                            $ac_payable = DB::table('account_payables')->insert([
                                'amount_type' => 'product_purchase',
                                'narration' => 'company balance on product purchase',
                                'amount_status' => 'unpaid',
                                'entry_date' => $this->today_is,
                                'model_id' => $companyBalanceId,
                                'total_amount' => $request->final_price,
                                'remaining_amount' => $request->final_price,
                                'dr' => $request->final_price,
                                'created_at' => $this->today_is,
                                'addedby' => $this->auth_user_id,
                            ]);
                        }
                    });
                }
                catch (\Throwable $e) {
                    return $e;
                    $message = 'Something went wrong';
                    $title = 'Error';
                    $icon_type = 'warning';
                }
            }
        }else{
            $message = 'No Product found, please Choose Product id carefully';
            $title = 'danger';
            $icon_type = 'danger';
        }

        Session::flash('swal_notification', ['title' => $title, 'icon_type' => $icon_type, 'message' => $message]);
        return redirect()->route('productpurchases.index');
    }
    
    public function update(Request $request, $id)
    {
        $this->validationRules($request, $id);

        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';
        try {

            $Employee_data = Employee::findOrFail($id);

            if ($request->hasFile('employee_image')) {
                if($Employee_data?->employee_image != null && \Storage::disk('public')->exists('employee/'.$Employee_data?->employee_image)){
                    \Storage::disk('public')->delete('employee/'.$Employee_data?->employee_image);
                }
                $employee_image_file = $request->file('employee_image');
                $extension = $request->file('employee_image')->extension();
                $employee_image = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_image = null;
            }

            if ($request->hasFile('employee_signature')) {
                if($Employee_data?->employee_signature != null && \Storage::disk('public')->exists('employee/'.$Employee_data?->employee_signature)){
                    \Storage::disk('public')->delete('employee/'.$Employee_data?->employee_signature);
                }
                $employee_signature_file = $request->file('employee_signature');
                $extension = $request->file('employee_signature')->extension();
                $employee_signature = time().mt_rand(10,99).'.'.$extension;
                
            }else{
                $employee_signature = null;
            }

            if($Employee_data->employee_image !='' && $employee_image == null){
                $employee_image = $Employee_data->employee_image;
            }
            
            if($Employee_data->employee_signature !='' && $employee_image == null){
                $employee_image = $Employee_data->employee_signature;
            }


            $Employee_data->update([
                'personal_farm_id' => $request->personal_farm_id,
                'employee_type_id' => $request->employee_type_id,
                'employee_level_id' => $request->employee_level_id,
                'name' =>  $request->name,
                'guardian_name' => $request->guardian_name,
                'contact_no' => $request->contact_no,
                'other_number' => $request->other_number,
                'other_number' => $request->other_number,
                'email' => $request->email,
                'cnic_no' => $request->cnic_no,
                'basic_salary' => $request->basic_salary,
                'other_amount' => $request->other_amount,
                'net_salary' => $request->net_salary,
                'contract_period' => $request->contract_period,
                'date_of_birth' => $request->date_of_birth,
                'joining_date' => $request->joining_date,
                'is_police_record' => $request->is_police_record,
                'address' => $request->address,
                'description' => $request->description,
                'blood_group' => $request->blood_group,
                'country_id' => $request->country_id,
                'province_id' => $request->province_id,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,

                'employee_image' => $employee_image,
                'employee_signature' => $employee_signature,
                'updatedby' => $this->auth_user_id,
            ]);

            if($Employee_data){
                if($request->hasFile('employee_image') && $employee_image != null){
                    $upload_to_folder = $employee_image_file->storeAs('employee/', $employee_image, 'public');
                }
                if($request->hasFile('employee_signature') && $employee_signature != null){
                    $upload_to_folder_sig = $employee_signature_file->storeAs('employee/', $employee_signature, 'public');
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
        $customer = Employee::find($id);
        if($customer){
            $html_data = \View::make('layouts._partial.customerdetail', compact('customer'))->render();
            $message = 'Employee Detail Data';
            $success = 'yes';
        }else{
            $message = 'No employee detail found against this id';
            $success = 'no';
            $html_data = '';
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);

        return response()->json($data, 200, $headers);
    }

    public function edit($id)
    {
        $pruchase = ProductPurchase::findOrFail($id);
        $companies = PartyCompany::where('is_active', 1)->get(['id','company_name','company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id','name','slug']);
        return view('productmanagement.purchases.create', compact('pruchase','companies', 'categories'));
    }

    public function validationRules($request, $id)
    {
        // $validator = Validator::make($request->all(),[
        $this->validate($request, [
            'company_id' => 'bail|required|integer',
            'product_category_id' => 'bail|required|integer',
            'product_id' => 'bail|required|integer',
            'product_code' => 'bail|required|string',
            'product_price' => 'bail|required|string',
            'purchase_date' => 'bail|required|date',
            'expiry_date' => 'bail|required|date',
            'quantity' => 'bail|required|numeric',
            'bonus_quantity' => 'bail|nullable|numeric',
            'purchase_price' => 'bail|required|numeric',
            'total_price' => 'bail|nullable|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'tax_amount' => 'bail|nullable|numeric',
            'tax_percentage' => 'bail|nullable|numeric',
            'final_price' => 'bail|nullable|numeric',
            'warranty_period' => 'bail|nullable|numeric',
            'description' => 'bail|nullable',
            'invoice_picture' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
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
        $purchase = ProductPurchase::findOrFail($id);
        $purchase->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('productpurchases.index');
    }
    
    public function forceDelete($id)
    {
        $purchase = ProductPurchase::findOrFail($id);
        $img_path = 'products/purchases/'.$purchase?->purchase_invoice;
        if($purchase?->purchase_invoice != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $purchase->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('productpurchases.index');
    }
}
