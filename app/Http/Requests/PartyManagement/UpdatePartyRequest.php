<?php

namespace App\Http\Requests\PartyManagement;

use App\Models\Party;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('party');
        $party = Party::with('farm:id,party_id,farm_image', 'company:id,party_id,company_logo')->findOrFail($id);

        return array_merge($this->basePartyFieldRules($id), [
            'is_vendor' => 'bail|nullable',
            'is_customer' => 'bail|nullable',
            'business_no' => 'bail|nullable|string',
            'opening_balance' => 'bail|nullable|numeric',
            'balance_type' => 'bail|nullable',
            'description' => 'bail|nullable|string',
            'contact_person_id' => 'bail|nullable|integer',
            'customer_division_id' => 'bail|nullable|integer',
            'cnic_front' => [
                'bail',
                'nullable',
                Rule::requiredIf(fn () => blank($party->cnic_front)),
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
            'cnic_back' => [
                'bail',
                'nullable',
                Rule::requiredIf(fn () => blank($party->cnic_back)),
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
            'signature_image' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'farm_image' => [
                'bail',
                'nullable',
                Rule::requiredIf(fn () => (int) $this->input('is_customer') === 1 && blank($party->farm?->farm_image)),
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
            'company_logo' => [
                'bail',
                'nullable',
                Rule::requiredIf(fn () => (int) $this->input('is_vendor') === 1 && blank($party->company?->company_logo)),
                'mimes:jpeg,jpg,png',
                'max:5000',
            ],
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
            'customer_type_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_type_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_subtype_id' => 'bail|required_if:is_customer,==,1|integer',
            'farm_name' => 'bail|required_if:is_customer,==,1|string',
            'farm_noc' => 'bail|required_if:is_customer,==,1|string',
            'farm_address' => 'bail|required_if:is_customer,==,1|string',
            'vendor_division_id' => 'bail|required_if:is_vendor,==,1|integer',
            'vendor_type_id' => 'bail|required_if:is_vendor,==,1|integer',
            'company_name' => 'bail|required_if:is_vendor,==,1|string',
            'business_type_id' => 'bail|required_if:is_vendor,==,1|integer',
            'company_address' => 'bail|required_if:is_vendor,==,1|string',
        ];
    }
}
