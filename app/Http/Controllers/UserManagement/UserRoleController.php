<?php

namespace App\Http\Controllers\UserManagement;

use App\Models\UserRole;
use Illuminate\Http\Request;
use DataTables;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    private $authUserId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId= \Auth::user()->id;
            return $next($request);
        });
    }

    public function getUserRolesList()
    {
        $roles = UserRole::orderBy('id','DESC')->get();
        return DataTables::of($roles)
            ->addIndexColumn()
            ->make(true);
    }

    public function index()
    {
        return view('usermanagement.userroles.index');
    }
}
