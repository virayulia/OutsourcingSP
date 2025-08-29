<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "area"; //cek
    protected $primaryKey = "area_id"; //cek

    protected $fillable = [
        'area_id',
        'area'
    ];
}
