<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fungsi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "fungsi"; //cek
    protected $primaryKey = "kode_fungsi"; //cek

    protected $fillable = [
        'kode_fungsi',
        'fungsi',
        'keterangan'
    ];
}
