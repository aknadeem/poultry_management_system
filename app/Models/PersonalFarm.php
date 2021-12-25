<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalFarm extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'personal_farms';
    protected $dates = ['created_at','updated_at', 'deleted_at'];

    public function type()
    {
        return $this->belongsTo('App\Models\FarmType', 'farm_type_id', 'id');
    }
    public function subtype()
    {
        return $this->belongsTo('App\Models\FarmSubtype', 'farm_subtype_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id');
    }
}
