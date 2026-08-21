<?php

namespace App\Http\Requests\ProductManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $items = [];
        $ids = $this->input('product_id') ?? [];
        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $items[] = [
                    'product_id'                  => $val,
                    'product_code'                => $this->input("product_code.{$key}"),
                    'product_name'                => $this->input("product_name.{$key}"),
                    'product_purchase_price'      => $this->input("product_sale_price.{$key}"), // maps form field `product_sale_price` to `product_purchase_price`
                    'product_qty'                 => $this->input("product_qty.{$key}"),
                    'product_bonus_qty'           => $this->input("product_bonus_qty.{$key}"),
                    'product_total_qty'           => $this->input("product_total_qty.{$key}"),
                    'product_discount'            => $this->input("product_discount.{$key}"),
                    'product_discount_percentage' => $this->input("product_discount_percentage.{$key}"),
                    'product_total_price'         => $this->input("product_total_price.{$key}"),
                ];
            }
        }
        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'party_company_id'    => ['bail', 'required', 'integer', 'exists:party_companies,id'],
            'product_category_id' => ['bail', 'required', 'integer', 'exists:product_categories,id'],
            'purchase_date'       => ['bail', 'required', 'date'],
            'expiry_date'         => ['bail', 'nullable', 'date'],
            'total_amount'        => ['bail', 'required', 'numeric'],
            'discount_amount'     => ['bail', 'nullable', 'numeric'],
            'discount_percentage' => ['bail', 'nullable', 'numeric'],
            'other_charges'       => ['bail', 'required', 'numeric'],
            'final_amount'        => ['bail', 'required', 'numeric'],
            'invoice_picture'     => ['bail', 'nullable'],
            'description'         => ['bail', 'nullable', 'string'],
            'due_date_option'     => ['bail', 'nullable', 'string'],
            'manual_number'       => ['bail', 'nullable', 'string'],

            'items'                                 => ['required', 'array', 'min:1'],
            'items.*.product_id'                    => ['bail', 'required', 'integer', 'exists:products,id'],
            'items.*.product_code'                  => ['bail', 'required', 'string'],
            'items.*.product_name'                  => ['bail', 'required', 'string'],
            'items.*.product_purchase_price'        => ['bail', 'required', 'numeric', 'min:0'],
            'items.*.product_qty'                   => ['bail', 'required', 'integer', 'min:1'],
            'items.*.product_bonus_qty'             => ['bail', 'nullable', 'integer', 'min:0'],
            'items.*.product_total_qty'             => ['bail', 'nullable', 'integer', 'min:0'],
            'items.*.product_discount'              => ['bail', 'nullable', 'numeric', 'min:0'],
            'items.*.product_discount_percentage'   => ['bail', 'nullable', 'numeric', 'min:0'],
            'items.*.product_total_price'           => ['bail', 'required', 'numeric', 'min:0'],
        ];
    }
}
