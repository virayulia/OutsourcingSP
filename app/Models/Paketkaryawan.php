<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paketkaryawan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "paket_karyawan"; //cek
    protected $primaryKey = "paket_karyawan_id"; //cek

    protected $fillable = [
        'paket_karyawan_id',
        'paket_id',
        'karyawan_id',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }
}
