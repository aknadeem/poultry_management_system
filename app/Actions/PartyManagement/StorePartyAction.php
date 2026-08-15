<?php

namespace App\Actions\PartyManagement;

use App\Models\Party;
use App\Models\PartyCompany;
use App\Models\PartyFarm;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class StorePartyAction
{
    public function __construct(
        private FileUploadService $uploadService,
        private FinancialBalanceService $balanceService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, ?object>  $files
     */
    public function execute(array $data, array $files, int $userId): Party
    {
        return DB::transaction(function () use ($data, $files, $userId) {
            $isCustomer = (int) ($data['is_customer'] ?? 0);
            $isVendor = (int) ($data['is_vendor'] ?? 0);

            $party = Party::create([
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
                'addedby' => $userId,
            ]);

            $images = [
                'profile_picture' => $this->uploadService->store($files['profile_picture'] ?? null, 'party'),
                'cnic_front' => $this->uploadService->store($files['cnic_front'] ?? null, 'party'),
                'cnic_back' => $this->uploadService->store($files['cnic_back'] ?? null, 'party'),
                'signature' => $this->uploadService->store($files['signature_image'] ?? null, 'party'),
            ];

            DB::table('parties')
                ->where('id', $party->id)
                ->update($images + ['addedby' => $userId]);

            if ($isCustomer) {
                $farmImage = $this->uploadService->store($files['farm_image'] ?? null, 'party/farm');

                PartyFarm::create([
                    'party_id' => $party->id,
                    'farm_type_id' => $data['farm_type_id'] ?? null,
                    'farm_subtype_id' => $data['farm_subtype_id'] ?? null,
                    'farm_name' => $data['farm_name'] ?? null,
                    'farm_noc' => $data['farm_noc'] ?? null,
                    'farm_image' => $farmImage,
                    'farm_address' => $data['farm_address'] ?? null,
                    'addedby' => $userId,
                ]);
            }

            if ($isVendor) {
                $companyLogo = $this->uploadService->store($files['company_logo'] ?? null, 'party/company');

                PartyCompany::create([
                    'party_id' => $party->id,
                    'company_name' => $data['company_name'] ?? null,
                    'business_type_id' => $data['business_type_id'] ?? null,
                    'company_logo' => $companyLogo,
                    'company_address' => $data['company_address'] ?? null,
                    'addedby' => $userId,
                ]);
            }

            $openingBalance = $data['opening_balance'] ?? null;
            if ($openingBalance !== null && $openingBalance !== '' && (float) $openingBalance > 0) {
                $this->balanceService->recordOpeningPartyBalance(
                    $party->id,
                    (float) $openingBalance,
                    $data['balance_type'] ?? null,
                    $userId
                );
            }

            return $party->fresh();
        });
    }
}
