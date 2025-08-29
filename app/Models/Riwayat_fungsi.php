<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_fungsi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_fungsi"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_fungsi',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function fungsi()
    {
        return $this->belongsTo(Fungsi::class, 'kode_fungsi', 'kode_fungsi');
    }
}
