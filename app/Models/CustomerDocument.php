<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerDocument extends Model
{
    use SoftDeletes;
    protected $table = 'customer_documents';
    use HasFactory;
}
