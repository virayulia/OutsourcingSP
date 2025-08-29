<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_resiko extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "riwayat_resiko"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'kode_resiko',
        'beg_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function resiko()
    {
        return $this->belongsTo(Resiko::class, 'kode_resiko');
    }
}
