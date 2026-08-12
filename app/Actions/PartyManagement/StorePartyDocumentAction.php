<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyDocument;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePartyDocumentAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $documentFile, int $userId): PartyDocument
    {
        return DB::transaction(function () use ($data, $documentFile, $userId) {
            $documentId = (int) ($data['party_document_id'] ?? 0);

            if ($documentId > 0) {
                $document = PartyDocument::find($documentId);
                if (! $document) {
                    throw ValidationException::withMessages([
                        'party_document_id' => 'No data found against this id',
                    ]);
                }

                $filename = $this->uploadService->replace(
                    $documentFile,
                    'party/documents',
                    $document->document_name
                );

                $document->update([
                    'party_id' => $data['party_id'],
                    'title' => $data['document_title'],
                    'document_name' => $filename,
                    'updatedby' => $userId,
                ]);

                return $document->fresh();
            }

            $filename = $this->uploadService->store($documentFile, 'party/documents');

            return PartyDocument::create([
                'party_id' => $data['party_id'],
                'title' => $data['document_title'],
                'document_name' => $filename,
                'addedby' => $userId,
            ]);
        });
    }
}
