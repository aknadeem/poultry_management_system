<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePartyQuickRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id_modal' => 'bail|nullable|integer',
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'farm_name' => 'bail|nullable|string',
            'address' => 'bail|required|string',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'is_vendor' => 'bail|nullable|boolean',
            'is_customer' => 'bail|nullable|boolean',
        ];
    }
}
