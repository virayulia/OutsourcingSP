<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\Province;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Paketkaryawan;
use App\Models\Riwayat_shift;
use Carbon\Carbon;
use App\Models\Perusahaan;
use App\Models\UnitKerja;


class KaryawanController extends Controller
{
    public function index()
    {
        // $data = DB::table('karyawan')
        //     ->join('perusahaan', 'karyawan.perusahaan_id', '=', 'perusahaan.perusahaan_id')
        //     ->select('karyawan.*', 'perusahaan.perusahaan')
        //     ->get();

        $data = Karyawan::with([
                'perusahaan',
                'pakaianTerakhir',
            ])->get();

        $paketList = DB::table('paket')->get();

        // Ambil paket aktif saat ini untuk tiap karyawan
        $paketKaryawan = DB::table('paket_karyawan as pk1')
            ->join('paket', 'pk1.paket_id', '=', 'paket.paket_id')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM paket_karyawan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('pk1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('pk1.beg_date', '=', 'latest.max_date');
            })
            ->select('pk1.karyawan_id', 'paket.paket as nama_paket')
            ->get()
            ->keyBy('karyawan_id');

       
        // $harianShift = DB::table('karyawan as k')
        //     ->select('k.*', 'rs.kode_harianshift', 'hs.harianshift')
        //     ->leftJoin(DB::raw('riwayat_shift as rs'), function($join) {
        //         $join->on('rs.id', '=', DB::raw('(SELECT rs2.id FROM riwayat_shift as rs2 WHERE rs2.karyawan_id = k.karyawan_id ORDER BY rs2.beg_date DESC LIMIT 1)'));
        //     })
        //     ->leftJoin('harianshift as hs', 'hs.kode_harianshift', '=', 'rs.kode_harianshift')
        //     ->get();

