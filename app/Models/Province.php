<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany('App\Models\City', 'province_id', 'id');
    }
}
