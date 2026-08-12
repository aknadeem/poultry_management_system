<?php

namespace App\Http\Requests\InventoryManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feed_name' => 'bail|required|string',
            'purchase_date' => 'bail|required|date',
            'feed_category_id' => 'bail|required|integer',
            'company_id' => 'bail|required|integer',
            'quantity' => 'bail|required|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable',
            'total_price' => 'bail|required|numeric',
            'bilty_number' => 'bail|nullable|string',
            'bilty_charges' => 'bail|required|numeric',
            'per_bag_discount' => 'bail|required|numeric',
            'sale_order_number' => 'bail|required|string',
            'delivery_order_number' => 'bail|required|string',
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
