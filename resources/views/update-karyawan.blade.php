@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')
<h3 class="mt-4">Update Data Karyawan</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/update-karyawan/{{$dataM->karyawan_id}}">
<!-- Input Date Range -->
@csrf
    <div class="mb-3">
        <label for="osis_id" class="form-label">OSIS ID</label>
        <input type="text" class="form-control" name="osis_id" id="osis_id" value="{{$dataM->osis_id}}">
    </div>
    <div class="mb-3">
        <label for="nomor-ktp" class="form-label">Nomor KTP</label>
        <input type="text" class="form-control" name="ktp" id="nomor-ktp" value="{{$dataM->ktp}}" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="nama" class="form-label">Nama Tenaga Kerja</label>
        <input type="text" class="form-control" name="nama" id="nama" value="{{$dataM->nama_tk}}">
    </div>
    <div class="mb-3">
        <label class="form-label" for="perusahaan">Vendor/Perusahaan</label>
        <select class="custom-select select2" name="perusahaan" id="perusahaan">
            <option selected>Pilih Perusahaan</option>
            @foreach ($dataP as $item)
                <option value="{{ $item->perusahaan_id }}" 
                    {{ $item->perusahaan_id == $dataM->perusahaan_id ? 'selected' : '' }}>
                    {{ $item->perusahaan }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal-lahir" class="form-label">Tanggal Lahir</label>
        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal-lahir" value="{{$dataM->tanggal_lahir}}">
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <input type="text" class="form-control" name="alamat" id="alamat" value="{{$dataM->alamat}}">
    </div>
    <div class="mb-3">
        <label for="jenis-kelamin" class="form-label">Jenis Kelamin</label> <br>
        <input type="radio" id="laki" name="jenis_kelamin" value="L" 
            {{ $dataM->jenis_kelamin == 'L' ? 'checked' : '' }}>
        <label for="laki">Laki-laki</label>

        <input type="radio" id="perempuan" name="jenis_kelamin" value="P" 
            {{ $dataM->jenis_kelamin == 'P' ? 'checked' : '' }}>
        <label for="perempuan">Perempuan</label><br>
    </div>
    <div class="mb-3">
        <label for="agama" class="form-label">Agama</label>
        <select class="form-select" aria-label="Default select example" name="agama">
            <option disabled>Pilih...</option>
            <option value="islam" {{ $dataM->agama == 'islam' ? 'selected' : '' }}>Islam</option>
            <option value="kristen" {{ $dataM->agama == 'kristen' ? 'selected' : '' }}>Kristen</option>
            <option value="hindu" {{ $dataM->agama == 'hindu' ? 'selected' : '' }}>Hindu</option>
            <option value="buddha" {{ $dataM->agama == 'buddha' ? 'selected' : '' }}>Buddha</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" aria-label="Default select example" name="status" id="status">
            <option selected>Pilih...</option>
            <option value="S" {{ $dataM->status == 'S' ? 'selected' : '' }}>Single</option>
            <option value="M" {{ $dataM->status == 'M' ? 'selected' : '' }}>Menikah</option>
            <option value="D" {{ $dataM->status == 'D' ? 'selected' : '' }}>Duda</option>
            <option value="J" {{ $dataM->status == 'J' ? 'selected' : '' }}>Janda</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="asal" class="form-label">Asal</label>
        <input type="text" class="form-control" name="asal" id="asal" value="{{$dataM->asal}}">
    </div>
    <input type="hidden" class="form-control" name="tahun_pensiun" id="tahun_pensiun" value="{{$dataM->tahun_pensiun}}">
    <input type="hidden" class="form-control" name="tanggal_pensiun" id="tanggal_pensiun" value="{{$dataM->tanggal_pensiun}}">
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script>
        $(document).ready(function() {
        $('#perusahaan').select2({
            placeholder: "Pilih Perusahaan",
            allowClear: false,
            width: '100%' // Pastikan lebarnya sesuai dengan form-control
        });
    });
</script>
@endsection
