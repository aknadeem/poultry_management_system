<?php

namespace App\Http\Requests\FarmManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use App\Models\PartyFarm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerFarmRequest extends FormRequest
{
    use InertiaAwareFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $farm = $this->route('customerfarm')
            ?? $this->route('customer_farm')
            ?? $this->route('customerFarm');
        if (! $farm instanceof PartyFarm && $farm !== null) {
            $farm = PartyFarm::query()->find($farm);
        }

        return [
            'farm_type_id' => ['bail', 'required', 'integer', 'exists:farm_types,id'],
            'farm_subtype_id' => ['bail', 'required', 'integer', 'exists:farm_subtypes,id'],
            'farm_name' => ['bail', 'required', 'string'],
            'farm_noc' => ['bail', 'required', 'string'],
            'farm_address' => ['bail', 'required', 'string'],
            'farm_area' => ['bail', 'nullable', 'numeric'],
            'feed_room_size' => ['bail', 'nullable', 'numeric'],
            'farm_capacity' => ['bail', 'nullable', 'integer'],
            'farm_image' => [
                'bail',
                'nullable',
                'mimes:jpeg,jpg,png',
                'max:5000',
                Rule::requiredIf(fn () => blank($farm?->farm_image)),
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
