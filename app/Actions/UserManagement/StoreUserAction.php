<?php

namespace App\Actions\UserManagement;

use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreUserAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, int $authUserId): User
    {
        return DB::transaction(function () use ($data, $imageFile, $authUserId) {
            $picture = $this->uploadService->store($imageFile, 'users');

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_role_id' => $data['user_role_id'],
                'contact_no' => $data['contact_no'],
                'password' => Hash::make($data['password']),
                'picture' => $picture,
                'addedby' => $authUserId,
            ]);
        });
    }
}
