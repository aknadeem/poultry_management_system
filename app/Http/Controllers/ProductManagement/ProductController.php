<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use App\Models\Product;
use App\Models\PartyCompany;
use App\Models\ProductStore;
use App\Models\ProductCategory;
use App\Models\VaccinationGroup;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Actions\ProductManagement\StoreProductAction;
use App\Actions\ProductManagement\UpdateProductAction;
use App\Actions\ProductManagement\DestroyProductAction;
use App\Http\Requests\ProductManagement\StoreProductRequest;
use App\Http\Requests\ProductManagement\UpdateProductRequest;

class ProductController extends Controller
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
        $products = Product::with('company:id,company_name', 'category:id,name')->get([
            'id',
            'product_name',
            'product_group',
            'product_code',
            'bar_code',
            'party_company_id',
            'product_category_id',
            'quantity',
            'purchase_date',
            'is_active',
        ]);

        return view('productmanagement.index', compact('products'));
    }

    public function create()
    {
        $product = new Product();
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $product_stores = ProductStore::get(['id', 'store_name', 'store_code', 'store_area', 'total_racks']);
        $vaccination_groups = VaccinationGroup::get(['id', 'name', 'slug']);
        $product_categories = ProductCategory::get(['id', 'name', 'slug']);

        return view('productmanagement.create', compact(
            'product',
            'companies',
            'product_stores',
            'vaccination_groups',
            'product_categories'
        ));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $companies = PartyCompany::where('is_active', 1)->get(['id', 'company_name', 'company_code']);
        $product_stores = ProductStore::get(['id', 'store_name', 'store_code', 'store_area', 'total_racks']);
        $vaccination_groups = VaccinationGroup::get(['id', 'name', 'slug']);
        $product_categories = ProductCategory::get(['id', 'name', 'slug']);

        return view('productmanagement.create', compact(
            'product',
            'companies',
            'product_stores',
            'vaccination_groups',
            'product_categories'
        ));
    }

    public function store(StoreProductRequest $request, StoreProductAction $action)
    {
        $message = 'Data created successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute(
                $request->validated(),
                $request->file('product_picture'),
                $this->authUserId
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', [
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);

        return redirect()->route('products.index');
    }

    public function update(UpdateProductRequest $request, $id, UpdateProductAction $action)
    {
        $message = 'Data updated successfully';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $product = Product::findOrFail($id);
            $action->execute(
                $product,
                $request->validated(),
                $request->file('product_picture'),
                $this->authUserId
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        Session::flash('swal_notification', [
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);

        return redirect()->route('products.index');
    }

    public function companyAndCategoryFilter($party_id, $category_id)
    {
        $products = Product::where('is_active', 1)
            ->where([
                ['party_company_id', '=', $party_id],
                ['product_category_id', '=', $category_id],
            ])
            ->get([
                'id',
                'product_name',
                'product_code',
                'product_type',
                'total_quantity',
                'quantity',
                'remaining_quantity',
                'purchase_price',
                'sale_price',
                'discount_amount',
                'tax_amount',
                'max_inventory_level',
                'discount_percentage',
                'tax_percentage',
                'warranty_period',
            ]);

        if ($products != '') {
            return response()->json([
                'success' => 'yes',
                'data' => $products->toArray(),
            ]);
        }

        return response()->json([
            'success' => 'no',
            'data' => [],
        ]);
    }

    public function show($id)
    {
        $result = Product::with(
            'company:id,company_name,company_code',
            'productstore:id,store_name'
        )->find($id);

        if ($result) {
            $html_data = \View::make('layouts._partial.productdetail', compact('result'))->render();
            $message = 'Item Detail Data';
            $success = 'yes';
        } else {
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

    public function destroy($id, DestroyProductAction $action)
    {
        try {
            $product = Product::findOrFail($id);
            $action->execute($product);
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

        return redirect()->route('products.index');
    }

    public function forceDelete($id, FileUploadService $uploadService)
    {
        try {
            $product = Product::findOrFail($id);
            $uploadService->delete('products', $product->product_picture);
            $product->forceDelete();
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

        return redirect()->route('products.index');
    }
}
