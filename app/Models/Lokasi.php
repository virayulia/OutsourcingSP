<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "lokasi"; //cek
    protected $primaryKey = "kode_lokasi"; //cek

    protected $fillable = [
        'kode_lokasi',
        'lokasi',
        'jenis'
    ];
    
    public function ump()
    {
        return $this->hasOne(Ump::class, 'kode_lokasi'); // Sesuai ERD
    }
}
