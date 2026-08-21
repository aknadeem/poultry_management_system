<?php

namespace App\Actions\FarmManagement;

use App\Models\PersonalFarm;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdatePersonalFarmAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(
        PersonalFarm $farm,
        array $data,
        ?UploadedFile $imageFile,
        int $userId,
    ): PersonalFarm {
        $existingFarmImage = $farm->farm_image;
        $stagedFarmImage = null;

        try {
            if ($imageFile) {
                $stagedFarmImage = $this->uploadService->store($imageFile, 'personalfarms');
            }

            $updatedFarm = DB::transaction(function () use ($farm, $data, $existingFarmImage, $stagedFarmImage, $userId): PersonalFarm {
                $farm->update([
                    'farm_type_id' => $data['farm_type_id'],
                    'farm_subtype_id' => $data['farm_subtype_id'],
                    'farm_name' => $data['farm_name'],
                    'farm_noc' => $data['farm_noc'],
                    'farm_area' => $data['farm_area'],
                    'farm_capacity' => $data['farm_capacity'],
                    'feed_room_size' => $data['feed_room_size'],
                    'farm_image' => $stagedFarmImage ?? $existingFarmImage,
                    'farm_address' => $data['farm_address'] ?? null,
                    'country_id' => $data['country_id'],
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'updatedby' => $userId,
                ]);

                return $farm->fresh();
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('personalfarms', $stagedFarmImage);

            throw $exception;
        }

        if ($stagedFarmImage) {
            $this->uploadService->delete('personalfarms', $existingFarmImage);
        }

        return $updatedFarm;
    }
}
