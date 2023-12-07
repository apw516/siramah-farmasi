<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mt_racikan_detail_dummy extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $table = 'mt_racikan_detail';
    protected $connection = 'mysql2';
    protected $guarded = ['id'];
}
