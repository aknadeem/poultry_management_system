<?php

namespace App\Http\Requests\FarmManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $id = $employee instanceof \App\Models\Employee ? $employee->id : $employee;

        return [
            'personal_farm_id' => 'bail|nullable|integer|exists:personal_farms,id',
            'employee_type_id' => 'bail|nullable|integer',
            'employee_level_id' => 'bail|required|integer',
            'name' => 'bail|required|string',
            'guardian_name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'other_number' => 'bail|nullable|numeric',
            'email' => 'bail|required|string',
            'cnic_no' => [
                'bail',
                'required',
                'numeric',
                Rule::unique('employees', 'cnic_no')->ignore($id),
            ],
            'father_cnic_no' => 'bail|nullable|string',
            'basic_salary' => 'bail|required|numeric',
            'other_amount' => 'bail|nullable|numeric',
            'net_salary' => 'bail|required|numeric',
            'contract_period' => 'bail|numeric|numeric',
            'date_of_birth' => 'bail|date',
            'joining_date' => 'bail|date',
            'is_police_record' => 'bail|nullable',
            'address' => 'bail|required|string',
            'blood_group' => 'bail|nullable',
            'description' => 'nullable|string',
            'country_id' => 'bail|required|integer',
            'province_id' => 'bail|required|integer',
            'city_id' => 'bail|required|integer',
            'employee_signature' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'employee_image' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'farm_image.required' => 'The farm image field is required',
        ];
    }
}
