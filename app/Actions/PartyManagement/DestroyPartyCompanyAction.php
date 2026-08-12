<?php

namespace App\Actions\PartyManagement;

use App\Models\PartyCompany;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DestroyPartyCompanyAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(PartyCompany $company): void
    {
        DB::transaction(function () use ($company) {
            $this->uploadService->delete('party/company', $company->company_logo);
            $company->delete();
        });
    }
}
