<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Penempatan;
use App\Models\Bidang;
use App\Models\Area;
use App\Models\Karyawan;
use App\Models\UnitKerja;
use Carbon\Carbon;
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

class PenempatanController extends Controller
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

        $paketList = Paket::with(['unitKerja', 'paketKaryawan.karyawan.perusahaan'])->get();
        

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

                // $unit = optional($unitAll[$id] ?? collect())->first();
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
                    ['unit_kerja' => $paket->unitKerja->unit_kerja ?? null],
                    // $unit?->toArray() ?? [],
                    $jabatan?->toArray() ?? [],
                    ['jabatan' => optional($jabatan?->jabatan)->jabatan ?? null],
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
    //  dd($data[100]);
        logger()->info('Total Kuota: ' . $totalExpected);
        logger()->info('Total Terpilih: ' . $totalActual);
        logger()->info('Detail Paket yang Kurang:', $errorLog);
        return view('penempatan', compact('data'));
    }

    
    // public function index()
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

    //     // Dump hasil log
    //     logger()->info('Total Kuota: ' . $totalExpected);
    //     logger()->info('Total Terpilih: ' . $totalActual);
    //     logger()->info('Detail Paket yang Kurang:', $errorLog);

    //     return view('penempatan', compact('data'));
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

//             $penyesuaian = Riwayat_penyesuaian::join('penyesuaian', 'penyesuaian.kode_suai', '=', 'riwayat_penyesuaian.kode_suai')
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
//                 $penyesuaian?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $ump_sumbar],
//                 $paket?->toArray() ?? [],
//                 $masakerja?->toArray() ?? [],
                
