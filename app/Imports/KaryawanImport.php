<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\Kuotajam;
use App\Models\Masakerja;
use App\Models\PaketKaryawan;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_lokasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanImport implements ToCollection
{
    private $total = 0;
    private $gagal = 0;

    public function getTotal()
    {
        return $this->total;
    }

    public function getGagal()
    {
        return $this->gagal;
    }

    public function collection(Collection $rows)
    {
        // Lewati baris header
        unset($rows[0]);

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $this->total++;

                $osis_id_lama = trim($row[0]);
                $osis_id_baru = trim($row[1]);

                // Ambil data karyawan lama
                $karyawan_lama = Karyawan::with([
                    'kuota_jam_lama',
                    'masa_kerja',
                    'paket',
                    'fungsi_aktif',
                    'shift_aktif',
                    'resiko_aktif',
                    'lokasi_aktif',
                ])->where('osis_id', $osis_id_lama)->first();

                // Cek data penting, jika ada yang kosong maka skip
                if (
                    !$karyawan_lama ||
                    !$karyawan_lama->perusahaan_id ||
                    !$karyawan_lama->kuota_jam_lama ||
                    !$karyawan_lama->masa_kerja ||
                    !$karyawan_lama->paket ||
                    !$karyawan_lama->fungsi_aktif ||
                    !$karyawan_lama->shift_aktif ||
                    !$karyawan_lama->resiko_aktif ||
                    !$karyawan_lama->lokasi_aktif
                ) {
                    $this->gagal++;
                    continue;
                }

                $tanggal_lahir = Date::excelToDateTimeObject($row[4])->format('Y-m-d');
                $tanggal_bekerja = Date::excelToDateTimeObject($row[10])->format('Y-m-d');
                
                $tahun_pensiun = Carbon::parse($tanggal_lahir)->addYears(56);
                $tanggal_pensiun = $tahun_pensiun->copy()->addMonthNoOverflow()->startOfMonth()->toDateString();

                // Simpan data pribadi karyawan baru
                $karyawan_baru = Karyawan::create([
                    'osis_id' => $osis_id_baru,
                    'ktp' => $row[2],
                    'nama_tk' => $row[3],
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $row[5],
                    'agama' => $row[6],
                    'status' => $row[7],
                    'alamat' => $row[8],
                    'asal' => $row[9],
                    'tanggal_bekerja' => $tanggal_bekerja,
                    'tahun_pensiun' => $tahun_pensiun,
                    'tanggal_pensiun' => $tanggal_pensiun,
                    'status_aktif' => 'Aktif',
                    'tunjangan_penyesuaian' => 0,
                    'perusahaan_id' => $karyawan_lama->perusahaan_id,
                    'catatan_pengganti' => 'Pengganti ID ' . $karyawan_lama->karyawan_id. 'TMT ' . $tanggal_bekerja,
                ]);

                $tanggalHariIni = Carbon::now()->toDateString();

                // Simpan paket, fungsi, jabatan, shift, resiko, lokasi, kuota jam, dan masa kerja
                PaketKaryawan::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'paket_id' => $karyawan_lama->paket->paket_id,
                    'beg_date' => $tanggal_bekerja
                ]);

                Riwayat_fungsi::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kode_fungsi' => $karyawan_lama->fungsi_aktif->kode_fungsi,
                    'beg_date' => $tanggal_bekerja
                ]);

                Riwayat_jabatan::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kode_jabatan' => $row[11],
                    'beg_date' => $tanggal_bekerja
                ]);

                Riwayat_shift::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kode_harianshift' => $karyawan_lama->shift_aktif->kode_harianshift,
                    'beg_date' => $tanggal_bekerja
                ]);

                Riwayat_resiko::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kode_resiko' => $karyawan_lama->resiko_aktif->kode_resiko,
                    'beg_date' => $tanggal_bekerja
                ]);

                Riwayat_lokasi::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kode_lokasi' => $karyawan_lama->lokasi_aktif->kode_lokasi,
                    'beg_date' => $tanggal_bekerja
                ]);

                Kuotajam::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'kuota' => $karyawan_lama->kuota_jam_lama->kuota,
                    'beg_date' => $tanggal_bekerja
                ]);

                Masakerja::create([
                    'karyawan_id' => $karyawan_baru->karyawan_id,
                    'tunjangan_masakerja' => $karyawan_lama->masa_kerja->tunjangan_masakerja,
                    'beg_date' => $tanggal_bekerja
                ]);

                $karyawan_lama->update([
                    'status_aktif' => 'Sudah Diganti', // atau kode status tertentu jika pakai enum
                    'catatan_pengganti' => 'Digantikan oleh ID ' . $karyawan_baru->karyawan_id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
