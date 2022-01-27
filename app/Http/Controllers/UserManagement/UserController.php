<?php

namespace App\Http\Controllers\UserManagement;

use Session;
use DataTables;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $auth_user_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
    }

    public function getUsersList()
    {
        $users = User::with('userlevel:id,name,slug')->orderBy('id','DESC')->get();
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user_level_id', function($row){
                return '<span>'.$row?->userlevel?->name.'</span>';
            })
            ->addColumn('Actions', function($row){
                return ' <a class="btn btn-secondary btn-sm ViewUserModal"
                UserId="'.$row["id"].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openUserModal"
                UserId="'.$row["id"].'" data-id="'.$row["id"].'" id="editUserModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route("users.destroy", $row["id"]).'"
                del_title="User: '.$row["name"].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['user_level_id','Actions'])
            ->make(true);
    }

    public function index()
    {
        // echo app_path().'<br>';     // '/var/www/mysite/app'
        // echo storage_path(); // '/var/www/mysite/storage'
        // dd(base_path());
        // $users = User::with('userlevel')->get();
        return view('usermanagement.users.index');
    }

    public function getUserLevelList()
    {
        $userlevels = UserLevel::get(['id','name']);
        if($userlevels->count()  > 0){
            $success = 'yes';
            $data = $userlevels;
        }else{
            $success = 'no';
            $data = $userlevels;
        }
        return response()->json([
            'success' => $success,
            'userlevels' => $data,
        ], 201);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'bail|required|string',
            'user_level_id' => 'bail|required|integer',
            'email' => 'bail|required|string',
            'password' => 'bail|required|string',
            'contact_no' => 'bail|required|string',
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
        if($request->user_id_modal > 0){
            $user_data = User::find($request->user_id_modal);
        }else{
            $user_data = null;
        }
        // $country = $session?->user?->getAddress()?->country;
        if ($request->hasFile('image_file')) {
            if($user_data?->picture != null && \Storage::disk('public')->exists('users/'.$user_data?->picture)){
                \Storage::disk('public')->delete('users/'.$user_data?->picture);
            }
            $path = 'users/';
            $image_file = $request->file('image_file');
            $extension = $request->file('image_file')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->expense_id_modal > 0){
            if($user_data !=''){
                $message = 'Data Updated successfully!';
                $success = 'yes';
                if($user_data->picture !='' && $imageName == null){
                    $imageName = $user_data->picture;
                }
                $update_user = $user_data->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_level_id' => $request->user_level_id,
                    'contact_no' => $request->contact_no,
                    'password' => $request->password,
                    'picture' => $imageName,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No entry found against this id';
                $success = 'no';
            }
        }else{
            $sv_user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_level_id' => $request->user_level_id,
                'contact_no' => $request->contact_no,
                'password' => $request->password,
                'picture' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);
            if($sv_user){
                $message = 'New User created successfully!';
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
        $expense = User::find($id);
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
        $user = User::with('userlevel:id,name')->find($id);
        if($user){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'user' => $user->toArray(),
            ], 201);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $img_path = 'users/'.$user?->picture;
        if($user?->picture != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $user->delete();
        Session::flash('swal_notification', ['title' => 'Deleted', 'icon_type' => 'success', 'message' => 'Data Deleted Successfully!']);
        return redirect()->route('users.index');
    }
    
}
