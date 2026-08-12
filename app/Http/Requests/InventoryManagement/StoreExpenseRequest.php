<?php

namespace App\Http\Requests\InventoryManagement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'bail|required|numeric',
            'expense_date' => 'bail|required|date',
            'category_id' => 'bail|required|integer',
            'remarks' => 'bail|required|string',
            'image_file' => 'nullable|mimes:jpeg,jpg,png|max:5000',
            'expense_id_modal' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'image_file.max' => 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'error' => $validator->errors()->toArray(),
            'success' => 'no',
        ], 201));
    }
}
