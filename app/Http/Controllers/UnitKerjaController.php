<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UnitKerja;
use App\Models\Bidang;
use App\Models\Area;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $data = DB::table('unit_kerja')
            ->select('unit_kerja.unit_id', 'unit_kerja.unit_kerja')
             ->get();
            //  dd($data);
        return view('unit-kerja', ['data' => $data]);

    }

    public function getTambah()
    {        
        return view('tambah-unit');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'unit_id' => 'required',
            'unit' => 'required',
            'fungsi' => 'fungsi'
        ]); 

        $paket = 'paket';
        $fungsi = 'fungsi';
        UnitKerja::create([
            'unit_id' =>$request->unit_id,
            'unit_kerja' => $request->unit,
            'paket' => $paket,
            'fungsi' => $fungsi
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getTambahBidang()
    {        
        $dataU = DB::table('unit_kerja')
        ->get();
        return view('tambah-bidang',['dataU'=>$dataU ]);
    }

    public function setTambahBidang(Request $request)
    {
        $request->validate([
            'unit' => 'required',
            'bidang' => 'required',
        ]); 
    
        Bidang::create([
            'unit_id' =>$request->unit,
            'bidang' => $request->bidang,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getTambahArea()
    {        
        $dataU = DB::table('unit_kerja')
        ->get();
        return view('tambah-area',['dataU'=>$dataU ]);
    }

    public function getBidang($unit_id)
    {
        $bidang = DB::table('bidang')->where('unit_id', $unit_id)->get();
        return response()->json($bidang);
    }


    public function setTambahArea(Request $request)
    {
        $request->validate([
            'bidang' => 'required',
            'area' => 'required'
        ]); 
    
        Area::create([
            'bidang_id' =>$request->bidang,
            'area' => $request->area,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('unit_kerja')
            ->where('unit_id','=', $id)
            ->first();

        return view('update-unit',['dataP' => $dataP]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'unit' => 'required',
        ]); 

        
        UnitKerja::where('unit_id', $id)
        ->update([
            'unit_kerja' => $request->unit,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        $hapus = UnitKerja::findorfail($id);
        $hapus->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
