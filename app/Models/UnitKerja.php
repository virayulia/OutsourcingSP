<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "unit_kerja"; //cek
    protected $primaryKey = "unit_id"; //cek

    protected $fillable = [
        'unit_id',
        'unit_kerja',
        'fungsi'
    ];

    public function paketUnit()
    {
        return $this->hasMany(Paket::class, 'unit_id', 'unit_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'departemen_id');
    }

}
