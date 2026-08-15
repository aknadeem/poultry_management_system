<?php

namespace App\Http\Controllers\UserManagement;

use Session;
use DataTables;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\UserManagement\StoreUserAction;
use App\Actions\UserManagement\UpdateUserAction;
use App\Actions\UserManagement\DestroyUserAction;
use App\Http\Requests\UserManagement\StoreUserRequest;

class UserController extends Controller
{
    private $authUserId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUserId = \Auth::user()->id;

            return $next($request);
        });
    }

    public function getUsersList()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('userRole:id,name,slug')->orderBy('id', 'DESC')->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user_role_id', function ($row) {
                return '<span>'.$row?->userRole?->name.'</span>';
            })
            ->addColumn('Actions', function ($row) {
                return ' <a class="btn btn-secondary btn-sm ViewUserModal"
                UserId="'.$row['id'].'" href="javascript:void(0);"
                title="View Details" tabindex="0" data-plugin="tippy"
                data-tippy-animation="scale" data-tippy-arrow="true"><i class="fa fa-eye"></i>
                View
            </a>
            <a class="btn btn-info btn-sm openUserModal"
                UserId="'.$row['id'].'" data-id="'.$row['id'].'" id="editUserModal" href="javascript:void(0);"
                title="Click to edit"><i
                    class="fa fa-pencil-alt"></i>
                Edit
            </a>
            <a class="btn btn-danger btn-sm delete-confirm"
                href="'.route('users.destroy', $row['id']).'"
                del_title="User: '.$row['name'].'" title="Click to delete"
                tabindex="0" data-plugin="tippy" data-tippy-animation="scale"
                data-tippy-arrow="true"><i class="fa fa-trash"></i>
                Delete
            </a>';
            })
            ->rawColumns(['user_role_id', 'Actions'])
            ->make(true);
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        return view('usermanagement.users.index');
    }

    public function getUserRoleList()
    {
        $this->authorize('create', User::class);
        $userRoles = UserRole::get(['id', 'name']);

        return response()->json([
            'success' => $userRoles->count() > 0 ? 'yes' : 'no',
            'userroles' => $userRoles,
        ], 201);
    }

    public function store(
        StoreUserRequest $request,
        StoreUserAction $storeAction,
        UpdateUserAction $updateAction
    ) {
        try {
            $userId = (int) ($request->input('user_id_modal') ?? 0);
            $imageFile = $request->file('image_file');

            if ($userId > 0) {
                $user = User::find($userId);
                if (! $user) {
                    return response()->json([
                        'message' => 'No entry found against this id',
                        'success' => 'no',
                    ], 200);
                }

                $this->authorize('update', $user);
                $updateAction->execute($user, $request->validated(), $imageFile, $this->authUserId);
                $message = 'Data Updated successfully!';
            } else {
                $this->authorize('create', User::class);
                $storeAction->execute($request->validated(), $imageFile, $this->authUserId);
                $message = 'New User created successfully!';
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

    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->authorize('view', $user);
            $html_data = \View::make('layouts._partial.customerdetail', ['expense' => $user])->render();

            return response()->json([
                'message' => 'User Detail Data',
                'success' => 'yes',
                'html_data' => $html_data,
            ], 201);
        }

        return response()->json([
            'message' => 'No data found against this id',
            'success' => 'no',
            'html_data' => '',
        ], 201);
    }

    public function edit($id)
    {
        $user = User::with('userRole:id,name')->find($id);
        if (! $user) {
            return response()->json(['message' => 'no'], 201);
        }

        $this->authorize('view', $user);
        return response()->json([
            'message' => 'yes',
            'user' => $user->toArray(),
        ], 201);
    }

    public function destroy($id, DestroyUserAction $action)
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('delete', $user);
            $action->execute($user);
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

        return redirect()->route('users.index');
    }
}
