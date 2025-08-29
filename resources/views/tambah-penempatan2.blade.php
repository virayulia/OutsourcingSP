@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')

<h3 class="mt-4">Tambah Penempatan</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-penempatan2">
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
        <label for="perusahaan" class="form-label">Vendor/Perusahaan</label>
        <select class="custom-select select2" name="perusahaan" id="perusahaan">
            <option selected>Pilih Perusahaan</option>
            @foreach ($dataP as $item)
                <option value="{{$item->perusahaan_id}}">{{$item->perusahaan}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
    </div>
    <div class="mb-3">
        <label for="jenis-kelamin" class="form-label">Jenis Kelamin</label> <br>
        <input type="radio" id="laki" name="jenis_kelamin" value="L">
        <label for="laki">Laki-laki</label>
        <input type="radio" id="perempuan" name="jenis_kelamin" value="P">
        <label for="perempuan">Perempuan</label><br>
    </div>
    <div class="mb-3">
        <label for="agama" class="form-label">Agama</label>
        <select class="form-select" aria-label="Default select example" name="agama" id="agama">
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
        <select class="form-select" aria-label="Default select example" name="status" id="status">
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
    <div class="mb-3">
        <label for="unit_kerja" class="form-label">Unit Kerja</label>
        <select class="custom-select select2" name="unit_kerja" id="unit_kerja">
            <option selected>Pilih Unit Kerja</option>
            @foreach ($dataU as $item)
                <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="bidang" class="form-label">Bidang Kerja</label>
        <select class="custom-select select2" name="bidang" id="bidang">
            <option value="" selected>Pilih Bidang</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="area" class="form-label">Area Kerja</label>
        <select class="custom-select select2" name="area" id="area">
            <option value="" selected>Pilih Area</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="lokasi" class="form-label">Lokasi</label>
        <select class="form-select" name="lokasi" id="lokasi">
            <option selected>Pilih Lokasi Kerja</option>
            @foreach ($dataL as $item)
                <option value="{{$item->kode_lokasi}}">{{$item->lokasi}}</option>
            @endforeach
        </select>
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
        <label for="paket" class="form-label">Paket</label>
        <select class="custom-select select2" name="paket" id="paket">
            <option selected>Pilih Paket</option>
            @foreach ($dataPk as $item)
                <option value="{{$item->kode_paket}}">{{$item->paket}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="harian_shift" class="form-label">Harian/Shift</label>
        <select class="form-select" aria-label="Default select example" name="harian_shift" id="harian_shift">
            <option selected>Pilih...</option>
            <option value="1">Harian</option>
            <option value="2">Shift</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="resiko" class="form-label">Resiko</label>
        <select class="form-select" aria-label="Default select example" name="resiko" id="resiko">
            <option value="1">Resiko</option>
            <option value="2" selected>Non Resiko</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="suai" class="form-label">Penyesuaian</label>
        <select class="custom-select select2" name="suai" id="suai">
            <option value ="10" selected>Pilih Penyesuaian</option>
            @foreach ($dataS as $item)
                <option value="{{$item->kode_suai}}">{{$item->keterangan}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="quota_jam_real" class="form-label">Quota Jam Real</label>
        <input type="text" class="form-control" name="quota_jam_real" id="quota_jam_real">
    </div>
    <div class="mb-3">
        <label for="tunjangan_masakerja" class="form-label">Tunjangan Masa Kerja</label>
        <input type="text" class="form-control" name="tunjangan_masakerja" id="tunjangan_masakerja">
    </div>
    <div class="mb-3">
        <label for="tanggal_bekerja" class="form-label">Tanggal Bekerja</label>
        <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script>
    $(document).ready(function() {
            $('#perusahaan').select2({
                placeholder: "Pilih Perusahaan",
                allowClear: false,
                width: '100%' // Pastikan lebarnya sesuai dengan form-control
            });
            $('#paket').select2({
                placeholder: "Pilih Paket",
                allowClear: false,
                width: '100%' // Pastikan lebarnya sesuai dengan form-control
            });
        });

    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({ width: '100%' });

        // Ketika Unit dipilih
        $('#unit_kerja').change(function() {
            var unit_id = $(this).val();
            $('#bidang').html('<option value="" selected>Pilih Bidang</option>'); // Reset bidang
            $('#area').html('<option value="" selected>Pilih Area</option>'); // Reset area

            if (unit_id) {
                $.ajax({
                    url: '/get-bidang/' + unit_id, // Endpoint mengambil bidang berdasarkan unit
                    type: 'GET',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#bidang').append('<option value="'+ value.bidang_id +'">'+ value.bidang +'</option>');
                            });
                        }
                    }
                });
            }
        });

        // Ketika Bidang dipilih
        $('#bidang').change(function() {
            var bidang_id = $(this).val();
            $('#area').html('<option value="" selected>Pilih Area</option>'); // Reset area

            if (bidang_id) {
                $.ajax({
                    url: '/get-area/' + bidang_id, // Endpoint mengambil area berdasarkan bidang
                    type: 'GET',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                $('#area').append('<option value="'+ value.area_id +'">'+ value.area +'</option>');
                            });
                        }
                    }
                });
            }
        });
    });

</script>

@endsection
