<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "jabatan"; //cek
    protected $primaryKey = "kode_jabatan"; //cek

    protected $fillable = [
        'kode_jabatan',
        'jabatan',
        'tunjangan_jabatan'
    ];
}
