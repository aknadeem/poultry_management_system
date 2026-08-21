<?php

namespace App\Http\Requests\InventoryManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeedRequest extends FormRequest
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
            'feed_name' => 'bail|required|string',
            'feed_category_id' => 'bail|required|integer',
        ];
    }
}