        $harianShift = DB::table('riwayat_shift as rs1')
            ->join('harianshift', 'rs1.kode_harianshift', '=', 'harianshift.kode_harianshift')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_shift
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rs1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rs1.beg_date', '=', 'latest.max_date');
            })
            ->select('rs1.karyawan_id', 'harianshift.harianshift as harianshift')
            ->get()
            ->keyBy('karyawan_id');

        $jabatan = DB::table('riwayat_jabatan as rj1')
            ->join('jabatan', 'rj1.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_jabatan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rj1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rj1.beg_date', '=', 'latest.max_date');
            })
            ->select('rj1.karyawan_id', 'jabatan.jabatan as jabatan')
            ->get()
            ->keyBy('karyawan_id');
        
        $jabatanList = DB::table('jabatan')->get();

        $area = DB::table('karyawan')
            ->leftJoin('area', 'karyawan.area_id', '=', 'area.area_id')
            ->select('karyawan.karyawan_id', 'area.area')
            ->get()
            ->keyBy('karyawan_id');
        
        // $pakaian = Karyawan::with('pakaianTerakhir')->get();
        //  dd($pakaian[0]->pakaianTerakhir->nilai_jatah);


        return view('karyawan', [
            'data' => $data,
            'paketList' => $paketList,
            'paketKaryawan' => $paketKaryawan,
            'harianShift' => $harianShift,
            'jabatan'  => $jabatan,
            'jabatanList' => $jabatanList,
            'area'        => $area,
            // 'pakaian'      => $pakaian
        ]);
    }


    public function detail($id)
    {
        $dataM = DB::table('karyawan')
            ->where('karyawan_id','=', $id)
            ->first();
        $dataP = DB::table('perusahaan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();

        return view('detail-karyawan', ['dataM'=>$dataM, 'dataP' =>$dataP, 'dataU' => $dataU]);
    }

    public function getTambah()
    {
        $dataP = DB::table('perusahaan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();
        
        return view('tambah-karyawan', ['dataP'=>$dataP, 'dataU' =>$dataU]);
    }

    public function setTambah(Request $request)
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
        ]); 
       $tanggal_lahir = Carbon::parse($request->tanggal_lahir);
       $tanggal_umur56 = $tanggal_lahir->copy()->addYears(56);
       $tanggal_pensiun = $tanggal_umur56->addMonth()->startOfMonth();
       $tahun_pensiun = $tanggal_umur56->format('Y-m-d');

        Karyawan::create([
            'osis_id' => $request->osis_id,
            'ktp' => $request->ktp,
            'nama_tk' => $request->nama,
            'perusahaan_id' =>$request->perusahaan,
            'tanggal_lahir' =>$request->tanggal_lahir,
            'jenis_kelamin' =>$request->jenis_kelamin,
            'agama' =>$request->agama,
            'status' =>$request->status,
            'alamat' =>$request->alamat,
            'asal' =>$request->asal ?: null,
            'tahun_pensiun' =>$tahun_pensiun,
            'tanggal_pensiun' =>$tanggal_pensiun,
        ]);

        return redirect('/karyawan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataM = DB::table('karyawan')
            ->where('karyawan_id','=', $id)
            ->first();
        $dataP = DB::table('perusahaan')
            ->get();
        $dataU = Db::table('unit_kerja')
            ->get();

        return view('update-karyawan', ['dataM'=>$dataM, 'dataP' =>$dataP, 'dataU' => $dataU]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'osis_id' =>'required',
            'ktp' => 'required',
            'nama' => 'required',
            'perusahaan' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
        ]); 

        // dd($request);
        
        Karyawan::where('karyawan_id', $id)
        ->update([
            'osis_id' => $request->osis_id,
            'ktp' => $request->ktp,
            'nama_tk' => $request->nama,
            'perusahaan_id' =>$request->perusahaan,
            'tanggal_lahir' =>$request->tanggal_lahir,
            'alamat' =>$request->alamat,
            'jenis_kelamin' =>$request->jenis_kelamin,
            'agama' =>$request->agama,
            'status' => $request->status,
            'asal' =>$request->asal
        ]);

        return redirect('/karyawan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        $hapus = Karyawan::findorfail($id);
        $hapus->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function simpanMutasi(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,karyawan_id',
            'paket_id' => 'required|exists:paket,paket_id',
            'beg_date' => 'required',
        ]);

        // Simpan mutasi dengan tanggal sekarang
        DB::table('paket_karyawan')->insert([
            'karyawan_id' => $request->karyawan_id,
            'paket_id' => $request->paket_id,
            'beg_date' => $request->beg_date,
        ]);

        return redirect()->back()->with('success', 'Mutasi paket berhasil disimpan.');
    }

    public function simpanShift(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'kode_harianshift' => 'required',
            'beg_date' => 'required',
        ]);
       

        // Simpan mutasi dengan tanggal sekarang
        DB::table('riwayat_shift')->insert([
            'karyawan_id' => $request->karyawan_id,
            'kode_harianshift' => $request->kode_harianshift,
            'beg_date' => $request->beg_date,
        ]);

        return redirect()->back()->with('success', 'Pergantian Harian/Shift berhasil disimpan.');
    }

    public function simpanPromosi(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,karyawan_id',
            'kode_jabatan' => 'required|exists:jabatan,kode_jabatan',
            'beg_date' => 'required',
        ]);

        // Simpan mutasi dengan tanggal sekarang
        DB::table('riwayat_jabatan')->insert([
            'karyawan_id' => $request->karyawan_id,
            'kode_jabatan' => $request->kode_jabatan,
            'beg_date' => $request->beg_date,
        ]);

        return redirect()->back()->with('success', 'Promosi jabatan berhasil disimpan.');
    }

    public function simpanArea(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'area_id' => 'required',
        ]);
       

        Karyawan::where('karyawan_id', $request->karyawan_id)
        ->update([
            'area_id' => $request->area_id,
        ]);

        return redirect()->back()->with('success', 'Pergantian Area berhasil disimpan.');
    }
    
    public function simpanPakaian(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'ukuran_baju' => 'required',
            'ukuran_celana' => 'required',
            'beg_date' => 'required',
        ]);
       

        DB::table('pakaian')->insert([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => 600000,
            'ukuran_baju' => $request->ukuran_baju,
            'ukuran_celana' => $request->ukuran_celana,
            'beg_date' => $request->beg_date,
        ]);

        return redirect()->back()->with('success', 'Pergantian Pakaian berhasil disimpan.');
    }


}
