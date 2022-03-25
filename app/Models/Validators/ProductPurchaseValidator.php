<?php

namespace App\Models\Validators;

use Illuminate\Validation\Rule;

class ProductPurchaseValidator
{
    public function validate(array $attributes): array
    {
        return validator($attributes,
            [
                'party_company_id' => ['bail', 'required', 'integer'],
                'product_category_id' => ['bail', 'required', 'integer'],
                'purchase_date' => ['bail', 'required', 'date'],
                'total_amount' => ['bail', 'required', 'numeric'],
                'discount_amount' => ['bail', 'nullable', 'numeric'],
                'discount_percentage' => ['bail', 'nullable', 'numeric'],
                'other_charges' => ['bail', 'required', 'numeric'],
                'final_amount' => ['bail', 'required', 'numeric'],
                'invoice_picture' => ['bail', 'nullable'],
                'description' => ['bail', 'nullable', 'string'],
                
                'product_id' => ['array'],
                'product_id.*' => ['bail', 'required', 'integer'],
                'product_code' => ['array'],
                'product_code.*' => ['bail', 'required', 'string'],
                'product_name' => ['array'],
                'product_name.*' => ['bail', 'required', 'string'],
                'product_sale_price' => ['array'],
                'product_sale_price.*' => ['bail', 'required', 'numeric'],
                'product_qty' => ['array'],
                'product_qty.*' => ['bail', 'required', 'integer'],
                'product_bonus_qty' => ['array'],
                'product_bonus_qty.*' => ['bail', 'nullable', 'integer'],  
                'product_total_qty' => ['array'],
                'product_total_qty.*' => ['bail', 'nullable', 'integer'],
                'product_discount' => ['array'],
                'product_discount.*' => ['bail', 'nullable', 'numeric'],
                'product_discount_percentage' => ['array'],
                'product_discount_percentage.*' => ['bail', 'nullable', 'numeric'],
                'product_total_price' => ['array'],
                'product_total_price.*' => ['bail', 'required', 'numeric'],
            ]
        )->validate();
    }
}