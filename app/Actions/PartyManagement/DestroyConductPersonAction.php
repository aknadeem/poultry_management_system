<?php

namespace App\Actions\PartyManagement;

use App\Models\ConductPerson;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyConductPersonAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ConductPerson $person): void
    {
        DB::transaction(function () use ($person) {
            $this->uploadService->delete('conduct_persons', $person->picture);
            $person->delete();
        });
    }
}
