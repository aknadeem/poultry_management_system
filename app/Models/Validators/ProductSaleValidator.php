<?php

namespace App\Models\Validators;

use App\Models\ProductSale;
use Illuminate\Validation\Rule;

class ProductSaleValidator
{
    public function validate(array $attributes): array
    {
        return validator($attributes,
            [
                'division_id' => ['bail', 'required', 'integer'],
                'party_id' => ['bail', 'required', 'integer'],
                'product_category_id' => ['bail', 'required', 'integer'],
                'party_company_id' => ['bail', 'required', 'integer'],

                'sale_date' => ['bail', 'required', 'date'],
                'due_date_option' => ['bail', 'required', 'string'],
                'manual_number' => ['bail', 'nullable'],
                'sale_type' => ['bail', 'required', 'string'],
                'total_amount' => ['bail', 'required', 'numeric'],
                'discount_amount' => ['bail', 'required', 'numeric'],
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