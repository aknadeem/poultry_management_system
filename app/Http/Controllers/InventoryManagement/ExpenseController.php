<?php

namespace App\Http\Controllers\InventoryManagement;

use Session;
use DataTables;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\InventoryManagement\StoreExpenseAction;
use App\Actions\InventoryManagement\UpdateExpenseAction;
use App\Actions\InventoryManagement\DestroyExpenseAction;
use App\Http\Requests\InventoryManagement\StoreExpenseRequest;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    private $authUserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index()
    {
        return view('inventorymanagement.expense.index');
    }

    public function getExpenseList()
    {
        $expenes = Expense::with('category:id,name')->orderBy('id', 'DESC')->get();

        return DataTables::of($expenes)
            ->addIndexColumn()
            ->addColumn('picture', function ($row) {
                $url = asset('storage/expenses/'.$row?->picture);

                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })->addColumn('category_id', function ($row) {
                return '<span>'.$row?->category?->name.'</span>';
            })
            ->addColumn('Actions', function ($row) {
                return '
            <a class="btn btn-info btn-sm openExpenseModal d-none"
            ExpenseId="'.$row['id'].'" data-id="'.$row['id'].'" id="editEspenseModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm d-none"
                href="'.route('expense.destroy', $row['id']).'"
                del_title="Expense: '.$row['id'].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['category_id', 'picture', 'Actions'])
            ->make(true);
    }

    public function getExpenseCategoryList()
    {
        $categories = ExpenseCategory::get(['id', 'name']);
        if ($categories->count() > 0) {
            $success = 'yes';
            $data = $categories;
        } else {
            $success = 'no';
            $data = $categories;
        }

        return response()->json([
            'success' => $success,
            'categories' => $data,
        ], 201);
    }

    public function store(
        StoreExpenseRequest $request,
        StoreExpenseAction $storeAction,
        UpdateExpenseAction $updateAction
    ) {
        try {
            $expenseId = (int) ($request->input('expense_id_modal') ?? 0);
            $imageFile = $request->hasFile('image_file') ? $request->file('image_file') : null;

            if ($expenseId > 0) {
                $expense = Expense::find($expenseId);
                if (! $expense) {
                    return response()->json([
                        'message' => 'No entry found against this id',
                        'success' => 'no',
                    ], 200);
                }

                $updateAction->execute($expense, $request->validated(), $imageFile, $this->authUserId);
                $message = 'Data Updated successfully!';
            } else {
                $storeAction->execute($request->validated(), $imageFile, $this->authUserId);
                $message = 'New Expense created successfully!';
            }

            return response()->json([
                'message' => $message,
                'success' => 'yes',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

    public function storeExpenseCategrory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_name' => 'bail|required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        $sv_expense_cat = ExpenseCategory::create([
            'name' => $request->cat_name,
        ]);
        if ($sv_expense_cat) {
            $message = 'New Category added successfully!';
            $success = 'yes';
        } else {
            $message = 'Something went wrong';
            $success = 'no';
        }

        return response()->json([
            'message' => $message,
            'success' => $success,
        ], 200);
    }

    public function show($id)
    {
        $expense = Expense::find($id);
        if ($expense) {
            $html_data = \View::make('layouts._partial.customerdetail', compact('expense'))->render();
            $message = 'Expense Detail Data';
            $success = 'yes';
        } else {
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

    public function edit($id)
    {
        $expense = Expense::with('category:id,name')->find($id);
        if ($expense) {
            $message = 'yes';

            return response()->json([
                'message' => $message,
                'expense' => $expense->toArray(),
            ], 201);
        }
    }

    public function destroy($id, DestroyExpenseAction $action)
    {
        try {
            $expense = Expense::findOrFail($id);
            $action->execute($expense);
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

        return redirect()->route('expense.index');
    }
}
