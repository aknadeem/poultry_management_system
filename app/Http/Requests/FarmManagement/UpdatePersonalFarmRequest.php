<?php

namespace App\Http\Requests\FarmManagement;

use App\Models\PersonalFarm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $farm = $this->route('personalfarm')
            ?? $this->route('personal_farm')
            ?? $this->route('personalFarm');
        if (! $farm instanceof PersonalFarm && $farm !== null) {
            $farm = PersonalFarm::query()->find($farm);
        }

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
            'farm_image' => [
                'bail',
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5000',
                Rule::requiredIf(fn (): bool => blank($farm?->farm_image)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'farm_image.required' => 'The farm image field is required',
        ];
    }
}
