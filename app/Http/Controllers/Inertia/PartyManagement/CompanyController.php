<?php

namespace App\Http\Controllers\Inertia\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyCompanyAction;
use App\Actions\PartyManagement\StorePartyCompanyAction;
use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Actions\PartyManagement\UpdatePartyCompanyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyCompanyRequest;
use App\Models\BusinessType;
use App\Models\PartyCompany;
use App\Queries\CompanyQuery;
use App\Support\CompanyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(CompanyQuery $query): Response
    {
        $companies = $query->paginate();

        return Inertia::render('Companies/Index', [
            'companies' => $companies->through(fn (PartyCompany $company): array => CompanyPresenter::listItem($company)),
            'filters'   => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Companies/Create', [
            'businessTypes' => BusinessType::query()->get(['id', 'name']),
        ]);
    }

    public function store(StorePartyCompanyRequest $request, StorePartyCompanyAction $action): RedirectResponse
    {
        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title'     => 'Created',
                'icon_type' => 'success',
                'message'   => 'Company created successfully',
            ]);
    }

    public function show(PartyCompany $company): Response
    {
        $company->load('businesstype:id,name', 'vendor:id,name,contact_no,email,description');

        return Inertia::render('Companies/Show', [
            'company' => CompanyPresenter::form($company),
        ]);
    }

    public function edit(PartyCompany $company): Response
    {
        $company->load('businesstype:id,name', 'vendor:id,name,contact_no,email,description');

        return Inertia::render('Companies/Edit', [
            'company'       => CompanyPresenter::form($company),
            'businessTypes' => BusinessType::query()->get(['id', 'name']),
        ]);
    }

    public function update(
        Request $request,
        PartyCompany $company,
        UpdatePartyCompanyAction $action,
    ): RedirectResponse {
        $request->validate([
            'name'       => 'required|string',
            'contact_no' => 'required|numeric',
            'email'      => 'required|string',
            'address'    => 'required|string',
            'image_file' => 'nullable|mimes:jpeg,jpg,png|max:5000',
            'description' => 'nullable|string',
        ]);

        $action->execute(
            $company,
            $request->all(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title'     => 'Updated',
                'icon_type' => 'success',
                'message'   => 'Company updated successfully',
            ]);
    }

    public function destroy(PartyCompany $company, DestroyPartyCompanyAction $action): RedirectResponse
    {
        $action->execute($company);

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title'     => 'Deleted',
                'icon_type' => 'success',
                'message'   => 'Company deleted successfully',
            ]);
    }

    public function toggleStatus(PartyCompany $company, UpdateActiveStatusAction $action): RedirectResponse
    {
        $action->execute($company->id, 'party_companies', (int) Auth::id());

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title'     => 'Updated',
                'icon_type' => 'success',
                'message'   => 'Status updated successfully',
            ]);
    }
}
