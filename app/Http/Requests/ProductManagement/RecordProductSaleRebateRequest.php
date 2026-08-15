<?php

namespace App\Http\Requests\ProductManagement;

use Illuminate\Foundation\Http\FormRequest;

class RecordProductSaleRebateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_page'         => 'required|string|in:ProductSaleDetail,ProductPurchaseDetail',
            'product_detail_id' => 'required|integer|min:1',
            'rebate_qty'        => 'required|integer|min:1',
            'rebate_reason'     => 'nullable|string|max:500',
            'rebate_description' => 'nullable|string|max:1000',
        ];
    }
}
