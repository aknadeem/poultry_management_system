<?php

namespace App\Http\Requests\ChickenModule;

use Illuminate\Foundation\Http\FormRequest;

class StoreChickenSaleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'manual_number' => 'bail|required|string',
            'sale_date' => 'bail|required|date',
            'customer_id' => 'bail|required|integer',
            'broker_id' => 'bail|required|integer',
            'first_weight' => 'bail|nullable|numeric',
            'second_weight' => 'bail|nullable|numeric',
            'net_weight' => 'bail|nullable|numeric',
            'total_weight' => 'bail|required|numeric',
            'per_kg_price' => 'bail|required|numeric',
            'discount_amount' => 'bail|required|numeric',
            'discount_percentage' => 'bail|required|numeric',
            'total_price' => 'bail|required|numeric',
            'vehicle_number' => 'bail|required|string',
            'driver_name' => 'bail|required|string',
            'driver_contact' => 'bail|required|numeric',
            'image_file' => 'bail|nullable|mimes:jpeg,jpg,png|max:5000',
            'broker_commission' => 'bail|nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'image_file.max'=> 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }
}
