<?php

namespace App\Actions\FarmManagement;

use App\Models\PersonalFarm;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StorePersonalFarmAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, UploadedFile $imageFile, int $userId): PersonalFarm
    {
        $farmImage = null;

        try {
            $farmImage = $this->uploadService->store($imageFile, 'personalfarms');

            return DB::transaction(function () use ($data, $farmImage, $userId): PersonalFarm {
                return PersonalFarm::query()->create([
                    'farm_type_id' => $data['farm_type_id'],
                    'farm_subtype_id' => $data['farm_subtype_id'],
                    'farm_name' => $data['farm_name'],
                    'farm_noc' => $data['farm_noc'],
                    'farm_area' => $data['farm_area'],
                    'farm_capacity' => $data['farm_capacity'],
                    'feed_room_size' => $data['feed_room_size'],
                    'farm_image' => $farmImage,
                    'farm_address' => $data['farm_address'] ?? null,
                    'country_id' => $data['country_id'],
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'addedby' => $userId,
                ]);
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('personalfarms', $farmImage);

            throw $exception;
        }
    }
}
