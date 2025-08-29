<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_penyesuaian;
use App\Models\Riwayat_lokasi;
use App\Models\PaketKaryawan;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Masakerja;
use Illuminate\Http\Request;

class PaketController extends Controller
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

        $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();

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

                $jabatan = optional($jabatanAll[$id] ?? collect())->first();
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
    // dd($data[100]);
        logger()->info('Total Kuota: ' . $totalExpected);
        logger()->info('Total Terpilih: ' . $totalActual);
        logger()->info('Detail Paket yang Kurang:', $errorLog);
        return view('paket', compact('data'));
    }

    //chatgpt salah
// public function index()
// {
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

//     $paketList = Paket::with([
//         'paketKaryawan.karyawan.perusahaan',
//         'paketKaryawan.karyawan.kuotaJam' => fn($q) => $q->orderByDesc('beg_date'),
//         'paketKaryawan.karyawan.riwayatUnit.unitKerja',
//         'paketKaryawan.karyawan.riwayatJabatan.jabatan',
//         'paketKaryawan.karyawan.riwayatShift.harianShift',
//         'paketKaryawan.karyawan.riwayatResiko.resiko',
//         'paketKaryawan.karyawan.riwayatLokasi.lokasi',
//         'paketKaryawan.karyawan.riwayatLokasi.lokasi.ump' => fn($q) => $q->where('tahun', $currentYear),
//         'paketKaryawan.karyawan.masaKerja' => fn($q) => $q->orderByDesc('beg_date'),
//     ])->get();

//     $data = [];

//     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

//         // Filter berdasarkan status
//         $aktif = $karyawanPaket->where('karyawan.status_aktif', 'Aktif');
//         $berhenti = $karyawanPaket->where('karyawan.status_aktif', 'Berhenti');
//         $diganti = $karyawanPaket->where('karyawan.status_aktif', 'Sudah Diganti');

//         $terpilih = collect();
//         if ($aktif->count() >= $kuota) {
//             $terpilih = $aktif->take($kuota);
//         } else {
//             $terpilih = $aktif;
//             $sisa = $kuota - $aktif->count();
//             $terpilih = $terpilih->concat($berhenti->take($sisa));
//             $sisa = $kuota - $terpilih->count();
//             $terpilih = $terpilih->concat($diganti->take($sisa));
//         }

//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             if (!$karyawan) continue;

//             $data[] = (object) [
//                 'osis_id' => $karyawan->osis_id ?? null,
//                 'karyawan_id' => $karyawan->karyawan_id ?? null,
//                 'nama_tk' => $karyawan->nama_tk ?? null,
//                 'perusahaan' => $karyawan->perusahaan->perusahaan ?? null,
//                 'perusahaan_id' => $karyawan->perusahaan_id ?? null,
//                 'tanggal_bekerja' => $karyawan->tanggal_bekerja ?? null,
//                 'aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y'),
//                 'ump' => optional($karyawan->riwayatLokasi->first()?->lokasi->ump->first())->ump ?? 0,
//                 'ump_sumbar' => $umpSumbar,
//                 'kuota' => $pk->kuota ?? 0,
//                 'tunjangan_jabatan' => $karyawan->riwayatJabatan->first()?->tunjangan_jabatan ?? 0,
//                 'tunjangan_masakerja' => $karyawan->masaKerja->first()?->tunjangan_masakerja ?? 0,
//                 'tunjangan_penyesuaian' => $karyawan->riwayatUnit->first()?->tunjangan_penyesuaian ?? 0,
//                 'tunjangan_shift' => $karyawan->riwayatShift->first()?->tunjangan_shift ?? 0,
//                 'tunjangan_resiko' => $karyawan->riwayatResiko->first()?->tunjangan_resiko ?? 0,
//                 'kode_resiko' => $karyawan->riwayatResiko->first()?->kode_resiko ?? null,
//                 'kode_lokasi' => $karyawan->riwayatLokasi->first()?->kode_lokasi ?? null,
//                 'paket' => $paket->paket ?? null,
//             ];
//         }
//     }
// dd($data);
//     return view('paket', compact('data'));
// }



    //yg udah benar
//     public function index()
// {
//     $data = [];
//     $errorLog = [];
//     $totalExpected = 0;
//     $totalActual = 0;

//     $paketList = Paket::with(['paketKaryawan.karyawan'])->get();
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

//     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $totalExpected += $kuota;

//         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->with('karyawan')
//             ->orderByDesc('beg_date')
//             ->get();

//         // Filter berdasarkan status dan pastikan data karyawan ada
//         $aktif = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
//         $berhenti = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
//         $diganti = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

//         // Pilih sesuai kuota
//         $terpilih = collect();

//         if ($aktif->count() >= $kuota) {
//             $terpilih = $aktif->take($kuota);
//         } else {
//             $terpilih = $aktif;
//             $sisa = $kuota - $aktif->count();

