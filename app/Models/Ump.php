<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ump extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ump"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'tahun',
        'kode_lokasi',
        'ump'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'kode_lokasi');
    }
}
