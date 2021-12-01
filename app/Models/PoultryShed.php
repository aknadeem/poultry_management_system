<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoultryShed extends Model
{
    protected $table = 'poultry_sheds';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];
}
