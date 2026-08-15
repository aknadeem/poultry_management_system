<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'cnic_no' => 'bail|required|string',
            'farm_name' => 'bail|required|string',
            'farm_type_id' => 'bail|required|integer',
            'farm_subtype_id' => 'bail|required|integer',
            'farm_name' => 'bail|required|string',
            'farm_noc' => 'bail|required|string',
            'farm_address' => 'bail|required|string',
            'farm_image' => [
                'bail',
                'required',
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],

            'address' => 'bail|required|string',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'is_vendor' => 'bail|nullable|boolean',
            'is_customer' => 'bail|nullable|boolean',
            'cnic_front' => [
                'bail',
                'required',
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
            'cnic_back' => [
                'bail',
                'required',
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
            'description' => 'bail|nullable',
        ];
    }
}
