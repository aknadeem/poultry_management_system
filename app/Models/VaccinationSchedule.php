<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VaccinationSchedule extends Model
{
    use SoftDeletes, HasFactory;
    protected $guarded = [];

    protected $table = 'vaccination_schedules';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'schedule_date' => 'date',
        'vaccination_date' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo('App\Models\PartyFarm', 'party_farm_id', 'id')->withDefault(['id' => null]);
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->withDefault(['id' => null]);
    }

}
