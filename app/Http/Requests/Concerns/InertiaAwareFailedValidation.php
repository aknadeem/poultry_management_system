<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait InertiaAwareFailedValidation
{
    protected function failedValidation(Validator $validator): void
    {
        if ($this->header('X-Inertia')) {
            parent::failedValidation($validator);
        }

        throw new HttpResponseException(response()->json([
            'error' => $validator->errors()->toArray(),
            'success' => 'no',
        ], 201));
    }
}
