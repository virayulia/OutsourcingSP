<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_jabatan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_jabatan"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_jabatan',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan');
    }
}
