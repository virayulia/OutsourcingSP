<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "karyawan"; //cek
    protected $primaryKey = "karyawan_id"; //cek
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'karyawan_id',
        'osis_id',
        'ktp',
        'nama_tk',
        'perusahaan_id',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status',
        'alamat',
        'asal',
        'tanggal_bekerja',
        'tahun_pensiun',
        'tanggal_pensiun',
        'status_aktif',
        'catatan_pengganti',
        'catatan_berhenti',
        'tanggal_berhenti',
        'tunjangan_penyesuaian'

    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }


    public function kuotaJam()
    {
        return $this->hasOne(Kuotajam::class, 'karyawan_id'); // sesuaikan foreign key-nya
    }

    public function masaKerja()
    {
        return $this->hasOne(Masakerja::class, 'karyawan_id'); // sesuaikan foreign key-nya
    }

    public function paketKaryawan()
    {
        return $this->hasMany(PaketKaryawan::class, 'karyawan_id');
    }

    // public function riwayatUnit()
    // {
    //     return $this->hasMany(Riwayat_unit::class, 'karyawan_id');
    // }

    public function riwayatJabatan()
    {
        return $this->hasMany(Riwayat_jabatan::class, 'karyawan_id');
    }

    public function riwayatShift()
    {
        return $this->hasMany(Riwayat_shift::class, 'karyawan_id');
    }

    public function riwayatResiko()
    {
        return $this->hasMany(Riwayat_resiko::class, 'karyawan_id');
    }

    public function riwayatPenyesuaian()
    {
        return $this->hasMany(Riwayat_penyesuaian::class, 'karyawan_id');
    }

    public function riwayatLokasi()
    {
        return $this->hasMany(Riwayat_lokasi::class, 'karyawan_id');
    }

    // relasi ke PaketKaryawan terbaru
    public function paket()
    {
        return $this->hasOne(PaketKaryawan::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke masa_kerja terbaru
    public function masa_kerja()
    {
        return $this->hasOne(Masakerja::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke kuota_jam terbaru
    public function kuota_jam_lama()
    {
        return $this->hasOne(Kuotajam::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke fungsi terbaru
    public function fungsi_aktif()
    {
        return $this->hasOne(Riwayat_fungsi::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke jabatan terbaru
    public function jabatan_aktif()
    {
        return $this->hasOne(Riwayat_jabatan::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke shift terbaru
    public function shift_aktif()
    {
        return $this->hasOne(Riwayat_shift::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke resiko terbaru
    public function resiko_aktif()
    {
        return $this->hasOne(Riwayat_resiko::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    // relasi ke lokasi terbaru
    public function lokasi_aktif()
    {
        return $this->hasOne(Riwayat_lokasi::class, 'karyawan_id')->latestOfMany('beg_date');
    }

    public function pakaianTerakhir()
    {
        return $this->hasOne(Pakaian::class, 'karyawan_id', 'karyawan_id')
                    ->latestOfMany('beg_date');
    }


}
