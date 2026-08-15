<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use App\Models\Party;
use App\Models\Division;
use App\Models\PartyCompany;
use App\Models\ProductCategory;
use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Models\ProductSaleRebate;
use App\Models\ProductPurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\ProductManagement\StoreProductSaleAction;
use App\Actions\ProductManagement\UpdateProductSaleAction;
use App\Actions\ProductManagement\DestroyProductSaleAction;
use App\Actions\ProductManagement\RecordProductSaleRebateAction;
use App\Http\Requests\ProductManagement\StoreProductSaleRequest;
use App\Http\Requests\ProductManagement\UpdateProductSaleRequest;
use App\Http\Requests\ProductManagement\RecordProductSaleRebateRequest;

class ProductSaleController extends Controller
{
    private $authUserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index()
    {
        $this->authorize('viewAny', ProductSale::class);
        $product_sales = ProductSale::with(
            'party:id,name,cnic_no,customer_division_id',
            'party.division:id,name',
            'company:id,company_name',
            'productcategory:id,name'
        )->get();

        return view('productmanagement.sales.index', compact('product_sales'));
    }

    public function create()
    {
        $this->authorize('create', ProductSale::class);
        $pruchase = new ProductSale();
        $divisions = Division::get(['id', 'name', 'slug']);
        $customers = Party::where('is_customer', 1)->get(['id', 'is_customer', 'name', 'cnic_no', 'customer_division_id']);
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id', 'name', 'slug']);

        return view('productmanagement.sales.create', compact('pruchase', 'companies', 'categories', 'divisions', 'customers'));
    }

    public function store(StoreProductSaleRequest $request, StoreProductSaleAction $action)
    {
        $this->authorize('create', ProductSale::class);
        try {
            $action->execute($request->validated(), $request->file('invoice_picture'), $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('productsales.index');
    }

    public function show($id)
    {
        $sale = ProductSale::with(
            'party:id,name,cnic_no,customer_division_id',
            'party.division:id,name',
            'company:id,company_name',
            'productcategory:id,name',
            'detail'
        )->findOrFail($id);
        $this->authorize('view', $sale);

        $items = ProductSaleDetail::where('product_sale_id', $id)->get();

        return view('productmanagement.sales.sale_detail', compact('sale', 'items'));
    }

    public function getInvoice($id)
    {
        $sale = ProductSale::with(
            'party:id,name,cnic_no,customer_division_id',
            'party.division:id,name',
            'company:id,company_name',
            'productcategory:id,name',
            'detail'
        )->findOrFail($id);
        $this->authorize('view', $sale);

        $items = ProductSaleDetail::where('product_sale_id', $id)->get();

        return view('productmanagement.sales.sale_invoice', compact('sale', 'items'));
    }

    public function edit($id)
    {
        $pruchase = ProductSale::findOrFail($id);
        $this->authorize('update', $pruchase);
        $divisions = Division::get(['id', 'name', 'slug']);
        $customers = Party::where('is_customer', 1)->get(['id', 'is_customer', 'name', 'cnic_no', 'customer_division_id']);
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id', 'name', 'slug']);

        return view('productmanagement.sales.create', compact('pruchase', 'companies', 'categories', 'divisions', 'customers'));
    }

    public function update(UpdateProductSaleRequest $request, $id, UpdateProductSaleAction $action)
    {
        try {
            $sale = ProductSale::findOrFail($id);
            $this->authorize('update', $sale);
            $action->execute($sale, $request->validated(), $request->file('invoice_picture'), $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('productsales.index');
    }

    public function destroy($id, DestroyProductSaleAction $action)
    {
        try {
            $sale = ProductSale::findOrFail($id);
            $this->authorize('delete', $sale);
            $action->execute($sale, $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return redirect()->route('productsales.index');
    }

    public function forceDelete($id)
    {
        $sale = ProductSale::findOrFail($id);
        $this->authorize('delete', $sale);
        $sale->forceDelete();
        Session::flash('swal_notification', [
            'title' => 'Deleted',
            'icon_type' => 'success',
            'message' => 'Data Deleted Successfully!',
        ]);

        return redirect()->route('productsales.index');
    }

    public function getProductDetailItem($id, $type)
    {
        if ($type == 'ProductSaleDetail') {
            $item = ProductSaleDetail::find($id);
        } else {
            $item = ProductPurchaseDetail::find($id);
        }

        if ($item != '') {
            $success = 'yes';
            $data = $item->toArray();
        } else {
            $success = 'no';
            $data = [];
        }

        return response()->json([
            'success' => $success,
            'data' => $data,
        ]);
    }

    public function productRebate(RecordProductSaleRebateRequest $request, RecordProductSaleRebateAction $action)
    {
        $this->authorize('update', ProductSale::class);
        try {
            $action->execute($request->validated(), $this->authUserId);
            Session::flash('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            Session::flash('swal_notification', [
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'Something went wrong',
            ]);
        }

        return back();
    }

    public function getRebates()
    {
        $rebates = ProductSaleRebate::get();

        return view('productmanagement.sales.sale_rebates', compact('rebates'));
    }
}
