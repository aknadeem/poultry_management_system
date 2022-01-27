<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    public $timestamps = false;

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'id');
    }
}
