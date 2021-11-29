<?php

namespace App\Http\Controllers\InventoryManagement;

use Session;
use DataTables;
use App\Models\Expense;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class ExpenseController extends Controller
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
        return view('inventorymanagement.expense.index');
    }

    public function getExpenseList()
    {
        $expenes = Expense::with('category:id,name')->orderBy('id','DESC')->get();
        return DataTables::of($expenes)
            ->addIndexColumn()
            ->addColumn('picture', function($row){
                $url= asset('storage/expenses/'.$row?->picture);
                return '<img class="rounded-circle avatar-lg" src="'.$url.'"  alt="No image" />';
            })->addColumn('category_id', function($row){
                return '<span>'.$row?->category?->name.'</span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewEmployeeModal"
                ExpenseId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openExpenseModal"
            ExpenseId="'.$row["id"].'" data-id="'.$row["id"].'" id="editEspenseModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("expense.destroy", $row["id"]).'"
                del_title="Expense: '.$row["id"].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['category_id','picture','Actions'])
            ->make(true);
    }

    public function getExpenseCategoryList()
    {
        $categories = ExpenseCategory::get(['id','name']);
        if($categories->count()  > 0){
            $success = 'yes';
            $data = $categories;
        }else{
            $success = 'no';
            $data = $categories;
        }
        return response()->json([
            'success' => $success,
            'categories' => $data,
        ], 201);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'amount' => 'bail|required|numeric',
            'expense_date' => 'bail|required|date',
            'category_id' => 'bail|required|integer',
            'remarks' => 'bail|required|string',
            'image_file' => 'mimes:jpeg,jpg,png|max:5000',
        ],[
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        if($request->expense_id_modal > 0){
            $exp_data = Expense::find($request->expense_id_modal);
        }else{
            $exp_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($exp_data?->picture != null && \Storage::disk('public')->exists('expenses/'.$exp_data?->picture)){
                \Storage::disk('public')->delete('expenses/'.$exp_data?->picture);
            }
            $path = 'expenses/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->expense_id_modal > 0){
            if($exp_data !=''){
                $message = 'Data Updated successfully!';
                $success = 'yes';
                if($exp_data->picture !='' && $imageName == null){
                    $imageName = $exp_data->picture;
                }
                $update_expense = $exp_data->update([
                    'category_id' => $request->category_id,
                    'expense_date' => $request->expense_date,
                    'amount' => $request->amount,
                    'remarks' => $request->remarks,
                    'picture' => $imageName,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No entry found against this id';
                $success = 'no';
            }
        }else{
            $sv_expense = Expense::create([
                'category_id' => $request->category_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'remarks' => $request->remarks,
                'picture' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);
            if($sv_expense){
                $message = 'New Expense created successfully!';
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
        $expense = Expense::find($id);
        if($expense){
            $html_data = \View::make('layouts._partial.customerdetail', compact('expense'))->render();
            $message = 'Expense Detail Data';
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

    public function edit($id)
    {
        $expense = Expense::with('category:id,name')->find($id);
        if($expense){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'expense' => $expense->toArray(),
            ], 201);
        }
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $img_path = 'expenses/'.$expense?->picture;
        if($expense?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $expense->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('expense.index');
    }
}