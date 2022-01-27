<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoultryShed extends Model
{
    use SoftDeletes;
    protected $table = 'poultry_sheds';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];
}
