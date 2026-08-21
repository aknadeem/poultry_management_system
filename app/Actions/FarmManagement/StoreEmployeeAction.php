<?php

namespace App\Actions\FarmManagement;

use App\Models\Employee;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class StoreEmployeeAction
{
    public function __construct(
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, ?object $signatureFile, int $userId): Employee
    {
        $employeeImage = null;
        $employeeSignature = null;

        try {
            $employeeImage = $this->uploadService->store($imageFile, 'employee');
            $employeeSignature = $this->uploadService->store($signatureFile, 'employee');

            return DB::transaction(function () use ($data, $employeeImage, $employeeSignature, $userId): Employee {
                return Employee::query()->create([
                    'personal_farm_id' => $data['personal_farm_id'] ?? null,
                    'employee_type_id' => $data['employee_type_id'] ?? null,
                    'employee_level_id' => $data['employee_level_id'],
                    'name' => $data['name'],
                    'guardian_name' => $data['guardian_name'],
                    'contact_no' => $data['contact_no'],
                    'other_number' => $data['other_number'] ?? null,
                    'email' => $data['email'],
                    'cnic_no' => $data['cnic_no'],
                    'father_cnic_no' => $data['father_cnic_no'] ?? null,
                    'basic_salary' => $data['basic_salary'],
                    'other_amount' => $data['other_amount'] ?? null,
                    'net_salary' => $data['net_salary'],
                    'contract_period' => $data['contract_period'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'joining_date' => $data['joining_date'] ?? null,
                    'is_police_record' => $data['is_police_record'] ?? null,
                    'address' => $data['address'],
                    'description' => $data['description'] ?? null,
                    'blood_group' => $data['blood_group'] ?? null,
                    'country_id' => $data['country_id'],
                    'province_id' => $data['province_id'],
                    'city_id' => $data['city_id'],
                    'employee_image' => $employeeImage,
                    'employee_signature' => $employeeSignature,
                    'addedby' => $userId,
                ]);
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('employee', $employeeImage);
            $this->uploadService->delete('employee', $employeeSignature);

            throw $exception;
        }
    }
}
