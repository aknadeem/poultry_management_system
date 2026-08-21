<?php

namespace App\Http\Requests\FarmManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonalFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'farm_type_id' => ['bail', 'required', 'integer', 'exists:farm_types,id'],
            'farm_subtype_id' => ['bail', 'required', 'integer', 'exists:farm_subtypes,id'],
            'farm_name' => ['bail', 'required', 'string'],
            'farm_noc' => ['bail', 'required', 'string'],
            'farm_area' => ['bail', 'required', 'numeric'],
            'farm_capacity' => ['bail', 'required', 'integer'],
            'feed_room_size' => ['bail', 'required', 'numeric'],
            'farm_address' => ['bail', 'nullable', 'string'],
            'country_id' => ['bail', 'required', 'integer', 'exists:countries,id'],
            'province_id' => ['bail', 'required', 'integer', 'exists:provinces,id'],
            'city_id' => ['bail', 'required', 'integer', 'exists:cities,id'],
            'farm_image' => ['bail', 'required', 'image', 'mimes:jpeg,jpg,png', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'farm_image.required' => 'The farm image field is required',
        ];
    }
}
