<?php

namespace App\Models;

use App\Interfaces\HasCountryProvinceCity;
use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model implements HasCountryProvinceCity
{
    use SoftDeletes, CountryPCRelationTrait;
    use HasFactory;

    protected $fillable = [
        'emp_number',
        'emp_code',
        'general_type',
        'name',
        'guardian_name',
        'contact_no',
        'other_number',
        'email',
        'cnic_no',
        'father_cnic_no',
        'basic_salary',
        'other_amount',
        'net_salary',
        'contract_period',
        'date_of_birth',
        'employee_age',
        'joining_date',
        'is_active',
        'is_police_record',
        'address',
        'status',
        'description',
        'blood_group',
        'employee_image',
        'employee_signature',
        'personal_farm_id',
        'employee_type_id',
        'employee_level_id',
        'employee_allowance_id',
        'country_id',
        'province_id',
        'city_id',
        'addedby',
        'updatedby',
    ];

    protected $table = 'employees';

    protected $dates = ['created_at','updated_at', 'date_of_birth'];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(PersonalFarm::class, 'personal_farm_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(EmployeeLevel::class, 'employee_level_id');
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Employee $model): void {
            $ex_number = Employee::max('emp_number');

            if ($ex_number >= 99999) {
                $length = 6;
            } elseif ($ex_number >= 999999) {
                $length = 7;
            } elseif ($ex_number >= 9999999) {
                $length = 8;
            } elseif ($ex_number >= 99999999) {
                $length = 9;
            } else {
                $length = 5;
            }

            $model->emp_number = $ex_number+1;
            $new_number = str_pad($ex_number, $length, 0, STR_PAD_LEFT)+1;
            $model->emp_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }
}
