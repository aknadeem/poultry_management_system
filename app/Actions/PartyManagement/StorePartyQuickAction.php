<?php

namespace App\Actions\PartyManagement;

use App\Models\Party;
use App\Models\PartyFarm;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePartyQuickAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * Lightweight Party upsert used by customer/vendor AJAX modals.
     *
     * @param  array{is_customer?: int|bool, is_vendor?: int|bool}  $flags
     */
    public function execute(array $data, ?object $imageFile, int $userId, array $flags = []): Party
    {
        return DB::transaction(function () use ($data, $imageFile, $userId, $flags) {
            $partyId = (int) ($data['customer_id_modal'] ?? 0);
            $isCustomer = (int) ($flags['is_customer'] ?? $data['is_customer'] ?? 1);
            $isVendor = (int) ($flags['is_vendor'] ?? $data['is_vendor'] ?? 0);

            if ($partyId > 0) {
                $party = Party::with('farm')->find($partyId);
                if (! $party) {
                    throw ValidationException::withMessages([
                        'customer_id_modal' => 'No party found against this id',
                    ]);
                }

                $picture = $this->uploadService->replace(
                    $imageFile,
                    'party',
                    $party->profile_picture
                );

                $party->update([
                    'name' => $data['name'],
                    'contact_no' => $data['contact_no'],
                    'email' => $data['email'],
                    'address' => $data['address'],
                    'profile_picture' => $picture,
                    'updatedby' => $userId,
                ]);

                if ($isCustomer && ! empty($data['farm_name'])) {
                    $farm = PartyFarm::firstOrNew(['party_id' => $party->id]);
                    $farm->farm_name = $data['farm_name'];
                    $farm->addedby = $farm->exists ? $farm->addedby : $userId;
                    $farm->updatedby = $userId;
                    $farm->save();
                }

                return $party->fresh('farm');
            }

            $picture = $this->uploadService->store($imageFile, 'party');

            $party = Party::create([
                'is_customer' => $isCustomer ?: 0,
                'is_vendor' => $isVendor ?: 0,
                'name' => $data['name'],
                'guardian_name' => $data['name'],
                'cnic_no' => $data['cnic_no'],
                'email' => $data['email'],
                'contact_no' => $data['contact_no'],
                'address' => $data['address'],
                'manual_number' => 'Q-'.time().mt_rand(10, 99),
                'profile_picture' => $picture,
                'addedby' => $userId,
            ]);

            if ($isCustomer && ! empty($data['farm_name'])) {
                $farmPicture = $this->uploadService->store($data['farm_image'], 'party_farm');
                PartyFarm::create([
                    'party_id' => $party->id,
                    'farm_type_id' => $data['farm_type_id'],
                    'farm_subtype_id' => $data['farm_subtype_id'],
                    'farm_name' => $data['farm_name'],
                    'farm_noc' => $data['farm_noc'],
                    'farm_image' => $farmPicture,
                    'farm_address' => $data['farm_address'],
                    'addedby' => $userId,
                ]);
            }

            return $party->fresh('farm');
        });
    }
}