//             if ($berhenti->count() >= $sisa) {
//                 $terpilih = $terpilih->concat($berhenti->take($sisa));
//             } else {
//                 $terpilih = $terpilih->concat($berhenti);
//                 $sisa = $kuota - $terpilih->count();
//                 $terpilih = $terpilih->concat($diganti->take($sisa));
//             }
//         }

//         $totalActual += $terpilih->count();

//         // Cek jika jumlah terpilih kurang dari kuota, simpan log
//         if ($terpilih->count() < $kuota) {
//             $errorLog[] = [
//                 'paket_id' => $paket->paket_id,
//                 'paket' => $paket->paket,
//                 'kuota' => $kuota,
//                 'terpilih' => $terpilih->count(),
//                 'selisih' => $kuota - $terpilih->count(),
//             ];
//         }

//         // Gabungkan data yang valid
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

//             // Ambil riwayat
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) use ($currentYear) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                         ->where('ump.tahun', $currentYear);
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

//             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $umpSumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }
//     }
//     // dd($data);

//     // Dump hasil log
//     logger()->info('Total Kuota: ' . $totalExpected);
//     logger()->info('Total Terpilih: ' . $totalActual);
//     logger()->info('Detail Paket yang Kurang:', $errorLog);

//     return view('paket', compact('data'));
// }



//     public function index()
// {
//     $data = [];

//     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

//     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->with('karyawan')
//             ->orderByDesc('beg_date')
//             ->get();

//         // Gabungkan status dengan prioritas: Aktif -> Berhenti -> Sudah Diganti
//         $terurut = $karyawanPaket->filter(fn($item) => $item->karyawan !== null)
//             ->sortBy(function ($item) {
//                 return match ($item->karyawan->status_aktif) {
//                     'Aktif' => 1,
//                     'Berhenti' => 2,
//                     'Sudah Diganti' => 3,
//                     default => 4,
//                 };
//             })
//             ->values();

//         $terpilih = $terurut->take($kuota);

//         // Loop data yang terpilih
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

//             // Relasi terkait
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) use ($currentYear) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                         ->where('ump.tahun', $currentYear);
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

//             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $umpSumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }

//         // Jika data kurang dari kuota, tambahkan placeholder kosong
//         // $kurang = $kuota - $terpilih->count();
//         // for ($i = 0; $i < $kurang; $i++) {
//         //     $data[] = (object) [
//         //         'karyawan_id' => null,
//         //         'nama_lengkap' => 'Belum tersedia',
//         //         'perusahaan' => $paket->paket ?? null,
//         //         'ump_sumbar' => $umpSumbar,
//         //         'paket_id' => $paket->paket_id,
//         //         'paket' => $paket->paket,
//         //         'aktif_mulai' => null,
//         //         // Tambahkan field default lainnya jika perlu
//         //     ];
//         // }
//     }

//     return view('paket', compact('data'));
// }

    
    // public function index()
    // {
    //     $data = [];

    //     // Ambil semua paket dengan relasi perusahaan
    //     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();

    //     foreach ($paketList as $paket) {
    //         $kuota = $paket->kuota_paket;

    //         // Ambil semua karyawan yang terkait dengan paket
    //         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
    //             ->with('karyawan')
    //             ->orderByDesc('beg_date')
    //             ->get();

    //         // Filter berdasarkan status
    //         $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Aktif');
    //         $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Berhenti');
    //         $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Sudah Diganti');

    //         // Ambil sebanyak kuota dari urutan prioritas
    //         $terpilih = collect();

    //         if ($aktif->count() >= $kuota) {
    //             $terpilih = $aktif->take($kuota);
    //         } else {
    //             $terpilih = $aktif;
    //             $sisa = $kuota - $terpilih->count();

    //             if ($berhenti->count() >= $sisa) {
    //                 $terpilih = $terpilih->concat($berhenti->take($sisa));
    //             } else {
    //                 $terpilih = $terpilih->concat($berhenti);
    //                 $sisa = $kuota - $terpilih->count();
    //                 $terpilih = $terpilih->concat($diganti->take($sisa));
    //             }
    //         }

    //         // Gabungkan data terpilih
    //         foreach ($terpilih as $pk) {
    //             $karyawan = $pk->karyawan;
    //             $karyawan_id = $karyawan->karyawan_id;

    //             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
    //                 ->join('ump', function ($join) {
    //                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
    //                         ->where('ump.tahun', date('Y'));
    //                 })
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             $ump_sumbar = Ump::where('kode_lokasi', '12')->where('tahun', date('Y'))->value('ump');
    //             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             // Gabungkan semua info menjadi satu objek
    //             $data[] = (object) array_merge(
    //                 $kuota_jam?->toArray() ?? [],
    //                 $karyawan->toArray(),
    //                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
    //                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
    //                 $unit?->toArray() ?? [],
    //                 $jabatan?->toArray() ?? [],
    //                 $shift?->toArray() ?? [],
    //                 $resiko?->toArray() ?? [],
    //                 $lokasi?->toArray() ?? [],
    //                 ['ump_sumbar' => $ump_sumbar],
    //                 $paket->toArray(),
    //                 $masakerja?->toArray() ?? []
    //             );
    //         }
    //     }

    //     return view('paket', compact('data'));
    // }


