<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyCompanyAction;
use App\Actions\PartyManagement\StorePartyCompanyAction;
use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyCompanyRequest;
use App\Models\PartyCompany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CompaniesController extends Controller
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
        $companies = PartyCompany::with('businesstype:id,name', 'vendor:id,name')->get();

        return view('partymanagement.company.index', compact('companies'));
    }

    public function store(StorePartyCompanyRequest $request, StorePartyCompanyAction $action)
    {
        try {
            $companyId = (int) $request->input('company_id_modal', 0);
            $action->execute(
                $request->validated(),
                $request->file('image_file'),
                $this->auth_user_id
            );

            return response()->json([
                'message' => $companyId > 0
                    ? 'A Company Data Updated successfully!'
                    : 'New Company Data created successfully!',
                'success' => 'yes',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 201);
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
        $data = PartyCompany::with('businesstype', 'vendor:id,name')->find($id);
        if ($data) {
            $html_data = \View::make('layouts._partial.companydetail', compact('data'))->render();

            return response()->json([
                'message' => 'Company Detail Data',
                'success' => 'yes',
                'html_data' => $html_data,
            ], 201);
        }

        return response()->json([
            'message' => 'No company detail found against this id',
            'success' => 'no',
            'html_data' => '',
        ], 201);
    }

    public function updateStatus($id, $tablename, UpdateActiveStatusAction $action)
    {
        try {
            $action->execute((int) $id, (string) $tablename, $this->auth_user_id);
            Session::flash('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Data Updated successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            Session::flash('swal_notification', [
                'title' => 'Warning',
                'icon_type' => 'warning',
                'message' => 'Data not updated, Something went wrong',
            ]);
        }

        return back();
    }

    public function getCompaniesList()
    {
        $companies = PartyCompany::get();

        return response()->json([
            'success' => $companies->count() > 0 ? 'yes' : 'no',
            'companies' => $companies,
        ], 201);
    }

    public function edit($id)
    {
        $company = PartyCompany::with('vendor:id,name,contact_no,email,description')->find($id);
        if ($company) {
            return response()->json([
                'message' => 'yes',
                'company' => [
                    'id' => $company->id,
                    'name' => $company->company_name,
                    'contact_no' => $company->vendor?->contact_no,
                    'email' => $company->vendor?->email,
                    'address' => $company->company_address,
                    'description' => $company->vendor?->description,
                    'company_logo' => $company->company_logo,
                ],
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyCompanyAction $action)
    {
        $company = PartyCompany::findOrFail($id);
        $action->execute($company);

        return redirect()->route('company.index');
    }
}
