<?php

namespace App\Http\Requests\FarmManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use App\Models\VaccinationSchedule;
use Illuminate\Foundation\Http\FormRequest;

class RecordVaccinationRequest extends FormRequest
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
        $schedule = VaccinationSchedule::query()->find($this->input('schedule_id'));
        $scheduleDate = $schedule?->schedule_date?->format('Y-m-d') ?? today()->format('Y-m-d');

        return [
            'schedule_id' => ['bail', 'required', 'integer'],
            'vaccination_date' => ['bail', 'required', 'date', 'after_or_equal:'.$scheduleDate],
            'remarks' => ['bail', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        $schedule = VaccinationSchedule::query()->find($this->input('schedule_id'));
        $scheduleDate = $schedule?->schedule_date?->format('Y-m-d') ?? today()->format('Y-m-d');

        return [
            'vaccination_date.after_or_equal' => 'The date must be after or equal to Schedule date: '.$scheduleDate,
        ];
    }
}
