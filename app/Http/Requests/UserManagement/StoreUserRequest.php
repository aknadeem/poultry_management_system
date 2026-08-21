<?php

namespace App\Http\Requests\UserManagement;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $user = $this->route('user');
        if ($user instanceof User) {
            $this->merge([
                'user_id_modal' => $user->id,
            ]);
        }
    }

    public function rules(): array
    {
        $userId = (int) ($this->input('user_id_modal') ?? 0);
        $isUpdate = $userId > 0;

        return [
            'user_id_modal' => ['nullable', 'integer'],
            'name' => ['bail', 'required', 'string'],
            'user_role_id' => ['bail', 'required', 'integer', 'exists:user_roles,id'],
            'email' => [
                'bail',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($isUpdate ? $userId : null),
            ],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:4'],
            'contact_no' => ['bail', 'required', 'string'],
            'image_file' => ['nullable', 'mimes:jpeg,jpg,png', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'image_file.max' => 'Maximum Image size to upload is 5MB (5000KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB',
        ];
    }

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
