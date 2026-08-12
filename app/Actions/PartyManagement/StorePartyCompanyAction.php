<?php

namespace App\Actions\PartyManagement;

use App\Models\BusinessType;
use App\Models\Party;
use App\Models\PartyCompany;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePartyCompanyAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * Upsert a PartyCompany (and vendor Party on create) from the company modal fields.
     */
    public function execute(array $data, ?object $imageFile, int $userId): PartyCompany
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $companyId = (int) ($data['company_id_modal'] ?? 0);

            if ($companyId > 0) {
                $company = PartyCompany::with('vendor')->find($companyId);
                if (! $company) {
                    throw ValidationException::withMessages([
                        'company_id_modal' => 'No Company detail found against this id',
                    ]);
                }

                $logo = $this->uploadService->replace(
                    $imageFile,
                    'party/company',
                    $company->company_logo
                );

                $company->update([
                    'company_name' => $data['name'],
                    'company_address' => $data['address'],
                    'company_logo' => $logo,
                    'updatedby' => $userId,
                ]);

                if ($company->party_id) {
                    Party::where('id', $company->party_id)->update([
                        'name' => $data['name'],
                        'contact_no' => $data['contact_no'],
                        'email' => $data['email'],
                        'address' => $data['address'],
                        'description' => $data['description'] ?? null,
                        'updatedby' => $userId,
                    ]);
                }

                return $company->fresh();
            }

            $businessTypeId = $data['business_type_id']
                ?? BusinessType::query()->value('id');

            if (! $businessTypeId) {
                throw ValidationException::withMessages([
                    'business_type_id' => 'A business type is required to create a company',
                ]);
            }

            $partyId = $data['party_id'] ?? null;
            if (! $partyId) {
                $party = Party::create([
                    'is_vendor' => 1,
                    'name' => $data['name'],
                    'contact_no' => $data['contact_no'],
                    'email' => $data['email'],
                    'address' => $data['address'],
                    'description' => $data['description'] ?? null,
                    'cnic_no' => '0000000000000',
                    'guardian_name' => $data['name'],
                    'manual_number' => 'AUTO-'.time(),
                    'country_id' => null,
                    'province_id' => null,
                    'city_id' => null,
                    'addedby' => $userId,
                ]);
                $partyId = $party->id;
            }

            $logo = $this->uploadService->store($imageFile, 'party/company');

            return PartyCompany::create([
                'party_id' => $partyId,
                'company_name' => $data['name'],
                'company_address' => $data['address'],
                'company_logo' => $logo,
                'business_type_id' => $businessTypeId,
                'addedby' => $userId,
            ]);
        });
    }
}
