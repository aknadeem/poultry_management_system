<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CountryPCRelationTrait {

    public function country(): BelongsTo
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }
    public function province(): BelongsTo
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'id');
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id');
    }
}