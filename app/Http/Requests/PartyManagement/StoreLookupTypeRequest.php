<?php

namespace App\Http\Requests\PartyManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLookupTypeRequest extends FormRequest
{
    use InertiaAwareFailedValidation;
    public const ALLOWED_TABLES = [
        'divisions',
        'customer_types',
        'vendor_types',
        'business_types',
        'farm_types',
        'farm_subtypes',
        'employee_types',
        'employee_levels',
        'product_categories',
        'vaccination_groups',
        'feed_categories',
        'chick_grades',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = (string) $this->input('tag_name');
        $nameRules = ['bail', 'required', 'string'];

        if (in_array($table, self::ALLOWED_TABLES, true)) {
            $nameRules[] = Rule::unique($table, 'name');
        }

        return [
            'tag_name' => ['bail', 'required', 'string', Rule::in(self::ALLOWED_TABLES)],
            'name' => $nameRules,
        ];
    }
}
