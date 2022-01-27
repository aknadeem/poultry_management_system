<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use SoftDeletes;
    protected $table = 'suppliers';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];
}
