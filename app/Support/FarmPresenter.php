<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\PartyFarm;
use App\Models\PersonalFarm;
use App\Models\VaccinationSchedule;

class FarmPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function personalFarm(PersonalFarm $farm): array
    {
        $farm->loadMissing(['type', 'subtype', 'country', 'province', 'city']);

        return [
            'id' => $farm->id,
            'farm_type_id' => $farm->farm_type_id,
            'farm_type_name' => $farm->type?->name,
            'farm_subtype_id' => $farm->farm_subtype_id,
            'farm_subtype_name' => $farm->subtype?->name,
            'farm_name' => $farm->farm_name,
            'farm_code' => $farm->farm_code,
            'farm_noc' => $farm->farm_noc,
            'farm_area' => $farm->farm_area,
            'farm_capacity' => $farm->farm_capacity,
            'feed_room_size' => $farm->feed_room_size,
            'farm_address' => $farm->farm_address,
            'farm_image' => $farm->farm_image,
            'farm_image_url' => $farm->farm_image
                ? asset('storage/personalfarms/'.$farm->farm_image)
                : null,
            'country_id' => $farm->country_id,
            'province_id' => $farm->province_id,
            'city_id' => $farm->city_id,
            'country_name' => $farm->country?->name,
            'province_name' => $farm->province?->name,
            'city_name' => $farm->city?->name,
            'is_active' => (bool) $farm->is_active,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function customerFarm(PartyFarm $farm): array
    {
        $farm->loadMissing(['party', 'type', 'subtype']);

        return [
            'id' => $farm->id,
            'party_id' => $farm->party_id,
            'party_name' => $farm->party?->name,
            'party_cnic_no' => $farm->party?->cnic_no,
            'farm_type_id' => $farm->farm_type_id,
            'farm_type_name' => $farm->type?->name,
            'farm_subtype_id' => $farm->farm_subtype_id,
            'farm_subtype_name' => $farm->subtype?->name,
            'farm_name' => $farm->farm_name,
            'farm_code' => $farm->farm_code,
            'farm_noc' => $farm->farm_noc,
            'farm_area' => $farm->farm_area,
            'farm_capacity' => $farm->farm_capacity,
            'feed_room_size' => $farm->feed_room_size,
            'farm_address' => $farm->farm_address,
            'farm_image' => $farm->farm_image,
            'farm_image_url' => $farm->farm_image
                ? asset('storage/party/farm/'.$farm->farm_image)
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function employee(Employee $employee): array
    {
        $employee->loadMissing(['farm', 'type', 'level', 'country', 'province', 'city']);

        return [
            'id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'personal_farm_id' => $employee->personal_farm_id,
            'farm_name' => $employee->farm?->farm_name,
            'employee_type_id' => $employee->employee_type_id,
            'employee_type_name' => $employee->type?->name,
            'employee_level_id' => $employee->employee_level_id,
            'employee_level_name' => $employee->level?->name,
            'name' => $employee->name,
            'guardian_name' => $employee->guardian_name,
            'contact_no' => $employee->contact_no,
            'other_number' => $employee->other_number,
            'email' => $employee->email,
            'cnic_no' => $employee->cnic_no,
            'father_cnic_no' => $employee->father_cnic_no,
            'basic_salary' => $employee->basic_salary,
            'other_amount' => $employee->other_amount,
            'net_salary' => $employee->net_salary,
            'contract_period' => $employee->contract_period,
            'date_of_birth' => $employee->date_of_birth?->format('Y-m-d'),
            'joining_date' => $employee->joining_date,
            'is_police_record' => (bool) $employee->is_police_record,
            'address' => $employee->address,
            'blood_group' => $employee->blood_group,
            'description' => $employee->description,
            'country_id' => $employee->country_id,
            'province_id' => $employee->province_id,
            'city_id' => $employee->city_id,
            'country_name' => $employee->country?->name,
            'province_name' => $employee->province?->name,
            'city_name' => $employee->city?->name,
            'employee_image' => $employee->employee_image,
            'employee_image_url' => $employee->employee_image
                ? asset('storage/employee/'.$employee->employee_image)
                : null,
            'employee_signature' => $employee->employee_signature,
            'employee_signature_url' => $employee->employee_signature
                ? asset('storage/employee/'.$employee->employee_signature)
                : null,
            'is_active' => (bool) $employee->is_active,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function vaccinationSchedule(VaccinationSchedule $schedule): array
    {
        $schedule->loadMissing(['farm', 'product']);

        return [
            'id' => $schedule->id,
            'party_farm_id' => $schedule->party_farm_id,
            'farm_name' => $schedule->farm?->farm_name,
            'product_id' => $schedule->product_id,
            'product_code' => $schedule->product?->product_code,
            'product_name' => $schedule->product?->product_name,
            'schedule_date' => $schedule->schedule_date?->format('Y-m-d'),
            'description' => $schedule->description,
            'is_vaccinated' => (bool) $schedule->is_vaccinated,
            'vaccination_date' => $schedule->vaccination_date?->format('Y-m-d'),
            'vaccinated_remarks' => $schedule->vaccinated_remarks,
            'is_active' => (bool) $schedule->is_active,
        ];
    }
}
