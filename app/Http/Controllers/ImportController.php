<?php

namespace App\Http\Controllers;
use App\Imports\KaryawanImport;
use App\Imports\MutasiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new KaryawanImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                return redirect()->back()->with('success', "$berhasil data berhasil diimport. $gagal baris gagal diproses.");
            } else {
                return redirect()->back()->with('error', "Semua baris gagal diproses. Tidak ada data yang diimport.");
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    // public function importMutasi(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv'
    //     ]);

    //     $import = new MutasiImport;

    //     try {
    //         Excel::import($import, $request->file('file'));

    //         $total = $import->getTotal();
    //         $gagal = $import->getGagal();
    //         $berhasil = $total - $gagal;

    //         if ($berhasil > 0) {
    //             return redirect()->back()->with('success', "$berhasil data berhasil diimport. $gagal baris gagal diproses.");
    //         } else {
    //             return redirect()->back()->with('error', "Semua baris gagal diproses. Tidak ada data yang diimport.");
    //         }

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
    //     }
    // }

    public function importMutasi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new MutasiImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog(); // ambil log dari importer

            if ($berhasil > 0) {
                return view('import_result', [
                    'successMessage' => "$berhasil data berhasil diimport. $gagal baris gagal diproses.",
                    'logs' => $logs
                ]);
            } else {
                return view('import_result', [
                    'errorMessage' => "Semua baris gagal diproses. Tidak ada data yang diimport.",
                    'logs' => $logs
                ]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

}