//             );
//         }
//   //dd($data);
//         return view('penempatan', compact('data'));
//     }

    public function detail($id)
    {
        $dataM = DB::table('penempatan')
            ->where('id','=', $id)
            ->first();
        $dataP = DB::table('perusahaan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();

        return view('detail-penempatan', ['dataM'=>$dataM, 'dataP' =>$dataP, 'dataU' => $dataU]);
    }

    public function getBidang($unit_id)
    {
        $bidang = Bidang::where('unit_id', $unit_id)->get();
        return response()->json($bidang);
    }

    public function getArea($bidang_id)
    {
        $area = Area::where('bidang_id', $bidang_id)->get();
        return response()->json($area);
    }

    public function getTambah()
    {
        $dataK = DB::table('karyawan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();
        $dataP = Db::table('perusahaan')
            ->get();
        $dataS = Db::table('suai')
            ->get();
        $dataL = Db::table('lokasi')
            ->get();
        $dataPk = Db::table('paket')
            ->get();
        $dataJ = Db::table('jabatan')
            ->get();
        return view('tambah-penempatan2', ['dataK'=>$dataK, 'dataU'=>$dataU, 'dataP'=>$dataP, 'dataJ'=>$dataJ,'dataS'=>$dataS,'dataL'=>$dataL, 'dataPk'=>$dataPk]);
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'nama_tk' => 'required',
            'unit_kerja' => 'required',
            'bidang' => 'nullable',
            'area' => 'nullable',
            'tanggal_bekerja' => 'required'
        ]); 

        Penempatan::create([
            'karyawan_id' => $request->nama_tk,
            'unit_id' => $request->unit_kerja,
            'bidang_id' =>$request->bidang ?: null,
            'area_id' => $request->area ?: null,
            'tanggal_bekerja' =>$request->tanggal_bekerja,
        ]);

        return redirect('/penempatan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function setTambah2(Request $request)
    {
        $request->validate([
            'osis_id' => 'required',
            'ktp' => 'required',
            'nama' => 'required',
            'perusahaan' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'status' => 'required',
            'alamat' => 'required',
            'asal' => 'nullable',
            'unit_kerja' => 'required',
            'bidang' => 'nullable',
            'area' => 'nullable',
            'tanggal_bekerja' => 'required',
            
        ]); 
        $tanggal_lahir = Carbon::parse($request->tanggal_lahir);
        $tanggal_umur56 = $tanggal_lahir->copy()->addYears(56);
        $tanggal_pensiun = $tanggal_umur56->addMonthsNoOverflow(1)->startOfMonth();
        $tahun_pensiun = $tanggal_umur56->format('Y-m-d');

        $karyawan = Karyawan::create([
            'osis_id' => $request->osis_id,
            'ktp' => $request->ktp,
            'nama_tk' => $request->nama,
            'perusahaan_id' =>$request->perusahaan,
            'tanggal_lahir' =>$request->tanggal_lahir,
            'jenis_kelamin' =>$request->jenis_kelamin,
            'agama' =>$request->agama,
            'status' =>$request->status,
            'alamat' =>$request->alamat,
            'asal' =>$request->asal,
            'tahun_pensiun' =>$tahun_pensiun,
            'tanggal_pensiun' =>$tanggal_pensiun,
        ]);

        Penempatan::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'unit_id' => $request->unit_kerja,
            'bidang_id' =>$request->bidang ?: null,
            'area_id' => $request->area ?: null,
            'kode_harianshift' => $request->harian_shift ?: 1,
            'kode_resiko' => $request->resiko ?: 2,
            'kode_suai' => $request->suai ?: 10,
            'kode_lokasi' => $request->lokasi ?: 12,
            'kode_paket' => $request->paket ?: 1,
            'kode_jabatan' => $request->jabatan ?: null,
            'tunjangan_masakerja' => $request->tunjangan_masakerja ?: null,
            'quota_jam_real' => $request->quota_jam_real ?: null,
            'tanggal_bekerja' =>$request->tanggal_bekerja,

        ]);

        return redirect('/penempatan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataM = DB::table('master_data')
            ->where('id','=', $id)
            ->first();
        $dataP = DB::table('perusahaan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();

        return view('update-master', ['dataM'=>$dataM, 'dataP' =>$dataP, 'dataU' => $dataU]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'ktp' => 'required',
            'nama' => 'required',
            'perusahaan' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'kota' => 'required',
            'provinsi' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'unit_kerja' => 'required',
            'username' => 'required',
            'tanggal_masuk' => 'required'
        ]); 

        // dd($request);
        
        Penempatan::where('id', $id)
        ->update([
            'ktp' => $request->ktp,
            'nama' => $request->nama,
            'company_id' =>$request->perusahaan,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' =>$request->tanggal_lahir,
            'alamat' =>$request->alamat,
            'kota' =>$request->kota,
            'provinsi' =>$request->provinsi,
            'kelamin' =>$request->jenis_kelamin,
            'agama' =>$request->agama,
            'unit_kerja_id' =>$request->unit_kerja,
            'username' =>$request->username,
            'tgl_masuk' =>$request->tanggal_masuk,
        ]);

        return redirect('/')->with('success', 'Data Berhasil Tersimpan');
    }

    // public function setBerhenti($id)
    // {
    //     $karyawan = Karyawan::findOrFail($id);
    //     $karyawan->status_aktif = 'Berhenti';
    //     $karyawan->save();

    //     return response()->json(['success' => true]);
    // }

    public function setBerhenti(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'catatan' => 'required|string|max:1000'
        ]);

        Karyawan::where('karyawan_id', $request->id)->update([
            'status_aktif' => 'Berhenti', // jika ada kolom status
            'catatan_berhenti' => $request->catatan,
            'tanggal_berhenti' => now(), // opsional
        ]);

        return response()->json(['message' => 'Karyawan berhasil diberhentikan']);
    }


    public function formPengganti($id)
    {
        // Ambil data utama karyawan
        $dataM = DB::table('karyawan')
            ->where('karyawan_id', '=', $id)
            ->first();

        // Ambil semua data perusahaan & jabatan 
        $dataP = DB::table('perusahaan')->get();
        $dataJ = DB::table('jabatan')->get();

        // Ambil quota jam real terakhir
        $quotaJam = DB::table('kuota_jam')
            ->where('karyawan_id', $id)
            ->orderByDesc('beg_date')
            ->first();

        // Ambil lokasi terakhir
        $lokasiTerakhir = DB::table('riwayat_lokasi')
            ->where('karyawan_id', $id)
            ->orderByDesc('beg_date')
            ->join('lokasi', 'riwayat_lokasi.kode_lokasi', '=', 'lokasi.kode_lokasi')
            ->select('lokasi.*')
            ->first();

        // Ambil paket terakhir
        $paketTerakhir = DB::table('paket_karyawan')
            ->where('karyawan_id', $id)
            ->orderByDesc('beg_date')
            ->join('paket', 'paket_karyawan.paket_id', '=', 'paket.paket_id')
            ->select('paket.*')
            ->first();

        return view('form-pengganti', [
            'dataM' => $dataM,
            'dataJ' => $dataJ,
            'dataP' => $dataP,
            'quotaJam' => $quotaJam,
            'lokasiTerakhir' => $lokasiTerakhir,
            'paketTerakhir' => $paketTerakhir,
        ]);
    }


    public function simpanPengganti(Request $request, $id)
    {
        DB::beginTransaction();
        // dd($request);
        try {
            $tahunPensiun = Carbon::parse($request->tanggal_lahir)
                ->addYears(56); 
        
            $tanggalPensiun = (clone $tahunPensiun)
                ->addMonthNoOverflow()
                ->startOfMonth(); 

            // Ambil data karyawan lama
            $karyawanLama = DB::table('karyawan')->where('karyawan_id', $id)->first();

            // Buat catatan pengganti
            $catatanPengganti = "Pengganti ID $id,";
            $tmt = $request->tanggal_bekerja;

            // Tambahkan nama jika data ada
            if ($karyawanLama) {
                $namaLama = $karyawanLama->nama_tk ?? '(nama tidak ditemukan)';

                $catatanPengganti .= " $namaLama, TMT $tmt";
            }

            // Simpan karyawan baru
            $newId = DB::table('karyawan')->insertGetId([
                'osis_id'                => $request->osis_id,
                'ktp'                    => $request->ktp,
                'nama_tk'                => $request->nama,
                'perusahaan_id'          => $request->perusahaan_id,
                'tanggal_lahir'          => $request->tanggal_lahir,
                'jenis_kelamin'          => $request->jenis_kelamin,
                'agama'                  => $request->agama,
                'status'                 => $request->status,
                'alamat'                 => $request->alamat,
                'asal'                   => $request->asal,
                'tanggal_bekerja'        => $request->tanggal_bekerja,
                'tahun_pensiun'          => $tahunPensiun->format('Y-m-d'),
                'tanggal_pensiun'        => $tanggalPensiun,
                'status_aktif'           => "Aktif",
                'catatan_pengganti'      => $catatanPengganti,
                'tunjangan_penyesuaian'  => 0,
            ]);


            // Simpan quota jam real 
            DB::table('kuota_jam')->insert([
                'karyawan_id' => $newId,
                'kuota'       => $request->quota_jam_real,
                'beg_date'    => $request->tanggal_bekerja,
            ]);

            $tj_masakerja = 0;

            DB::table('masa_kerja')->insert([
                'karyawan_id' => $newId,
                'tunjangan_masakerja' => $tj_masakerja,
                'beg_date'    => $request->tanggal_bekerja,
            ]);
            
            // Simpan riwayat jabatan
            DB::table('riwayat_jabatan')->insert([
                'karyawan_id' => $newId,
                'kode_jabatan'     => $request->jabatan,
                'beg_date'    => $request->tanggal_bekerja,
            ]);


            // Simpan riwayat lokasi
            DB::table('riwayat_lokasi')->insert([
                'karyawan_id' => $newId,
                'kode_lokasi' => $request->lokasi,
                'beg_date'    => $request->tanggal_bekerja,
            ]);

            // Simpan riwayat shift 
            DB::table('riwayat_shift')->insert([
                'karyawan_id'      => $newId,
                'kode_harianshift' => $request->harianshift,
                'beg_date'         => $request->tanggal_bekerja,
            ]);

            // Simpan riwayat resiko 
            DB::table('riwayat_resiko')->insert([
                'karyawan_id'  => $newId,
                'kode_resiko'  => 1,
                'beg_date'     => $request->tanggal_bekerja,
            ]);

            // Simpan paket jika ada
            DB::table('paket_karyawan')->insert([
                'paket_id'    => $request->paket,
                'karyawan_id' => $newId,
                'beg_date'    => $request->tanggal_bekerja,
            ]);
            

            // Update status karyawan lama
            DB::table('karyawan')
            ->where('karyawan_id', $id)
            ->update([
                'status_aktif' => 'Sudah Diganti',
                'catatan_pengganti' => 'Digantikan oleh ID' .$id .', '. $request->nama,
            ]);

            DB::commit();
            return redirect('/penempatan')->with('success', 'Data pengganti berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data pengganti: ' . $e->getMessage());
        }
    }

}
