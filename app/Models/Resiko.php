<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resiko extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "resiko"; //cek
    protected $primaryKey = "kode_resiko"; //cek

    protected $fillable = [
        'kode_resiko',
        'resiko',
        'tunjangan_resiko'
    ];
}
