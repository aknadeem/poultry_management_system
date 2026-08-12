<?php

namespace App\Http\Requests\ChickenModule;

use Illuminate\Foundation\Http\FormRequest;

class StoreChickenPurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_date' => 'bail|required|date',
            'chick_grade_id' => 'bail|required|integer',
            'company_id' => 'bail|required|integer',
            'chick_weight' => 'bail|required|numeric',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|nullable|string',
            'driver_name' => 'bail|nullable|string',
            'driver_contact' => 'bail|nullable|numeric',
            'bilty_number' => 'bail|nullable|string',
            'bilty_charges' => 'bail|nullable|string',
            'sale_order_number' => 'bail|nullable|string',
            'delivery_order_number' => 'bail|nullable|string',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
        ];
    }

    public function messages()
    {
        return [
            'image_file.max' => 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }
}
