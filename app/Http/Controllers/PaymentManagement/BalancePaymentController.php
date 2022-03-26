<?php

namespace App\Http\Controllers\PartyManagement;

use DataTables;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CompanyBalance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class BalancePaymentController extends Controller
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
        return view('partymanagement.company.balance.index');
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
        return view('partymanagement.company.balancepayments.index');
    }

    public function getCompaniesBalanceList()
    {
        $balances = CompanyBalance::with('company:id,company_name,company_address')->orderBy('id','DESC')->withCasts([
            'created_at' => 'date:d M, Y'
        ])->get();
        return DataTables::of($balances)
            ->addIndexColumn()
            ->addColumn('company_id', function($row){
                return '<span> '.$row?->company?->company_name.' </span>';
            })->addColumn('type', function($row){
                return ucfirst(str_replace('_', ' ', $row?->type));
            })
            ->addColumn('Action', function($row){
                return '<a class="btn btn-success btn-bold btn-sm openAddPaymentModal"
                FeedId="'.$row["id"].'" data-id="'.$row["id"].'" href="javascript:void(0);" title="Click to add payment"><i
                    class="fa fa-plus"></i>
                Payment
                </a>
                <a class="btn btn-primary btn-sm"
                FeedId="'.$row["id"].'" href="'.route("companybalance.show", $row["id"]).'"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
                </a>';
            })
            ->rawColumns(['company_id', 'type','Action'])
            ->make(true);
    }

    public function getBalanceWithCompany($id)
    {
        $balance = CompanyBalance::with('productpurchase')->find($id);
        if($balance){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'balance' => $balance->toArray(),
            ], 201);
        }
    }

    public function edit($id)
    {
        $company = Company::find($id);
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
        $company = Company::findOrFail($id);
        $img_path = 'companies/'.$company?->company_logo;
        if($company?->company_logo != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $company->delete();
        return redirect()->route('company.index');
    }
}