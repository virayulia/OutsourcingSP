<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyesuaian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "penyesuaian"; //cek
    protected $primaryKey = "kode_suai"; //cek

    protected $fillable = [
        'kode_suai',
        'keterangan',
        'tunjangan_penyesuaian'
    ];
}
