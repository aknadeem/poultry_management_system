<?php

namespace App\Http\Controllers\PartyManagement;

use Illuminate\Http\Request;
use App\Models\PartyDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerFormRequest;

class PartyDocumentController extends Controller
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
        // dd($this->auth_user_id);
        $party_documents = PartyDocument::get();
        return view('partymanagement.company.index', compact('party_documents'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'party_id' => 'bail|required|integer',
            'document_title' => 'bail|required|string',
            'document_name' => 'bail|required',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()->toArray(),
                'success' => 'no',
            ], 201);
        }
        
        if($request->party_document_id > 0){
            $party_document = PartyDocument::find($request->party_document_id);
        }else{
            $party_document = null;
        }

        if ($request->hasFile('document_name')) {
            if($party_document?->document_name != null && \Storage::disk('public')->exists('party/documents/'.$party_document?->document_name)){
                \Storage::disk('public')->delete('parties/documents/'.$company_data?->document_name);
            }
            $path = 'party/documents/';
            $image_file = $request->file('document_name');
            $extension = $request->file('document_name')->extension();
            $imageName = time().mt_rand(10,99).'.'.$extension;
            $upload = $image_file->storeAs($path, $imageName, 'public');
        }else{
            $imageName = null;
        }

        if($request->party_account_id > 0){
            if($party_document !=''){
                $message = 'Data Updated successfully!';
                $success = 'yes';

                if($party_document?->document_name !='' && $imageName == null){
                    $imageName = $party_document?->document_name;
                }

                $update_doc = $party_document->update([
                    'party_id' => $request->party_id,
                    'title' => $request->document_title,
                    'document_name' => $imageName,
                    'updatedby' => $this->auth_user_id,
                ]);
            }else{
                $message = 'No data found against this id';
                $success = 'no';
            }
        }else{
            $party_document = PartyDocument::create([
                'party_id' => $request->party_id,
                'title' => $request->document_title,
                'document_name' => $imageName,
                'addedby' => $this->auth_user_id,
            ]);
            if($party_document){
                $message = 'Data created successfully!';
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
       return 0;
    }


    public function edit($id)
    {
        $party_document = PartyDocument::find($id);
        if($party_document){
            $message = 'yes';
            return response()->json([
                'message' => $message,
                'party_document' => $party_document->toArray(),
            ], 201);
        }
    }
    
    public function destroy($id)
    {
        $party_document = PartyDocument::findOrFail($id);

        $img_path = 'party/documents/'.$party_document?->document_name;
        if($party_document?->document_name != null && \Storage::disk('public')->exists($img_path)){
            \Storage::disk('public')->delete($img_path);
        }
        $party_document->delete();
        return redirect()->route('company.index');
    }
}
