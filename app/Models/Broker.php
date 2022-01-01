<?php

namespace App\Models;

use App\Interfaces\HasCountryProvinceCity;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Broker extends Model implements HasCountryProvinceCity
{
    use HasFactory, CountryPCRelationTrait;
}
