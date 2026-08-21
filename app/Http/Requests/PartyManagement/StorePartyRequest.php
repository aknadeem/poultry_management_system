<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->basePartyFieldRules(null), [
            'is_vendor' => 'bail|nullable',
            'is_customer' => 'bail|nullable',
            'business_no' => 'bail|nullable|string',
            'opening_balance' => 'bail|nullable|numeric',
            'balance_type' => 'bail|nullable',
            'description' => 'bail|nullable|string',
            'contact_person_id' => 'bail|nullable|integer',
            'customer_division_id' => 'bail|exclude_unless:is_customer,1|nullable|integer',
            'cnic_front' => 'bail|required|mimes:jpeg,jpg,png|max:5000',
            'cnic_back' => 'bail|required|mimes:jpeg,jpg,png|max:5000',
            'farm_image' => 'bail|exclude_unless:is_customer,1|required|mimes:jpeg,jpg,png|max:5000',
            'company_logo' => 'bail|exclude_unless:is_vendor,1|required|mimes:jpeg,jpg,png|max:5000',
        ]);
    }

    public function messages(): array
    {
        return [
            'cnic_no.min' => 'The CNIC Number must be at least 13 Digits',
            'cnic_no.max' => 'The CNIC Number must not be greater than 13 Digits',
            'contact_no.min' => 'The Contact number must be at least 11 Digits',
            'contact_no.max' => 'The Contact number must not be greater than 11 Digits',
            'farm_image.required_if' => 'The farm image field is required',
            'company_logo.required_if' => 'The company logo field is required',
        ];
    }

    private function basePartyFieldRules($id): array
    {
        return [
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'cnic_no' => 'bail|required|string|min:13|max:13|unique:parties,cnic_no,'.$id,
            'email' => 'bail|nullable|string',
            'contact_no' => 'bail|required|string|min:11|max:11',
            'business_number' => 'bail|nullable|string|min:11|max:11',
            'manual_number' => 'bail|required|string',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'address' => 'bail|nullable|string',
            'profile_picture' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'signature_image' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'customer_type_id' => 'bail|exclude_unless:is_customer,1|required|integer',
            'farm_type_id' => 'bail|exclude_unless:is_customer,1|required|integer',
            'farm_subtype_id' => 'bail|exclude_unless:is_customer,1|required|integer',
            'farm_name' => 'bail|exclude_unless:is_customer,1|required|string',
            'farm_noc' => 'bail|exclude_unless:is_customer,1|required|string',
            'farm_address' => 'bail|exclude_unless:is_customer,1|required|string',
            'vendor_division_id' => 'bail|exclude_unless:is_vendor,1|required|integer',
            'vendor_type_id' => 'bail|exclude_unless:is_vendor,1|required|integer',
            'company_name' => 'bail|exclude_unless:is_vendor,1|required|string',
            'business_type_id' => 'bail|exclude_unless:is_vendor,1|required|integer',
            'company_address' => 'bail|exclude_unless:is_vendor,1|required|string',
        ];
    }
}
