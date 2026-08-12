<?php

namespace App\Http\Controllers\PartyManagement;

use App\Actions\PartyManagement\DestroyPartyDocumentAction;
use App\Actions\PartyManagement\StorePartyDocumentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyManagement\StorePartyDocumentRequest;
use App\Models\PartyDocument;
use Illuminate\Support\Facades\Log;

class PartyDocumentController extends Controller
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
        $party_documents = PartyDocument::get();

        return view('partymanagement.company.index', compact('party_documents'));
    }

    public function store(StorePartyDocumentRequest $request, StorePartyDocumentAction $action)
    {
        try {
            $documentId = (int) $request->input('party_document_id', 0);
            $action->execute(
                $request->validated(),
                $request->file('document_name'),
                $this->auth_user_id
            );

            return response()->json([
                'message' => $documentId > 0 ? 'Data Updated successfully!' : 'Data created successfully!',
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
        $party_document = PartyDocument::find($id);
        if ($party_document) {
            return response()->json([
                'message' => 'yes',
                'party_document' => $party_document->toArray(),
            ], 201);
        }
    }

    public function destroy($id, DestroyPartyDocumentAction $action)
    {
        $document = PartyDocument::findOrFail($id);
        $action->execute($document);

        return redirect()->route('company.index');
    }
}
