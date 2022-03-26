<?php

namespace App\Models;

use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\HasCountryProvinceCity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model implements HasCountryProvinceCity
{
    use SoftDeletes, CountryPCRelationTrait;
    protected $table = 'employees';
    use HasFactory;
    protected $dates = ['created_at','updated_at', 'date_of_birth'];
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
    ];

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_number = Employee::max('emp_number');
            
            if($ex_number >= 99999)
                $length = 6;
            else if($ex_number >= 999999)
                $length = 7;
            else if($ex_number >= 9999999)
                $length = 8;
            else if($ex_number >= 99999999)
                $length = 9;
            else
                $length = 5;

            $model->emp_number = $ex_number+1;
            $new_number = str_pad($ex_number, $length, 0, STR_PAD_LEFT)+1;
            $model->emp_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }
}
