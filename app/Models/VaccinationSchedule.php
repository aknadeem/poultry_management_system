<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VaccinationSchedule extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'vaccination_schedules';

    protected $dates = ['created_at','updated_at','deleted_at', 'schedule_date','vaccination_date'];

    // protected $casts = [];


    public function farm()
    {
        return $this->belongsTo('App\Models\PartyFarm', 'party_farm_id', 'id')->withDefault(['id' => null]);
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->withDefault(['id' => null]);
    }

}
