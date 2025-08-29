<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "bidang"; //cek
    protected $primaryKey = "bidang_id"; //cek

    protected $fillable = [
        'bidang_id',
        'unit_id',
        'bidang'
    ];
}
