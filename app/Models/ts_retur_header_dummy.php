<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ts_retur_header_dummy extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $connection = 'mysql2';
    protected $table = 'ts_retur_header';
    protected $guarded = ['id'];
    // public function ts_layanan_detail(){
    //     return $this->hasMany(ts_layanan_detail_dummy::class,'row_id_header','id');
    // }
}
