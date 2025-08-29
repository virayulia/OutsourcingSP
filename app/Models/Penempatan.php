<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penempatan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "penempatan"; //cek
    protected $primaryKey = "penempatan_id"; //cek
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'penempatan_id',
        'unit_id',
        'karyawan_id',
        'kode_jabatan',
        'bidang_id',
        'area_id',
        'kode_harianshift',
        'kode_resiko',
        'kode_suai',
        'kode_lokasi',
        'kode_paket',
        'tunjangan_masakerja',
        'quota_jam_real',
        'tanggal_bekerja',
        'tanggal_selesai',
        'status'
    ];
}
