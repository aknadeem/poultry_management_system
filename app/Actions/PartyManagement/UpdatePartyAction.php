<?php

namespace App\Actions\PartyManagement;

use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\PartyFarm;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UpdatePartyAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, ?object>  $files
     */
    public function execute(Party $party, array $data, array $files, int $userId): Party
    {
        return DB::transaction(function () use ($party, $data, $files, $userId) {
            $isCustomer = (int) ($data['is_customer'] ?? 0);
            $isVendor = (int) ($data['is_vendor'] ?? 0);
            $party->update([
                'is_vendor' => $isVendor,
                'is_customer' => $isCustomer,
                'name' => $data['name'],
                'guardian_name' => $data['guardian_name'],
                'cnic_no' => $data['cnic_no'],
                'email' => $data['email'] ?? null,
                'contact_no' => $data['contact_no'],
                'business_no' => $data['business_no'] ?? null,
                'manual_number' => $data['manual_number'],
                'address' => $data['address'] ?? null,
                'customer_type_id' => $data['customer_type_id'] ?? null,
                'vendor_type_id' => $data['vendor_type_id'] ?? null,
                'customer_division_id' => $data['customer_division_id'] ?? null,
                'vendor_division_id' => $data['vendor_division_id'] ?? null,
                'description' => $data['description'] ?? null,
                'country_id' => $data['country_id'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'contact_person_id' => $data['contact_person_id'] ?? null,
                'balance' => $data['opening_balance'] ?? null,
                'balance_type' => $data['balance_type'] ?? null,
                'updatedby' => $userId,
            ]);

            // Store new uploads without deleting existing files (preserves prior media).
            $imageUpdates = array_filter([
                'profile_picture' => $this->uploadService->store($files['profile_picture'] ?? null, 'party'),
                'cnic_front' => $this->uploadService->store($files['cnic_front'] ?? null, 'party'),
                'cnic_back' => $this->uploadService->store($files['cnic_back'] ?? null, 'party'),
                'signature' => $this->uploadService->store($files['signature_image'] ?? null, 'party'),
            ], fn ($value) => $value !== null);

            if ($imageUpdates !== []) {
                DB::table('parties')
                    ->where('id', $party->id)
                    ->update($imageUpdates + ['updatedby' => $userId]);
            }

            if ($isCustomer) {
                $farm = PartyFarm::firstOrNew(['party_id' => $party->id]);
                $farm->fill([
                    'farm_type_id' => $data['farm_type_id'] ?? null,
                    'farm_subtype_id' => $data['farm_subtype_id'] ?? null,
                    'farm_name' => $data['farm_name'] ?? null,
                    'farm_noc' => $data['farm_noc'] ?? null,
                    'farm_address' => $data['farm_address'] ?? null,
                ]);

                $farmImage = $this->uploadService->store($files['farm_image'] ?? null, 'party/farm');
                if ($farmImage !== null) {
                    $farm->farm_image = $farmImage;
                }

                if ($farm->exists) {
                    $farm->updatedby = $userId;
                } else {
                    $farm->addedby = $userId;
                }

                $farm->save();
            }

            if ($isVendor) {
                $company = PartyCompany::firstOrNew(['party_id' => $party->id]);
                $company->fill([
                    'company_name' => $data['company_name'] ?? null,
                    'business_type_id' => $data['business_type_id'] ?? null,
                    'company_address' => $data['company_address'] ?? null,
                ]);

                $companyLogo = $this->uploadService->store($files['company_logo'] ?? null, 'party/company');
                if ($companyLogo !== null) {
                    $company->company_logo = $companyLogo;
                }

                if ($company->exists) {
                    $company->updatedby = $userId;
                } else {
                    $company->addedby = $userId;
                }

                $company->save();
            }

            return $party->fresh();
        });
    }
}
