<?php

namespace App\Http\Requests\BalanceManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyBalancePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('idempotency_key')) {
            $this->merge([
                'idempotency_key' => (string) \Illuminate\Support\Str::uuid(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'balance_id' => ['bail', 'required', 'integer', 'exists:party_balances,id'],
            'party_id' => ['bail', 'required', 'integer', 'exists:parties,id'],
            'amount_payment' => ['bail', 'required', 'numeric', 'gt:0'],
            'paid_date' => ['bail', 'required', 'date'],
            'payment_option' => ['bail', 'required', 'string', Rule::in(['cash', 'cheque', 'other'])],
            'cheque_date' => ['bail', 'required_if:payment_option,cheque', 'nullable', 'date'],
            'bank_name' => ['bail', 'required_if:payment_option,cheque', 'nullable', 'string', 'max:255'],
            'cheque_picture' => ['bail', 'required_if:payment_option,cheque', 'nullable', 'file', 'mimes:jpeg,jpg,png', 'max:5120'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:5120'],
            'idempotency_key' => ['bail', 'required', 'uuid'],
        ];
    }
}
