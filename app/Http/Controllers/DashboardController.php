<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_lokasi;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Masakerja;

class DashboardController extends Controller
{
    public function index()
{
    $data = [];
    $errorLog = [];
    $totalExpected = 0;
    $totalActual = 0;
    $currentYear = date('Y');
    $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

    // Ambil semua data di awal untuk efisiensi
    $kuotaJamAll = Kuotajam::latest('beg_date')->get()->keyBy('karyawan_id');
    $jabatanAll = Riwayat_jabatan::with('jabatan')->latest('beg_date')->get()->groupBy('karyawan_id');
    $shiftAll = Riwayat_shift::with('harianshift')->latest('beg_date')->get()->groupBy('karyawan_id');
    $resikoAll = Riwayat_resiko::with('resiko')->latest('beg_date')->get()->groupBy('karyawan_id');
    $fungsiAll = Riwayat_fungsi::with('fungsi')->latest('beg_date')->get()->groupBy('karyawan_id');
    $lokasiAll = Riwayat_lokasi::with(['lokasi.ump' => function ($query) use ($currentYear) {
        $query->where('tahun', $currentYear);
    }])->latest('beg_date')->get()->groupBy('karyawan_id');
    $masakerjaAll = Masakerja::latest('beg_date')->get()->keyBy('karyawan_id');

    $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan', 'paketKaryawan.paket.unitKerja.departemen'])->get();
    $jabatanCount = [];
    $genderCount = ['Laki-laki' => 0, 'Perempuan' => 0];
    $statusAktifCount = ['Aktif' => 0, 'Tidak Aktif' => 0];
    $departemenCount = [];
    $fungsiCount = [];
    $perusahaanCount = [
        'SI' => ['aktif' => 0, 'jumlah' => 0],
        'SP' => ['aktif' => 0, 'jumlah' => 0],
    ];
    $asalKecamatanCount = [];

    foreach ($paketList as $paket) {
        $kuota = (int) $paket->kuota_paket;
        $totalExpected += $kuota;

        $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

        $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
        $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
        $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

        // Ambil karyawan sesuai kuota
        $terpilih = collect();
        if ($aktif->count() >= $kuota) {
            $terpilih = $aktif->take($kuota);
        } else {
            $terpilih = $aktif;
            $sisa = $kuota - $aktif->count();
            $terpilih = $terpilih->concat($berhenti->take($sisa));
            $sisa = $kuota - $terpilih->count();
            $terpilih = $terpilih->concat($diganti->take($sisa));
        }

        $totalActual += $terpilih->count();

        if ($terpilih->count() < $kuota) {
            $errorLog[] = [
                'paket_id' => $paket->paket_id,
                'paket' => $paket->paket,
                'kuota' => $kuota,
                'terpilih' => $terpilih->count(),
                'selisih' => $kuota - $terpilih->count(),
            ];
        }

        foreach ($terpilih as $pk) {
            $karyawan = $pk->karyawan;
            if (!$karyawan) continue;
            $id = $karyawan->karyawan_id;

            // Hitung gender
            if ($karyawan->jenis_kelamin === 'L') {
                $genderCount['Laki-laki']++;
            } elseif ($karyawan->jenis_kelamin === 'P') {
                $genderCount['Perempuan']++;
            }

            // Hitung status aktif
            if ($karyawan->status_aktif === 'Aktif') {
                $statusAktifCount['Aktif']++;
            } else {
                $statusAktifCount['Tidak Aktif']++;
            }
            //Hitung TK per Jabatan
            $jabatan = optional($jabatanAll[$id] ?? collect())->first();
            $namaJabatan = optional($jabatan?->jabatan)->jabatan ?? 'Tidak Diketahui';

            if (!isset($jabatanCount[$namaJabatan])) {
                $jabatanCount[$namaJabatan] = 0;
            }
            $jabatanCount[$namaJabatan]++;

            //Hitung TK per Fungsi
            $fungsi = optional($fungsiAll[$id] ?? collect())->first();
                        // dd($fungsi);

            $namaFungsi = optional($fungsi?->fungsi)->fungsi ?? 'Tidak Diketahui';

            if (!isset($fungsiCount[$namaFungsi])) {
                $fungsiCount[$namaFungsi] = 0;
            }
            $fungsiCount[$namaFungsi]++;

            //HItung TK per Departemen
            $departemenNama = optional($pk->paket?->unitKerja?->departemen)->departemen ?? 'Tidak Diketahui';

            if (!isset($departemenCount[$departemenNama])) {
                $departemenCount[$departemenNama] = ['aktif' => 0, 'jumlah' => 0];
            }

            $departemenCount[$departemenNama]['jumlah']++;

            if ($karyawan->status_aktif === 'Aktif') {
                $departemenCount[$departemenNama]['aktif']++;
            }

            uasort($departemenCount, fn($a, $b) => $b['jumlah'] <=> $a['jumlah']);


            $is_si = optional($pk->paket?->unitKerja?->departemen)->is_si;

            // Total karyawan per Perusahaan
            $perusahaanLabel = $is_si === 1 ? 'SI' : 'SP';

            if (!isset($perusahaanCount[$perusahaanLabel])) {
                $perusahaanCount[$perusahaanLabel] = ['aktif' => 0, 'jumlah' => 0];
            }

            $perusahaanCount[$perusahaanLabel]['jumlah']++;

            if ($karyawan->status_aktif === 'Aktif') {
                $perusahaanCount[$perusahaanLabel]['aktif']++;
            }

            // Hitung per Kecamatan dari asal
            $asal = $karyawan->asal;
            $parts = explode(',', $asal);
            $kecamatan = count($parts) > 1 ? trim($parts[1]) : trim($parts[0]);

            // Normalisasi nama (jaga-jaga untuk ejaan tidak konsisten)
            $kecamatan = strtolower($kecamatan);

            if (str_contains($kecamatan, 'kilangan')) {
                $kategori = 'Lubuk Kilangan';
            } elseif (str_contains($kecamatan, 'pauh')) {
                $kategori = 'Pauh';
            } elseif (str_contains($kecamatan, 'begalung')) {
                $kategori = 'Lubuk Begalung';
            } else {
                $kategori = 'Lain-lain';
            }

            if (!isset($asalKecamatanCount[$kategori])) {
                $asalKecamatanCount[$kategori] = 0;
            }
            $asalKecamatanCount[$kategori]++;

            //Collect data semua karyawan
            $shift = optional($shiftAll[$id] ?? collect())->first();
            $resiko = optional($resikoAll[$id] ?? collect())->first();
            $lokasi = optional($lokasiAll[$id] ?? collect())->first();
            $fungsi = optional($fungsiAll[$id] ?? collect())->first();
            $kuota_jam = $kuotaJamAll[$id] ?? null;
            $masakerja = $masakerjaAll[$id] ?? null;

            $data[] = (object) array_merge(
                $kuota_jam?->toArray() ?? [],
                $karyawan->toArray(),
                ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
                ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
                $jabatan?->toArray() ?? [],
                [
                    'jabatan' => optional($jabatan?->jabatan)->jabatan ?? null,
                    'tunjangan_jabatan' => optional($jabatan?->jabatan)->tunjangan_jabatan ?? 0,
                ],
                ['fungsi' => $fungsi?->fungsi?->fungsi ?? null],
                $shift?->toArray() ?? [],
                $resiko?->toArray() ?? [],
                $lokasi?->toArray() ?? [],
                ['ump_sumbar' => $umpSumbar],
                $paket->toArray(),
                $masakerja?->toArray() ?? []
            );
        }
    }

    uasort($departemenCount, fn($a, $b) => $b['jumlah'] <=> $a['jumlah']);

    $perusahaanCount['Total'] = [
        'aktif' => $perusahaanCount['SI']['aktif'] + $perusahaanCount['SP']['aktif'],
        'jumlah' => $perusahaanCount['SI']['jumlah'] + $perusahaanCount['SP']['jumlah'],
    ];

    // dd($asalKecamatanCount);

    logger()->info('Total Kuota: ' . $totalExpected);
    logger()->info('Total Terpilih: ' . $totalActual);
    logger()->info('Detail Paket yang Kurang:', $errorLog);

    return view('dashboard', compact('data', 'jabatanCount', 'genderCount', 'statusAktifCount','departemenCount','perusahaanCount','fungsiCount','asalKecamatanCount'));
}

}
