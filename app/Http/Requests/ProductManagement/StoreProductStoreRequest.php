<?php

namespace App\Http\Requests\ProductManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string'],
            'store_type' => ['required', 'string'],
            'total_racks' => ['required', 'integer', 'min:0'],
            'store_area' => ['required', 'numeric', 'min:0'],
            'store_desciption' => ['nullable', 'string'],
        ];
    }
}
