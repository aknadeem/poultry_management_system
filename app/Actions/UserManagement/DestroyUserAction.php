<?php

namespace App\Actions\UserManagement;

use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyUserAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->uploadService->delete('users', $user->picture);
            $user->delete();
        });
    }
}
