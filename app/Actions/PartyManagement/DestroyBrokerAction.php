<?php

namespace App\Actions\PartyManagement;

use App\Models\Broker;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyBrokerAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(Broker $broker): void
    {
        DB::transaction(function () use ($broker) {
            $this->uploadService->delete('brokers', $broker->picture);
            $broker->delete();
        });
    }
}
