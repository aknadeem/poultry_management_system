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
}
