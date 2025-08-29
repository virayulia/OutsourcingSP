<?php

namespace App\Imports;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Paket;
use App\Models\Paketkaryawan;
use App\Models\Riwayat_jabatan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;


class MutasiImport implements ToCollection
{

    private $total = 0;
    private $gagal = 0;
    protected $log = [];

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
        unset($rows[0]); // Lewati header

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $this->total++;

                $osis_id = $row[0] ?? null;
                $paket_excel = $row[1] ?? null;
                $tanggal_mutasi_excel = $row[2] ?? null;
                $jabatan_excel = $row[3] ?? null;
                $tanggal_promosi_excel = $row[4] ?? null;

                // Jika OSIS ID kosong → skip
                if (empty($osis_id)) {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - OSIS ID kosong";
                    continue;
                }

                $karyawan = Karyawan::where('osis_id', $osis_id)->first();

                if (!$karyawan) {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - Karyawan dengan OSIS ID $osis_id tidak ditemukan";
                    continue;
                }

                $inserted_mutasi = false;
                $inserted_promosi = false;

                // ===== Mutasi =====
                if (!empty($paket_excel) && !empty($tanggal_mutasi_excel)) {
                    $paket = Paket::where('paket', $paket_excel)->first();

                    if ($paket) {
                        $beg_date_mutasi = Date::excelToDateTimeObject($tanggal_mutasi_excel)->format('Y-m-d');

                        PaketKaryawan::create([
                            'karyawan_id' => $karyawan->karyawan_id,
                            'paket_id' => $paket->paket_id,
                            'beg_date' => $beg_date_mutasi,
                        ]);

                        $inserted_mutasi = true;
                    } else {
                        $this->log[] = "Baris " . ($index + 1) . ": GAGAL Mutasi - Paket '$paket_excel' tidak ditemukan";
                    }
                }

                // ===== Promosi =====
                if (!empty($jabatan_excel) && !empty($tanggal_promosi_excel)) {
                    $jabatan = Jabatan::where('kode_jabatan', $jabatan_excel)->first();

                    if ($jabatan) {
                        $beg_date_promosi = Date::excelToDateTimeObject($tanggal_promosi_excel)->format('Y-m-d');

                        Riwayat_jabatan::create([
                            'karyawan_id' => $karyawan->karyawan_id,
                            'kode_jabatan' => $jabatan->kode_jabatan,
                            'beg_date'      => $beg_date_promosi,
                        ]);

                        $inserted_promosi = true;
                    } else {
                        $this->log[] = "Baris " . ($index + 1) . ": GAGAL Promosi - Jabatan '$jabatan_excel' tidak ditemukan";
                    }
                }

                // Logging hasil
                if ($inserted_mutasi && $inserted_promosi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Mutasi & Promosi";
                } elseif ($inserted_mutasi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Hanya Mutasi";
                } elseif ($inserted_promosi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Hanya Promosi";
                } else {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - Tidak ada mutasi atau promosi";
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLog()
    {
        return $this->log;
    }

}
