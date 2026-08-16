<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConductPerson extends Model
{
    use SoftDeletes;
    protected $guarded = [];


    protected $table = 'conduct_people';
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

    protected static function booted(): void
    {
        static::created(function (ConductPerson $person) {
            $person->updateQuietly([
                'person_code' => 'CP-' . str_pad((string) $person->id, 3,'0',STR_PAD_LEFT),
            ]);
        });
    }
}
