@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')

<h3 class="mt-4">Tambah Pengganti</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/simpan-pengganti/{{$dataM->karyawan_id}}">
@csrf
    <div class="mb-3">
        <label for="osis_id" class="form-label">OSIS ID</label>
        <input type="text" class="form-control" name="osis_id" id="osis_id">
    </div>
    <div class="mb-3">
        <label for="ktp" class="form-label">Nomor KTP</label>
        <input type="text" class="form-control" name="ktp" id="ktp">
    </div>
    <div class="mb-3">
        <label for="nama" class="form-label">Nama Tenaga Kerja</label>
        <input type="text" class="form-control" name="nama" id="nama">
    </div>
    <div class="mb-3">
        <label for="jabatan" class="form-label">Jabatan</label>
        <select class="form-select" name="jabatan" id="jabatan">
            <option selected>Pilih Jabatan</option>
            @foreach ($dataJ as $item)
                <option value="{{$item->kode_jabatan}}">{{$item->jabatan}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
    </div>
    <div class="mb-3">
        <label class="form-label">Jenis Kelamin</label><br>
        <input type="radio" id="laki" name="jenis_kelamin" value="L">
        <label for="laki">Laki-laki</label>
        <input type="radio" id="perempuan" name="jenis_kelamin" value="P">
        <label for="perempuan">Perempuan</label><br>
    </div>
    <div class="mb-3">
        <label for="agama" class="form-label">Agama</label>
        <select class="form-select" name="agama" id="agama">
            <option selected>Pilih...</option>
            <option value="Islam">Islam</option>
            <option value="Kristen">Kristen</option>
            <option value="Katolik">Katolik</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddha">Buddha</option>
            <option value="Konghucu">Konghucu</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" name="status" id="status">
            <option selected>Pilih...</option>
            <option value="S">Single</option>
            <option value="M">Menikah</option>
            <option value="D">Duda</option>
            <option value="J">Janda</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <input type="text" class="form-control" name="alamat" id="alamat">
    </div>
    <div class="mb-3">
        <label for="asal" class="form-label">Asal</label>
        <input type="text" class="form-control" name="asal" id="asal">
    </div>

    <input type="hidden" name="karyawan_id" value="{{ $dataM->karyawan_id }}">
    <input type="hidden" name="perusahaan_id" value="{{ $dataM->perusahaan_id }}">
    <input type="hidden" name="lokasi" value="{{ $lokasiTerakhir->kode_lokasi ?? '' }}">
    <input type="hidden" name="paket" value="{{ $paketTerakhir->paket_id ?? '' }}">
    <input type="hidden" class="form-control" name="quota_jam_real" value="{{ $quotaJam->kuota ?? '' }}">

    <div class="mb-3">
        <label for="tanggal_bekerja" class="form-label">Tanggal Bekerja</label>
        <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja">
    </div>
    <div class="mb-3">
        <label for="harianshift" class="form-label">Harian/Shift</label>
        <select class="form-select" name="harianshift" id="harianshift">
            <option>Pilih...</option>
            <option value="1" selected>Harian</option>
            <option value="2">Shift</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection
