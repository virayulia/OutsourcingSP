<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_lokasi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_lokasi"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_lokasi',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'kode_lokasi');
    }
}
