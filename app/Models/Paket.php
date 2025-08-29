<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "paket"; //cek
    protected $primaryKey = "paket_id"; //cek

    protected $fillable = [
        'paket_id',
        'paket',
        'kuota_paket',
        'unit_id'
    ];

    public function paketKaryawan()
    {
        return $this->hasMany(PaketKaryawan::class, 'paket_id', 'paket_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }
}
