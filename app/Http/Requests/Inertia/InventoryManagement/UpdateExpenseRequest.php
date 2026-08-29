<?php

namespace App\Http\Requests\Inertia\InventoryManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'image_file.max' => 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }
}
