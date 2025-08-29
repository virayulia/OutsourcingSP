<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_shift extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_shift"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_harianshift',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function harianShift()
    {
        return $this->belongsTo(Harianshift::class, 'kode_harianshift');
    }
}
