<?php

namespace App\Http\Requests\BalanceManagement;

use Illuminate\Foundation\Http\FormRequest;

class ReversePartyBalancePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reversal_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
