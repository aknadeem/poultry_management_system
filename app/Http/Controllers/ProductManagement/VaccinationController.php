<?php

namespace App\Http\Controllers\ProductManagement;

use App\Actions\FarmManagement\RecordVaccinationAction;
use App\Actions\FarmManagement\StoreVaccinationScheduleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\RecordVaccinationRequest;
use App\Http\Requests\FarmManagement\StoreVaccinationScheduleRequest;
use App\Models\PartyFarm;
use App\Models\Product;
use App\Models\VaccinationSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class VaccinationController extends Controller
{
    private int $authUserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('farmmanagement.vaccination.index');
    }

    public function getScheduleList(): JsonResponse
    {
        $stores = VaccinationSchedule::with('farm:id,farm_name','product:id,product_code,product_name')->orderBy('id','DESC')->withCasts([
            'schedule_date' => 'date:d M, Y',
            'vaccination_date' => 'date:d M, Y',
            ])->get();
        return DataTables::of($stores)
            ->addIndexColumn()
            ->addColumn('is_active', function($row){
                $is_checked = ($row?->is_active == 1) ? 'checked' : '';
                $b_color = ($row?->is_active == 1) ? 'success' : 'danger';
                return '<a href="'.route("updatestatus", ["id" => $row->id, "tag" => "vaccination_schedules"]).'"
                title="Click to update Status" class="confirm-status">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input bg-'.$b_color.' border-'.$b_color.'" id="switch1"
                    '.$is_checked.'>
                    <label class="form-check-label" for="switch1"></label>
                </div>
            </a>';
            })->addColumn('is_vaccinated', function($row){
                $is_checked = ($row?->is_vaccinated == 1) ? 'Yes' : 'Not Vaccinated';
                $color = ($row?->is_vaccinated == 1) ? 'success' : 'danger';
                $v_date = ($row?->is_vaccinated == 1) ? 'At: '.$row?->vaccination_date?->format('d M, Y') : '';
                return '<span class="fw-bold fs-5 text-'.$color.'">'.$is_checked.'<br>'.$v_date.'</span>';
            })->addColumn('party_farm_id', function($row){
                return '<span class="fs-5">'.$row?->farm?->farm_name.'</span>';
            })->addColumn('product_id', function($row){
                return '<span class="fs-5"><b>Code: </b>'.$row?->product?->product_code.'<br> <b>Name:</b> '.$row?->product?->product_name.'</span>';
            })
            ->addColumn('Actions', function($row){
                return '
            <a class="btn btn-info btn-md OpenAddVaccinationModal"
                href="javascript:void(0);" FarmName="'.$row?->farm?->farm_name.'" ProductCode="'.$row?->product?->product_code.'" ProductName="'.$row?->product?->product_name.'" ScheduleId="'.$row["id"].'"
                title="Click to edit"><i
                    class="fas fa-syringe"></i>
                Add Vaccination
            </a>';
            })
            ->rawColumns(['party_farm_id','product_id','is_vaccinated','is_active','Actions'])
            ->make(true);
    }

    public function create(): JsonResponse
    {
        $products = Product::get(['id','product_code','product_name']);
        $farms = PartyFarm::get(['id','party_id','farm_name']);

        if($products->count() > 0 && $farms->count() > 0){
            return response()->json([
                'success' => 'yes',
                'products' => $products->toArray(),
                'farms' => $farms->toArray(),
            ]);
        }else{
            return response()->json([
                'success' => 'no',
                'products' => [],
                'farms' => [],
            ]);
        }
    }

    public function edit(int $id): void
    {
    }

    public function store(
        StoreVaccinationScheduleRequest $request,
        StoreVaccinationScheduleAction $action,
    ): JsonResponse {
        $message = 'Vaccination schedule added successfully!';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute($request->validated(), $this->authUserId);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        return response()->json([
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);
    }

    public function addVaccination(
        RecordVaccinationRequest $request,
        RecordVaccinationAction $action,
    ): JsonResponse {
        $schedule = VaccinationSchedule::query()->find($request->integer('schedule_id'));

        if (! $schedule) {
            return response()->json([
                'title' => 'Error',
                'icon_type' => 'warning',
                'message' => 'No Data Found',
            ]);
        }

        $message = 'Vaccine has been  added successfully!';
        $title = 'Success';
        $icon_type = 'success';

        try {
            $action->execute($schedule, $request->validated(), $this->authUserId);
        } catch (\Throwable $e) {
            Log::error($e);
            if (app()->environment('testing')) {
                throw $e;
            }
            $message = 'Something went wrong';
            $title = 'Error';
            $icon_type = 'warning';
        }

        return response()->json([
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);
    }

    public function update(Request $request, int $id): void
    {
    }

    public function show(int $id): JsonResponse
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
    public function destroy(int $id): RedirectResponse
    {
        $schedule = VaccinationSchedule::findOrFail($id);
        $schedule->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Vaccination schedule deleted successfully!']);
        
        return redirect()->route('vaccination.index');
    }
    
    public function forceDelete(int $id): RedirectResponse
    {
        $schedule = VaccinationSchedule::findOrFail($id);
        $schedule->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Vaccination schedule deleted successfully!']);
        return redirect()->route('vaccination.index');
    }
}

