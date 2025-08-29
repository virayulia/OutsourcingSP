<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "departemen"; //cek
    protected $primaryKey = "departemen_id"; //cek

    protected $fillable = [
        'departemen_id',
        'departemen',
        'is_si'
    ];

}
