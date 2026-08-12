<?php

namespace App\Actions\PartyManagement;

use App\Models\Broker;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class StoreBrokerAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, int $userId): Broker
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $picture = $this->uploadService->store($imageFile, 'brokers');

            return Broker::create([
                'name' => $data['name'],
                'guardian_name' => $data['guardian_name'],
                'cnic_no' => $data['cnic_no'],
                'email' => $data['email'],
                'contact_no' => $data['contact_number'],
                'country_id' => $data['country_id'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'address' => $data['address'] ?? null,
                'picture' => $picture,
                'addedby' => $userId,
            ]);
        });
    }
}
