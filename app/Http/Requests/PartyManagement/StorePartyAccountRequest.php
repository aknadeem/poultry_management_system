<?php

namespace App\Http\Requests\PartyManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePartyAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'party_account_id' => 'bail|nullable|integer',
            'party_id' => 'bail|required|integer',
            'account_title' => 'bail|required|string',
            'account_number' => 'bail|required|string',
            'bank_name' => 'bail|required|string',
            'branch_code' => 'bail|nullable|string',
            'opening_balance' => 'bail|required|numeric',
        ];
    }
}
