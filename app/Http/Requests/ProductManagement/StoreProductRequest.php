<?php

namespace App\Http\Requests\ProductManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_taxable' => $this->boolean('is_taxable') ? 1 : 0,
            'is_sale_on_tp' => $this->boolean('is_sale_on_tp') ? 1 : 0,
            'is_claimable' => $this->boolean('is_claimable') ? 1 : 0,
            'is_fridged' => $this->boolean('is_fridged') ? 1 : 0,
            'is_narcotic' => $this->boolean('is_narcotic') ? 1 : 0,
            'is_unwaranted' => $this->boolean('is_unwaranted') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_group' => ['bail', 'required', 'integer', Rule::in([1, 2, 3, 4])],
            'company_id' => ['bail', 'required', 'integer', 'exists:party_companies,id'],
            'product_category_id' => ['bail', 'required', 'integer', 'exists:product_categories,id'],
            'product_name' => ['bail', 'required', 'string'],
            'batch_number' => ['bail', 'nullable', 'string'],
            'serial_number' => ['bail', 'nullable', 'string'],
            'product_type' => ['bail', 'required', 'integer', 'exists:product_types,id'],
            'vaccination_group' => ['bail', 'nullable', 'integer', 'exists:vaccination_groups,id'],
            'pack_size_unit' => ['bail', 'nullable', 'numeric'],
            'pack_size_unit_type' => ['bail', 'nullable', 'string', Rule::in(['gram', 'kilo_gram'])],
            'store_id' => ['bail', 'required', 'integer', 'exists:product_stores,id'],
            'rack_number' => ['bail', 'nullable', 'numeric'],
            'min_level' => ['bail', 'nullable', 'numeric'],
            'max_level' => ['bail', 'nullable', 'numeric'],
            'mrp_price' => ['bail', 'nullable', 'numeric'],
            'whole_sale_price' => ['bail', 'nullable', 'numeric'],
            'full_less_price' => ['bail', 'nullable', 'numeric'],
            'store_price' => ['bail', 'nullable', 'numeric'],
            'retail_price' => ['bail', 'nullable', 'numeric'],
            'trade_price' => ['bail', 'nullable', 'numeric'],
            'purchase_price' => ['bail', 'required', 'numeric'],
            'sale_price' => ['bail', 'required', 'numeric'],
            'discount_amount' => ['bail', 'nullable', 'numeric'],
            'tax_percentage' => ['bail', 'nullable', 'numeric'],
            'tax_amount' => ['bail', 'nullable', 'numeric'],
            'discount_percentage' => ['bail', 'nullable', 'numeric'],
            'warranty_period' => ['bail', 'nullable', 'numeric'],
            'is_taxable' => ['bail', 'nullable', 'boolean'],
            'is_sale_on_tp' => ['bail', 'nullable', 'boolean'],
            'is_claimable' => ['bail', 'nullable', 'boolean'],
            'is_fridged' => ['bail', 'nullable', 'boolean'],
            'is_narcotic' => ['bail', 'nullable', 'boolean'],
            'is_unwaranted' => ['bail', 'nullable', 'boolean'],
            'description' => ['bail', 'nullable', 'string'],
            'product_picture' => ['bail', 'nullable', 'mimes:jpeg,jpg,png', 'max:5000'],
        ];
    }
}
