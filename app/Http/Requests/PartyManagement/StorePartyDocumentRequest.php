<?php

namespace App\Http\Requests\PartyManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyDocumentRequest extends FormRequest
{
    use InertiaAwareFailedValidation;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $documentId = (int) $this->input('party_document_id', 0);
        $existing = $documentId > 0
            ? \App\Models\PartyDocument::find($documentId)
            : null;

        return [
            'party_document_id' => 'bail|nullable|integer',
            'party_id' => 'bail|required|integer',
            'document_title' => 'bail|required|string',
            'document_name' => [
                'bail',
                Rule::requiredIf(fn () => blank($existing?->document_name)),
                'file',
            ],
        ];
    }
}
