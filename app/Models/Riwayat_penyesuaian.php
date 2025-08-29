<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_penyesuaian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_penyesuaian"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_suai',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function penyesuaian()
    {
        return $this->belongsTo(Penyesuaian::class, 'kode_suai');
    }
}
