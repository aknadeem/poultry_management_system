<?php

namespace App\Http\Requests\Inertia\InventoryManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cat_name' => 'bail|required|string',
        ];
    }
}
