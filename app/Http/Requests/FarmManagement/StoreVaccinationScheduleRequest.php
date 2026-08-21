<?php

namespace App\Http\Requests\FarmManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreVaccinationScheduleRequest extends FormRequest
{
    use InertiaAwareFailedValidation;

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
            'farm_id' => ['bail', 'required', 'integer', 'exists:party_farms,id'],
            'product_id' => ['bail', 'required', 'integer', 'exists:products,id'],
            'schedule_date' => ['bail', 'required', 'date', 'after_or_equal:today'],
            'description' => ['bail', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_date.after_or_equal' => 'The date must be after or equal to Current date: '.today()->format('Y-m-d'),
        ];
    }
}
