<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use DataTables;
use App\Models\Country;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\PartyCompany;
use App\Models\PersonalFarm;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use App\Models\EmployeeLevel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class ProductStoreController extends Controller
{
    private $authUserId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId= \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        $stores = ProductStore::get();
        return view('productmanagement.stores.index', compact('stores'));
    }

    public function getStoreList()
    {
        $stores = ProductStore::orderBy('id','DESC')->get();
        return DataTables::of($stores)
            ->addIndexColumn()
            ->addColumn('is_active', function($row){
                $is_checked = ($row?->is_active == 1) ? 'checked' : '';
                return '<a href="'.route("updatestatus", ["id" => $row->id, "tag" => "product_stores"]).'"
                title="Click to update Status" class="confirm-status">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="switch1"
                    '.$is_checked.'>
                    <label class="form-check-label" for="switch1"></label>
                </div>
            </a>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewDetailModal"
                StoreId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm OpenAddStoreModal"
                StoreId="'.$row['id'].'" data-id="'.$row['id'].'" id="OpenAddStoreModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("productstores.destroy", $row["id"]).'"
                del_title="Store '.$row["store_name"].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['is_active','Actions'])
            ->make(true);
    }

    public function create()
    {
        $store = new ProductStore();
        $companies = PartyCompany::get();
        return view('productmanagement.create', compact('store','companies'));
    }

    public function store(Request $request)
    {
        try {
            $storeId = (int) ($request->input('store_id') ?? 0);

            $this->validationRules($request, $storeId);

            if ($storeId > 0) {
                $store = ProductStore::find($storeId);

                if (! $store) {
                    return response()->json([
                        'message' => 'No entry found against this id',
                        'success' => 'no',
                    ], 200);
                }

                $store->update([
                    'store_name' => $request->store_name,
                    'store_type' => $request->store_type,
                    'store_area' => $request->store_area,
                    'total_racks' => $request->total_racks,
                    'description' => $request->store_desciption,
                    'updatedby' => $this->authUserId,
                ]);

                $message = 'Data updated successfully';
            } else {
                $store = ProductStore::create([
                    'store_name' => $request->store_name,
                    'store_type' => $request->store_type,
                    'store_area' => $request->store_area,
                    'total_racks' => $request->total_racks,
                    'description' => $request->store_desciption,
                    'addedby' => $this->authUserId,
                ]);

                $message = 'Data created successfully';
            }

            return response()->json([
                'message' => $message,
                'success' => 'yes',
                'data' => $store->toArray(),
            ], 200);

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
        $result = ProductStore::find($id);
        if($result){
            $html_data = \View::make('layouts._partial.detailModal', compact('result'))->render();
            $message = 'Detail Data';
            $success = 'yes';
        }else{
            $message = 'No detail found against this id';
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
        $store = ProductStore::find($id);

        if (! $store) {
            return response()->json(['message' => 'no'], 201);
        }

        return response()->json([
            'message' => 'yes',
            'store' => $store->toArray(),
        ], 201);
    }

    public function validationRules($request, $id)
    {
        $validator = Validator::make($request->all(),[
        // $this->validate($request, [
            'store_name' => 'bail|required|string',
            'store_type' => 'bail|required|string',
            'total_racks' => 'bail|required|integer',
            'store_area' => 'bail|required|numeric',
            'store_desciption' => 'bail|nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $store = ProductStore::findOrFail($id);
        $store->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        
        return redirect()->route('productstores.index');
    }
    
    public function forceDelete($id)
    {
        $store = ProductStore::findOrFail($id);
        $store->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('productstores.index');
    }
}