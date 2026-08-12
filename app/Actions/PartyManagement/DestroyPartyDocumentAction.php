<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyDocument;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyPartyDocumentAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(PartyDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $this->uploadService->delete('party/documents', $document->document_name);
            $document->delete();
        });
    }
}