//     public function index()
// {
//     $data = [];

//     // Ambil semua paket
//     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan:perusahaan_id,perusahaan'])->get();

//     foreach ($paketList as $paket) {
//         $kuota = $paket->kuota_paket;

//         // Ambil semua karyawan terkait paket ini
//         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->orderByDesc('beg_date')
//             ->with('karyawan') // eager load biar efisien
//             ->get();

//         // Pisahkan berdasarkan status aktif karyawan
//         $aktif = $karyawanPaket->filter(function ($item) {
//             return $item->karyawan->status_aktif === 'Aktif';
//         });

//         $nonaktif = $karyawanPaket->reject(function ($item) {
//             return $item->karyawan->status_aktif === 'Aktif';
//         });

//         // Ambil sebanyak kuota
//         $terpilih = $aktif->take($kuota);

//         if ($terpilih->count() < $kuota) {
//             $sisa = $kuota - $terpilih->count();
//             $terpilih = $terpilih->concat($nonaktif->take($sisa));
//         }

//         // Gabungkan data
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                          ->where('ump.tahun', date('Y'));
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

//             $ump_sumbar = Ump::where('kode_lokasi', '12')->where('tahun', date('Y'))->value('ump');
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

//             // Gabung semua info ke satu objek
//             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 [
//                     'perusahaan' => $karyawan->perusahaan->perusahaan ?? '-',
//                     'aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')
//                 ],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $ump_sumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }
//     }

//     return view('paket', compact('data'));
// }

//     public function index()
//     {
//         // Ambil semua karyawan beserta nama perusahaan
//         $karyawanList = Karyawan::join('perusahaan', 'perusahaan.perusahaan_id', '=', 'karyawan.perusahaan_id')
//             ->select('karyawan.*', 'perusahaan.perusahaan') // ambil nama perusahaan atau semua kolom jika perlu
//             ->get();

//         $data = [];

//         foreach ($karyawanList as $karyawan) {
//             $karyawan_id = $karyawan->karyawan_id;

//             // Ambil riwayat masing-masing entitas
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', 'ump.kode_lokasi','=','lokasi.kode_lokasi')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $ump_sumbar = Ump::where('kode_lokasi', '12')
//                 ->where('tahun', date('Y')) // atau tahun aktif
//                 ->value('ump');

//             $paket = PaketKaryawan::join('paket', 'paket.paket_id', '=', 'paket_karyawan.paket_id')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

//             // Gabungkan data
//             $data[] = (object) array_merge(
//                 $kuota_jam->toArray(),
//                 $karyawan->toArray()?? [],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $ump_sumbar],
//                 $paket?->toArray() ?? [],
//                 $masakerja?->toArray() ?? [],
                
//             );
//         }
//   //dd($data);
//         return view('paket', compact('data'));
//     }

    public function indexpaket()
    {
        $data = DB::table('paket')
            ->join('unit_kerja','unit_kerja.unit_id','=','paket.unit_id')
            ->select('paket.*','unit_kerja.*')
            ->orderBy('paket_id', 'asc')
            ->get();
            //  dd($data);
        return view('data_paket', ['data' => $data]);

    }

    public function getTambah()
    {        
        $unit = DB::table('unit_kerja')
                ->select('unit_kerja.*')
                ->get();
        return view('tambah-paket', ['unit'=> $unit]);
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'paket' => 'required',
            'kuota_paket' => 'required',
            'unit_kerja' => 'required'
        ]); 

        Paket::create([
            'paket' => $request->paket,
            'kuota_paket' => $request->kuota_paket,
            'unit_id' => $request->unit_kerja
        ]);

        return redirect('/datapaket')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('paket')
            ->where('paket_id','=', $id)
            ->first();
        $unit = DB::table('unit_kerja')
            ->select('unit_kerja.*')
            ->get();

        return view('update-paket',['dataP' => $dataP, 'unit' =>$unit]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'paket' => 'required',
            'kuota_paket' => 'required'
        ]); 

        
        Paket::where('paket_id', $id)
        ->update([
            'paket' => $request->paket,
            'kuota_paket' => $request->kuota_paket
        ]);

        return redirect('/datapaket')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        $hapus = Paket::findorfail($id);
        $hapus->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }

}

