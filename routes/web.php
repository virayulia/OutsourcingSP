<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenempatanController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\UmpController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DashboardController;

use App\Models\Penempatan;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index']);


Route::get('/karyawan', [KaryawanController::class, 'index']);
Route::get('/gettambah-karyawan', [KaryawanController::class, 'getTambah']);
Route::post('/tambah-karyawan', [KaryawanController::class, 'setTambah']);
Route::get('/getupdate-karyawan/{id}', [KaryawanController::class, 'getUpdate']);
Route::post('/update-karyawan/{id}', [KaryawanController::class, 'setUpdate']);
Route::get('/delete-karyawan/{id}', [KaryawanController::class, 'destroy'])->name('delete-master-data');
Route::get('/detail-karyawan/{id}', [KaryawanController::class, 'detail']);
Route::post('/mutasi-paket', [KaryawanController::class, 'simpanMutasi']);
Route::post('/ganti-shift', [KaryawanController::class, 'simpanShift']);
Route::post('/promosi-jabatan', [KaryawanController::class, 'simpanPromosi']);
Route::post('/ganti-area', [KaryawanController::class, 'simpanArea']);
Route::post('/ganti-pakaian', [KaryawanController::class, 'simpanPakaian']);




Route::get('/perusahaan', [PerusahaanController::class, 'index']);
Route::get('/gettambah-perusahaan', [PerusahaanController::class, 'getTambah']);
Route::post('/tambah-perusahaan', [PerusahaanController::class, 'setTambah']);
Route::get('/getupdate-perusahaan/{id}', [PerusahaanController::class, 'getUpdate']);
Route::post('/update-perusahaan/{id}', [PerusahaanController::class, 'setUpdate']);
Route::get('/delete-perusahaan/{id}', [PerusahaanController::class, 'destroy']);

Route::get('/unit-kerja', [UnitKerjaController::class, 'index']);
Route::get('/gettambah-unit', [UnitKerjaController::class, 'getTambah']);
Route::post('/tambah-unit', [UnitKerjaController::class, 'setTambah']);
Route::get('/getupdate-unit/{id}', [UnitKerjaController::class, 'getUpdate']);
Route::post('/update-unit/{id}', [UnitKerjaController::class, 'setUpdate']);
Route::get('/delete-unit/{id}', [UnitKerjaController::class, 'destroy']);
Route::get('/gettambah-bidang', [UnitKerjaController::class, 'getTambahBidang']);
Route::post('/tambah-bidang', [UnitKerjaController::class, 'setTambahBidang']);
Route::get('/gettambah-area', [UnitKerjaController::class, 'getTambahArea']);
Route::post('/tambah-area', [UnitKerjaController::class, 'setTambahArea']);
Route::get('/get-bidang/{unit_id}', [UnitKerjaController::class, 'getBidang']);

Route::get('/penempatan', [PenempatanController::class, 'index']);
Route::get('/gettambah-penempatan', [PenempatanController::class, 'getTambah']);
Route::post('/tambah-penempatan', [PenempatanController::class, 'setTambah']);
Route::post('/tambah-penempatan2', [PenempatanController::class, 'setTambah2']);
Route::get('/getupdate-unit-kerja/{id}', [PenempatanController::class, 'getUpdate']);
Route::post('/update-unit-kerja/{id}', [PenempatanController::class, 'setUpdate']);
Route::get('/get-bidang/{unit_id}', [PenempatanController::class, 'getBidang']);
Route::get('/get-area/{bidang_id}', [PenempatanController::class, 'getArea']);

Route::post('/set-berhenti', [PenempatanController::class, 'setBerhenti']);
Route::get('/tambah-pengganti/{id}', [PenempatanController::class, 'formPengganti']);
Route::post('/simpan-pengganti/{id}', [PenempatanController::class, 'simpanPengganti']);

Route::post('/import-karyawan', [ImportController::class, 'import']);
Route::post('/import-mutasi', [ImportController::class, 'importMutasi']);





Route::get('/paket', [PaketController::class, 'index']);
Route::get('/datapaket', [PaketController::class, 'indexpaket']);
Route::get('/gettambah-paket', [PaketController::class, 'getTambah']);
Route::post('/tambah-paket', [PaketController::class, 'setTambah']);
Route::get('/getupdate-paket/{id}', [PaketController::class, 'getUpdate']);
Route::post('/update-paket/{id}', [PaketController::class, 'setUpdate']);
Route::get('/delete-paket/{id}', [PaketController::class, 'destroy']);

Route::get('/ump', [UmpController::class, 'index']);
Route::get('/gettambah-ump-tahunan', [UmpController::class, 'getTambah']);
Route::post('/tambah-ump-tahunan', [UmpController::class, 'setTambah']);
Route::get('/gettambah-ump', [UmpController::class, 'getTambah2']);
Route::post('/tambah-ump', [UmpController::class, 'setTambah2']);
Route::get('/getupdate-ump/{id}', [UmpController::class, 'getUpdate']);
Route::post('/update-ump/{id}', [UmpController::class, 'setUpdate']);
Route::get('/delete-ump/{id}', [UmpController::class, 'destroy']);



Route::get('/kota/{provinsi_id}', [WilayahController::class, 'getKota']);
