<?php

namespace App\Http\Requests\ChickenModule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChickenPurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_date' => 'bail|required|date',
            'vehicle_number' => 'bail|nullable|string',
            'driver_name' => 'bail|nullable|string',
            'driver_contact' => 'bail|nullable|numeric',
            'company_id' => 'bail|required|integer',
            'quantity' => 'bail|required|numeric',
            'weight' => 'bail|nullable|numeric',
            'price' => 'bail|required|numeric',
            'discount_amount' => 'bail|required|numeric',
            'discount_percentage' => 'bail|required|numeric',
            'total_price' => 'bail|required|numeric',
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
