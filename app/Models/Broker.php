<?php

namespace App\Models;

use App\Interfaces\HasCountryProvinceCity;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Broker extends Model implements HasCountryProvinceCity
{
    protected $fillable = [
        'name',
        'broker_code',
        'guardian_name',
        'cnic_no',
        'email',
        'contact_no',
        'opening_balance',
        'country_id',
        'province_id',
        'city_id',
        'address',
        'is_active',
        'picture',
        'addedby',
        'updatedby',
    ];
    use HasFactory, CountryPCRelationTrait;

    protected static function booted(): void
    {
        static::created(function (Broker $broker) {
            $broker->updateQuietly([
                'broker_code' => 'BRK-' . str_pad((string) $broker->id, 3,'0',STR_PAD_LEFT),
            ]);
        });
    }
}
