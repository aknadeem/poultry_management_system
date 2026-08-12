<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyBalanceLimitAction;
use App\Actions\PartyManagement\StorePartyBalanceLimitAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyBalanceLimitRequest;
use App\Models\PartyBalanceLimit;
use Illuminate\Support\Facades\Log;

class PartyBalanceLimitController extends Controller
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
        $balance_limits = PartyBalanceLimit::get();

        return view('partymanagement.company.index', compact('balance_limits'));
    }

    public function store(StorePartyBalanceLimitRequest $request, StorePartyBalanceLimitAction $action)
    {
        try {
            $limitId = (int) $request->input('party_balance_limit_id', 0);
            $action->execute($request->validated(), $this->auth_user_id);

            return response()->json([
                'message' => $limitId > 0 ? 'A Company Data Updated successfully!' : 'Data created successfully!',
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
        $balance_limit = PartyBalanceLimit::find($id);
        if ($balance_limit) {
            return response()->json([
                'message' => 'yes',
                'balance_limit' => $balance_limit->toArray(),
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyBalanceLimitAction $action)
    {
        $limit = PartyBalanceLimit::findOrFail($id);
        $action->execute($limit);

        return redirect()->route('company.index');
    }
}
