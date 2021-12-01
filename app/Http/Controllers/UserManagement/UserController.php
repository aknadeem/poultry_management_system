<?php

namespace App\Http\Controllers\UserManagement;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;

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
    
}
