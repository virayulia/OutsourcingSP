<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harianshift extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "harianshift"; //cek
    protected $primaryKey = "kode_harianshift"; //cek

    protected $fillable = [
        'kode_harianshift',
        'harianshift',
        'tunjangan_shift'
    ];
}
