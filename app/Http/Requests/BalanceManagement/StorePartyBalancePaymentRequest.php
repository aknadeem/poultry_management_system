<?php

namespace App\Http\Requests\BalanceManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePartyBalancePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'balance_id' => 'bail|required|integer',
            'party_id' => 'bail|required|integer',
            'amount_payment' => 'bail|required|numeric',
            'paid_date' => 'bail|required|date',
            'payment_option' => 'bail|required|string',
            'cheque_date' => 'bail|required_if:payment_option,cheque|nullable|date',
            'bank_name' => 'bail|required_if:payment_option,cheque|nullable|string',
            'cheque_picture' => 'bail|required_if:payment_option,cheque|nullable',
            'description' => 'nullable',
            'image_file' => 'nullable',
        ];
    }
}
