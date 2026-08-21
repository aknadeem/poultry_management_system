<?php

namespace App\Http\Requests\ChickenModule;

use Illuminate\Foundation\Http\FormRequest;

class StoreChickPurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_id' => 'bail|required|integer',
            'purchase_date' => 'bail|required|date',
            'chick_grade_id' => 'bail|required|integer',
            'company_id' => 'bail|required|integer',
            'chick_entry_age' => 'bail|required|integer',
            'chick_weight' => 'bail|required|numeric',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric|min:0|max:50',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|nullable|numeric',
            'discount_percentage' => 'bail|nullable|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|nullable|string',
            'driver_name' => 'bail|nullable|string',
            'driver_contact' => 'bail|nullable|numeric',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'customer_farm_id' => 'bail|nullable|integer',
            'vendor_id' => 'bail|nullable|integer',
            'bilty_number' => 'bail|nullable|string',
            'bilty_charges' => 'bail|nullable|numeric',
            'sale_order_number' => 'bail|nullable|string',
            'delivery_order_number' => 'bail|nullable|string',
            'remarks' => 'bail|nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }
}
