<?php

namespace App\Http\Requests\PartyManagement;

use App\Http\Requests\Concerns\InertiaAwareFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePartyBalanceLimitRequest extends FormRequest
{
    use InertiaAwareFailedValidation;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'party_balance_limit_id' => 'bail|nullable|integer',
            'party_id' => 'bail|required|integer',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'debit_limit' => 'bail|required|numeric',
            'credit_limit' => 'bail|required|numeric',
        ];
    }
}
