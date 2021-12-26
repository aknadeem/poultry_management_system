<?php


namespace App\Http\Controllers\PartyManagement;

use App\Models\Company;
use App\Models\PartyCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class CompaniesController extends Controller
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
        // dd($this->auth_user_id);
        $companies = PartyCompany::with('businesstype:id,name','vendor:id,name')->get();
        return view('partymanagement.company.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'farm_name' => 'bail|string',
            'address' => 'bail|required|string',
            'image_file' => 'nullable',
            'description' => 'nullable|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        
        if($request->company_id_modal > 0){
            $company_data = Company::find($request->company_id_modal);
        }else{
            $company_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($company_data?->company_logo != null && \Storage::disk('public')->exists('companies/'.$company_data?->company_logo)){
                \Storage::disk('public')->delete('companies/'.$company_data?->company_logo);
            }
            $path = 'companies/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->company_id_modal > 0){
            if($company_data !=''){
                $message = 'A Company Data Updated successfully!';
                $success = 'yes';
                if($company_data->image !='' && $imageName == null){
                    $imageName = $company_data->image;
                }
                $update_company = $company_data->update([
                    'name' => $request->name,
                    'contact_no' => $request->contact_no,
                    'email' => $request->email,
                    'address' => $request->address,
                    'company_logo' => $imageName,
                    'description' => $request->description,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No Company detail found against this id';
                $success = 'no';
            }
        }else{
            $company = Company::create([
                'name' => $request->name,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'address' => $request->address,
                'company_logo' => $imageName,
                'description' => $request->description,
                'addedby' => $this->auth_user_id,
            ]);
            if($company){
                $message = 'New Company Data created successfully!';
                $success = 'yes';
            }else{
                $message = 'Something went wrong';
                $success = 'no';
            }
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }

    public function show($id)
    {
        $data = PartyCompany::with('businesstype', 'vendor:id,name')->find($id);
        if($data){
            $html_data = \View::make('layouts._partial.companydetail', compact('data'))->render();
            $message = 'Company Detail Data';
            $success = 'yes';
        }else{
            $message = 'No company detail found against this id';
            $success = 'no';
            $html_data = '';
        }
        return response()->json([
            'message' => $message,
            'success' => $success,
            'html_data' => $html_data,
        ], 201);

        // return response()->json($data, 200, $headers);
    }

    public function updateStatus($id, $tablename)
    {

        if ($tablename !='' && Schema::hasTable($tablename) ) {
            $company_data = DB::table($tablename)->where('id',$id)->first();

            if ($company_data->is_active == 0) {
                $status = 1;
            }else{
                $status = 0;
            }

            $company = DB::table($tablename)->where('id',$id)->update(['is_active' => $status, 'updatedby' => $this->auth_user_id]);
            if($company){
                $message = 'Data Updated successfully!';
                $success = 'yes';
                $icon_type = 'success';
            }else{
                $message = 'Data not updated, Something went wrong';
                $success = 'no';
                $icon_type = 'warning';
            }
        }else{
            $message = 'Data not updated, Something went wrong';
            $success = 'no';
            $icon_type = 'warning';
        }
        Session::flash('swal_notification', ['title' => $message, 'icon_type' => $icon_type, 'message' => $message]);
        return back();
    }

    public function getCompaniesList()
    {
        $companies = PartyCompany::get();
        if($companies->count()  > 0){
            $success = 'yes';
            $data = $companies;
        }else{
            $success = 'no';
            $data = $companies;
        }
        return response()->json([
            'success' => $success,
            'companies' => $data,
        ], 201);
    }

    public function edit($id)
    {
        $company = PartyCompany::find($id);
        if($company){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'company' => $company->toArray(),
            ], 201);
        }
    }
    
    public function destroy($id)
    {
        $company = PartyCompany::findOrFail($id);
        $img_path = 'party/company/'.$company?->company_logo;
        if($company?->company_logo != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $company->delete();
        return redirect()->route('company.index');
    }
}
