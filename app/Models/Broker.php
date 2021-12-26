<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    use HasFactory;

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
