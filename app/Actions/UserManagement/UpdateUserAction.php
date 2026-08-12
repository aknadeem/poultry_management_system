<?php

namespace App\Actions\UserManagement;

use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(User $user, array $data, ?object $imageFile, int $authUserId): User
    {
        return DB::transaction(function () use ($user, $data, $imageFile, $authUserId) {
            $picture = $this->uploadService->replace($imageFile, 'users', $user->picture);

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'user_role_id' => $data['user_role_id'],
                'contact_no' => $data['contact_no'],
                'picture' => $picture,
                'updatedby' => $authUserId,
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $user->update($payload);

            return $user->fresh();
        });
    }
}
