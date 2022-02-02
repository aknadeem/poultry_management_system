<?php

namespace App\Http\Controllers\ProductManagement;

use Session;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\PartyFarm;
use App\Models\PartyCompany;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\VaccinationGroup;
use App\Models\VaccinationSchedule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VaccinationController extends Controller
{
    private $auth_user_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
    }
    // public function getVaccinationScheduleFarm()
    // {
        
    // }

    public function index()
    {
        return view('farmmanagement.vaccination.index');
    }

    public function getScheduleList()
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

    public function create()
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

    public function edit($id)
    {
    }

    public function store(Request $request)
    {
        $curent_date = today()->format('Y-m-d');
        $validator = Validator::make($request->all(),[
        // $this->validate($request, [
            'farm_id' => 'bail|nullable|integer',
            'product_id' => 'bail|nullable|integer',
            'schedule_date' => 'bail|required|date|after_or_equal:'.$curent_date,
            'desciption' => 'bail|nullable',
        ],[
            'schedule_date.after_or_equal' => 'The date must be after or equal to Current date: '.$curent_date
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }

        $message = 'Vaccination schedule added successfully!';
        $title = 'Success';
        $icon_type = 'success';
        try {
            $product = VaccinationSchedule::create([
                'party_farm_id' => $request->farm_id,
                'product_id' => $request->product_id,
                'schedule_date' => $request->schedule_date,
                'description' => $request->description,
                'addedby' => $this->auth_user_id,
            ]);
        }
        catch (\Throwable $e) {
            return $e;
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
    
    public function addVaccination(Request $request)
    {
        $vacc_schedule= VaccinationSchedule::find($request->schedule_id);

        if($vacc_schedule){
            $schedule_date = $vacc_schedule?->schedule_date?->format('Y-m-d');
            $validator = Validator::make($request->all(),[
            // $this->validate($request, [
                'farm_id' => 'bail|nullable|integer',
                'product_id' => 'bail|nullable|integer',
                'vaccination_date' => 'bail|required|date|after_or_equal:'.$schedule_date,
                'remarks' => 'bail|nullable',
            ],[
                'vaccination_date.after_or_equal' => 'The date must be after or equal to Schedule date: '.$schedule_date
            ]);
            if($validator->fails()){
                return response()->json([
                    'error' => $validator->errors()->toArray(),
                    'success' => 'no',
                ], 201);
            }
    
            $message = 'Vaccine has been  added successfully!';
            $title = 'Success';
            $icon_type = 'success';
            try {
                $vacc_schedule->update([
                    'is_vaccinated' => 1,
                    'vaccination_date' => $request->vaccination_date,
                    'vaccinated_remarks' => $request->remarks,
                    'updatedby' => $this->auth_user_id,
                ]);
            }
            catch (\Throwable $e) {
                return $e;
                $message = 'Something went wrong';
                $title = 'Error';
                $icon_type = 'warning';
            }
        }else{
            $message = 'No Data Found';
            $title = 'Error';
            $icon_type = 'warning';
        }

        return response()->json([
            'title' => $title,
            'icon_type' => $icon_type,
            'message' => $message,
        ]);
    }

    public function update(Request $request, $id)
    {  
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
        $curent_date = today()->format('Y-m-d');
        $validator = Validator::make($request->all(),[
        // $this->validate($request, [
            'farm_id' => 'bail|nullable|integer',
            'product_id' => 'bail|nullable|integer',
            'schedule_date' => 'bail|required|date|after:tomorrow',
            'desciption' => 'bail|nullable',
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
        $product = Product::findOrFail($id);
        $product->delete();

        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        
        return redirect()->route('products.index');
    }
    
    public function forceDelete($id)
    {
        $product = Product::findOrFail($id);
        $product->forceDelete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('products.index');
    }
}

