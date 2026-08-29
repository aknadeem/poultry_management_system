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
        $this->authorize('viewAny', PartyCompany::class);

        $companies = $query->paginate();

        return Inertia::render('Companies/Index', [
            'companies' => $companies->through(fn (PartyCompany $company): array => CompanyPresenter::listItem($company)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', PartyCompany::class);

        return Inertia::render('Companies/Create', [
            'businessTypes' => BusinessType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StorePartyCompanyRequest $request, StorePartyCompanyAction $action): RedirectResponse
    {
        $this->authorize('create', PartyCompany::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Company created successfully',
            ]);
    }

    public function show(PartyCompany $company): Response
    {
        $this->authorize('view', $company);

        $company->load('businesstype:id,name', 'vendor:id,name,contact_no,email,description');

        return Inertia::render('Companies/Show', [
            'company' => CompanyPresenter::form($company),
        ]);
    }

    public function edit(PartyCompany $company): Response
    {
        $this->authorize('update', $company);

        $company->load('businesstype:id,name', 'vendor:id,name,contact_no,email,description');

        return Inertia::render('Companies/Edit', [
            'company' => CompanyPresenter::form($company),
            'businessTypes' => BusinessType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(
        Request $request,
        PartyCompany $company,
        UpdatePartyCompanyAction $action,
    ): RedirectResponse {
        $this->authorize('update', $company);

        $data = $request->validate([
            'name' => 'required|string',
            'contact_no' => 'required|numeric',
            'email' => 'required|string',
            'address' => 'required|string',
            'business_type_id' => 'nullable|integer|exists:business_types,id',
            'image_file' => 'nullable|mimes:jpeg,jpg,png|max:5000',
            'description' => 'nullable|string',
        ]);

        $action->execute(
            $company,
            $data,
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Company updated successfully',
            ]);
    }

    public function destroy(PartyCompany $company, DestroyPartyCompanyAction $action): RedirectResponse
    {
        $this->authorize('delete', $company);

        $action->execute($company);

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Company deleted successfully',
            ]);
    }

    public function toggleStatus(PartyCompany $company, UpdateActiveStatusAction $action): RedirectResponse
    {
        $this->authorize('update', $company);

        $action->execute($company->id, 'party_companies', (int) Auth::id());

        return redirect()
            ->route('inertia.companies.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
