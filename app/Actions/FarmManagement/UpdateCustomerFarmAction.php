<?php

namespace App\Actions\FarmManagement;

use App\Models\PartyFarm;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UpdateCustomerFarmAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(PartyFarm $farm, array $data, ?object $imageFile, int $userId): PartyFarm
    {
        return DB::transaction(function () use ($farm, $data, $imageFile, $userId) {
            $farmImage = $this->uploadService->replace(
                $imageFile,
                'party/farm',
                $farm->farm_image
            );

            $farm->update([
                'farm_type_id' => $data['farm_type_id'],
                'farm_subtype_id' => $data['farm_subtype_id'],
                'farm_name' => $data['farm_name'],
                'farm_noc' => $data['farm_noc'],
                'farm_address' => $data['farm_address'],
                'farm_area' => $data['farm_area'] ?? null,
                'feed_room_size' => $data['feed_room_size'] ?? null,
                'farm_capacity' => $data['farm_capacity'] ?? null,
                'farm_image' => $farmImage,
                'updatedby' => $userId,
            ]);

            return $farm->fresh(['party:id,name,cnic_no', 'type:id,name', 'subtype:id,name']);
        });
    }
}
