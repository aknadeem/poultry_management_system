<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrokerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('broker');

        return [
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'cnic_no' => 'bail|required|string|min:13|max:13|unique:brokers,cnic_no,'.$id,
            'email' => 'bail|required|string',
            'contact_number' => 'bail|required|string|min:11|max:11',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'address' => 'bail|nullable|string',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'cnic_no.min' => 'The CNIC Number must be at least 13 Digits',
            'cnic_no.max' => 'The CNIC Number must not be greater than 13 Digits',
            'contact_number.min' => 'The Contact number must be at least 11 Digits',
            'contact_number.max' => 'The Contact number must not be greater than 11 Digits',
        ];
    }
}
