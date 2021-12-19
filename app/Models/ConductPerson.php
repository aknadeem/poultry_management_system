<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConductPerson extends Model
{
    protected $table = 'conduct_people';
    // use HasFactory;
    protected $dates = ['created_at','updated_at'];
    protected $casts = [
        'country_id' => 'integer',
        'province_id' => 'integer',
        'city_id' => 'integer',
    ];

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
