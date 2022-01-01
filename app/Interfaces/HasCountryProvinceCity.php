<?php
namespace App\Interfaces;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasCountryProvinceCity
{
    public function country(): BelongsTo;
    public function province(): BelongsTo;
    public function city(): BelongsTo;
}   