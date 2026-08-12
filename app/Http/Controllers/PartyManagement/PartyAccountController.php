<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyAccountAction;
use App\Actions\PartyManagement\StorePartyAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyAccountRequest;
use App\Models\PartyAccount;
use Illuminate\Support\Facades\Log;

class PartyAccountController extends Controller
{
    private $auth_user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth_user_id = \Auth::user()->id;

            return $next($request);
        });
    }

    public function index()
    {
        $party_accounts = PartyAccount::get();

        return view('partymanagement.company.index', compact('party_accounts'));
    }

    public function store(StorePartyAccountRequest $request, StorePartyAccountAction $action)
    {
        try {
            $accountId = (int) $request->input('party_account_id', 0);
            $action->execute($request->validated(), $this->auth_user_id);

            return response()->json([
                'message' => $accountId > 0 ? 'A Data Updated successfully!' : 'Data created successfully!',
                'success' => 'yes',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'success' => 'no',
            ], 201);
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
        return 0;
    }

    public function edit($id)
    {
        $party_account = PartyAccount::find($id);
        if ($party_account) {
            return response()->json([
                'message' => 'yes',
                'party_account' => $party_account->toArray(),
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyAccountAction $action)
    {
        $account = PartyAccount::findOrFail($id);
        $action->execute($account);

        return redirect()->route('company.index');
    }
}
