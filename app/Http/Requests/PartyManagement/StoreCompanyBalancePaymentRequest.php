<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyBalancePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_balance_id' => 'bail|required|integer',
            'party_company_id' => 'bail|required|integer',
            'amount_payment' => 'bail|required|numeric',
            'payment_option' => 'bail|required|string',
            'cheque_date' => 'bail|required_if:payment_option,cheque|date',
            'bank_name' => 'bail|required_if:payment_option,cheque|string',
            'cheque_picture' => 'bail|required_if:payment_option,cheque',
            'description' => 'nullable',
            'image_file' => 'nullable',
        ];
    }
}
