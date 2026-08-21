<?php

namespace App\Http\Controllers\Inertia\FarmManagement;

use App\Actions\FarmManagement\DestroyEmployeeAction;
use App\Actions\FarmManagement\StoreEmployeeAction;
use App\Actions\FarmManagement\UpdateEmployeeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\StoreEmployeeRequest;
use App\Http\Requests\FarmManagement\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Queries\EmployeeQuery;
use App\Support\FarmLookups;
use App\Support\FarmPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(EmployeeQuery $query): Response
    {
        $employees = $query->paginate();

        return Inertia::render('Employees/Index', [
            'employees' => $employees->through(fn (Employee $employee): array => FarmPresenter::employee($employee)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Employees/Create', FarmLookups::formOptions());
    }

    public function store(StoreEmployeeRequest $request, StoreEmployeeAction $action): RedirectResponse
    {
        $action->execute(
            $request->validated(),
            $request->file('employee_image'),
            $request->file('employee_signature'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.employees.index')
            ->with('swal_notification', [
                'title' => 'Created',
                'icon_type' => 'success',
                'message' => 'Data created successfully',
            ]);
    }

    public function show(Employee $employee): Response
    {
        return Inertia::render('Employees/Show', [
            'employee' => FarmPresenter::employee($employee),
        ]);
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Employees/Edit', array_merge(FarmLookups::formOptions(), [
            'employee' => FarmPresenter::employee($employee),
        ]));
    }

    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee,
        UpdateEmployeeAction $action,
    ): RedirectResponse {
        $action->execute(
            $employee,
            $request->validated(),
            $request->file('employee_image'),
            $request->file('employee_signature'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.employees.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data updated successfully',
            ]);
    }

    public function destroy(Employee $employee, DestroyEmployeeAction $action): RedirectResponse
    {
        $action->execute($employee);

        return redirect()
            ->route('inertia.employees.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }
}
