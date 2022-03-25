<?php

namespace App\Http\Controllers\UserManagement;

use App\Models\UserLevel;
use Illuminate\Http\Request;
use DataTables;
use App\Http\Controllers\Controller;

class UserLevelController extends Controller
{
    private $auth_user_id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id= \Auth::user()->id;
            return $next($request);
        });
    }

    public function getUserLevelsList()
    {
        $users = UserLevel::orderBy('id','DESC')->get();
        return DataTables::of($users)
            ->addIndexColumn()
            ->make(true);
    }

    public function index()
    {
        return view('usermanagement.userlevels.index');
    }
}
