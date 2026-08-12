<?php

namespace App\Actions\PartyManagement;

use App\Models\ConductPerson;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UpdateConductPersonAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ConductPerson $person, array $data, ?object $imageFile, int $userId): ConductPerson
    {
        return DB::transaction(function () use ($person, $data, $imageFile, $userId) {
            $picture = $this->uploadService->replace(
                $imageFile,
                'conduct_persons',
                $person->picture
            );

            $person->update([
                'name' => $data['name'],
                'guardian_name' => $data['guardian_name'],
                'cnic_no' => $data['cnic_no'],
                'email' => $data['email'],
                'contact_number' => $data['contact_number'],
                'country_id' => $data['country_id'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'address' => $data['address'] ?? null,
                'picture' => $picture,
                'updatedby' => $userId,
            ]);

            return $person->fresh();
        });
    }
}
