<?php

namespace App\Http\Controllers\ChickenModule;

use Session;
use DataTables;
use App\Models\Party;
use App\Models\ChickGrade;
use App\Models\PartyCompany;
use App\Models\ChickenPurchase;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\ChickenModule\StoreChickenPurchaseAction;
use App\Actions\ChickenModule\UpdateChickenPurchaseAction;
use App\Actions\ChickenModule\DestroyChickenPurchaseAction;
use App\Http\Requests\ChickenModule\StoreChickenPurchaseRequest;
use App\Http\Requests\ChickenModule\UpdateChickenPurchaseRequest;

class ChickenPurchaseController extends Controller
{
    private $auth_user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id = \Auth::user()->id;
            return $next($request);
        });
    }

    public function index()
    {
        return view('chickens.purchase.index');
    }

    public function getPurchaseList()
    {
        $chicken_purchases = ChickenPurchase::orderBy('id', 'DESC')
            ->with('company:id,company_name')
            ->get();

        return DataTables::of($chicken_purchases)
            ->addIndexColumn()
            ->addColumn('picture', function ($row) {
                $url = asset('storage/chicks/' . $row?->picture);
                return '<img class="rounded-circle avatar-lg" src="' . $url . '"  alt="No image" />';
            })
            ->addColumn('company_id', function ($row) {
                return '<span> ' . $row?->company?->company_name . ' </span>';
            })
            ->addColumn('Actions', function ($row) {
                return '<a class="btn btn-secondary btn-sm"
                PurchaseId="' . $row["id"] . '" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm" href="' . route("chickenpurchase.edit", $row["id"]) . '"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="' . route("chickenpurchase.destroy", $row["id"]) . '"
                del_title="Chicken Purchase" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['picture', 'company_id', 'Actions'])
            ->make(true);
    }

    public function create()
    {
        $purchase = new ChickenPurchase();
        $chick_grades = ChickGrade::get();
        $compaines = PartyCompany::where('is_active', 1)
            ->with('vendor:id,name,guardian_name')
            ->get(['id', 'party_id', 'company_name', 'company_address']);
        $customers = Party::where([['is_active', 1], ['is_customer', 1]])
            ->whereHas('farm')
            ->with('farm:id,party_id,farm_name,farm_code,farm_capacity')
            ->get(['id', 'is_customer', 'name', 'cnic_no', 'contact_no']);

        return view('chickens.purchase.create', compact('purchase', 'compaines', 'chick_grades', 'customers'));
    }

    public function store(StoreChickenPurchaseRequest $request, StoreChickenPurchaseAction $action)
    {
        try {
            $imageFile = $request->hasFile('image_file') ? $request->file('image_file') : null;
            $action->execute($request->validated(), $imageFile, $this->auth_user_id);

            Session::flash('swal_notification', [
                'title' => 'Saved',
                'icon_type' => 'success',
                'message' => 'Data created successfully!',
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

        return redirect()->route('chickenpurchase.index');
    }

    public function edit($id)
    {
        $purchase = ChickenPurchase::with('company:id,company_name,party_id', 'company.vendor:id,name,guardian_name')->findOrFail($id);
        $chick_grades = ChickGrade::get();
        $compaines = PartyCompany::where('is_active', 1)
            ->with('vendor:id,name,guardian_name')
            ->get(['id', 'party_id', 'company_name', 'company_address']);

        return view('chickens.purchase.create', compact('purchase', 'compaines', 'chick_grades'));
    }

    public function update(UpdateChickenPurchaseRequest $request, $id, UpdateChickenPurchaseAction $action)
    {
        try {
            $purchase = ChickenPurchase::findOrFail($id);
            $imageFile = $request->hasFile('image_file') ? $request->file('image_file') : null;
            $action->execute($purchase, $request->validated(), $imageFile, $this->auth_user_id);

            Session::flash('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data Updated Successfully!',
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

        return redirect()->route('chickenpurchase.index');
    }

    public function destroy($id, DestroyChickenPurchaseAction $action)
    {
        try {
            $purchase = ChickenPurchase::findOrFail($id);
            $action->execute($purchase);

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

        return redirect()->route('chickenpurchase.index');
    }
}
