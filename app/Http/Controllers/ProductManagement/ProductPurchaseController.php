<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use Carbon\Carbon;
use App\Models\PartyCompany;
use App\Models\ProductCategory;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseRebate;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\ProductManagement\StoreProductPurchaseAction;
use App\Actions\ProductManagement\DestroyProductPurchaseAction;
use App\Http\Requests\ProductManagement\StoreProductPurchaseRequest;

class ProductPurchaseController extends Controller
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
        $product_purchases = ProductPurchase::with('company:id,party_id,company_name', 'productcategory:id,name')
            ->orderBy('id', 'DESC')
            ->get();

        return view('productmanagement.purchases.index', compact('product_purchases'));
    }

    public function create()
    {
        $pruchase = new ProductPurchase();
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id', 'name', 'slug']);

        return view('productmanagement.purchases.create', compact('pruchase', 'companies', 'categories'));
    }

    public function store(StoreProductPurchaseRequest $request, StoreProductPurchaseAction $action)
    {
        try {
            $action->execute($request->validated(), $this->authUserId);
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

        return redirect()->route('productpurchases.index');
    }

    public function getInvoice($id)
    {
        $purchase = ProductPurchase::with('detail', 'company:id,party_id,company_name', 'productcategory:id,name')
            ->orderBy('id', 'DESC')
            ->findOrFail($id);

        return view('productmanagement.purchases.purchase_invoice', compact('purchase'));
    }

    public function show($id)
    {
        $purchase = ProductPurchase::with('detail', 'company:id,party_id,company_name', 'productcategory:id,name')
            ->orderBy('id', 'DESC')
            ->findOrFail($id);

        return view('productmanagement.purchases.purchase_detail', compact('purchase'));
    }

    public function edit($id)
    {
        $pruchase = ProductPurchase::findOrFail($id);
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $categories = ProductCategory::where('is_active', 1)->get(['id', 'name', 'slug']);

        return view('productmanagement.purchases.create', compact('pruchase', 'companies', 'categories'));
    }

    public function update($id)
    {
        Session::flash('swal_notification', [
            'title' => 'Info',
            'icon_type' => 'info',
            'message' => 'Product purchase update is not available. Delete and recreate if needed.',
        ]);

        return redirect()->route('productpurchases.index');
    }

    public function destroy($id, DestroyProductPurchaseAction $action)
    {
        try {
            $purchase = ProductPurchase::findOrFail($id);
            $action->execute($purchase, $this->authUserId);
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

        return redirect()->route('productpurchases.index');
    }

    public function getRebates()
    {
        $rebates = ProductPurchaseRebate::get();

        return view('productmanagement.purchases.purchase_rebates', compact('rebates'));
    }
}
