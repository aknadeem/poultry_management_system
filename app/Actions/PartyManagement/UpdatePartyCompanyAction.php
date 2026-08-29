<?php

namespace App\Actions\PartyManagement;

use App\Models\Party;
use App\Models\PartyCompany;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdatePartyCompanyAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(PartyCompany $company, array $data, ?object $imageFile, int $userId): PartyCompany
    {
        return DB::transaction(function () use ($company, $data, $imageFile, $userId) {
            $logo = $this->uploadService->replace(
                $imageFile,
                'party/company',
                $company->company_logo
            );

            $company->update([
                'company_name'    => $data['name'],
                'company_address' => $data['address'],
                'company_logo'    => $logo,
                'updatedby'       => $userId,
            ]);

            if ($company->party_id) {
                Party::where('id', $company->party_id)->update([
                    'name'        => $data['name'],
                    'contact_no'  => $data['contact_no'],
                    'email'       => $data['email'],
                    'address'     => $data['address'],
                    'description' => $data['description'] ?? null,
                    'updatedby'   => $userId,
                ]);
            }

            return $company->fresh();
        });
    }
}
