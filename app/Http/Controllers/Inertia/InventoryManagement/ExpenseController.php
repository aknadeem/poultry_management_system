<?php

namespace App\Http\Controllers\Inertia\InventoryManagement;

use App\Actions\InventoryManagement\DestroyExpenseAction;
use App\Actions\InventoryManagement\StoreExpenseAction;
use App\Actions\InventoryManagement\UpdateExpenseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inertia\InventoryManagement\StoreExpenseCategoryRequest;
use App\Http\Requests\Inertia\InventoryManagement\StoreExpenseRequest;
use App\Http\Requests\Inertia\InventoryManagement\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Queries\ExpenseQuery;
use App\Support\InventoryLookups;
use App\Support\InventoryPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(ExpenseQuery $query): Response
    {
        $this->authorize('viewAny', Expense::class);

        $expenses = $query->paginate();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses->through(fn (Expense $expense): array => InventoryPresenter::expense($expense)),
            'filters' => $query->filters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Expense::class);

        return Inertia::render('Expenses/Create', InventoryLookups::expenseOptions());
    }

    public function store(StoreExpenseRequest $request, StoreExpenseAction $action): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        $action->execute(
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.expenses.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'New Expense created successfully!',
            ]);
    }

    public function show(Expense $expense): Response
    {
        $this->authorize('view', $expense);

        return Inertia::render('Expenses/Show', [
            'expense' => InventoryPresenter::expense($expense),
        ]);
    }

    public function edit(Expense $expense): Response
    {
        $this->authorize('update', $expense);

        return Inertia::render('Expenses/Edit', array_merge(InventoryLookups::expenseOptions(), [
            'expense' => InventoryPresenter::expense($expense),
        ]));
    }

    public function update(
        UpdateExpenseRequest $request,
        Expense $expense,
        UpdateExpenseAction $action,
    ): RedirectResponse {
        $this->authorize('update', $expense);

        $action->execute(
            $expense,
            $request->validated(),
            $request->file('image_file'),
            (int) Auth::id(),
        );

        return redirect()
            ->route('inertia.expenses.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Data Updated successfully!',
            ]);
    }

    public function destroy(Expense $expense, DestroyExpenseAction $action): RedirectResponse
    {
        $this->authorize('delete', $expense);
        $action->execute($expense);

        return redirect()
            ->route('inertia.expenses.index')
            ->with('swal_notification', [
                'title' => 'Deleted',
                'icon_type' => 'success',
                'message' => 'Data Deleted Successfully!',
            ]);
    }

    public function storeCategory(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        $category = ExpenseCategory::create([
            'name' => $request->validated('cat_name'),
        ]);

        return redirect()
            ->back()
            ->with([
                'swal_notification' => [
                    'title' => 'Success',
                    'icon_type' => 'success',
                    'message' => 'New Category added successfully!',
                ],
                'created_lookup' => [
                    'table' => 'expense_categories',
                    'id' => $category->id,
                    'name' => $category->name,
                ],
            ]);
    }
}
