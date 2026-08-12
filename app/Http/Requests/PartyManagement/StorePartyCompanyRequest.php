<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePartyCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id_modal' => 'bail|nullable|integer',
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'address' => 'bail|required|string',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'description' => 'nullable|string',
            'party_id' => 'bail|nullable|integer',
            'business_type_id' => 'bail|nullable|integer',
        ];
    }
}
