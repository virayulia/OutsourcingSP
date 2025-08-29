@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')

<h3 class="mt-4">Tambah Penempatan</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-penempatan">
@csrf
    <div class="mb-3">
        <label for="nama_tk" class="form-label">Nama Tenaga Kerja</label>
        <select class="custom-select select2" name="nama_tk" id="nama_tk">
            <option selected>Pilih Tenaga Kerja</option>
            @foreach ($dataK as $item)
                <option value="{{$item->karyawan_id}}">{{$item->nama_tk}}</option>
            @endforeach
        </select>
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
            <option selected>Pilih Bidang</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="area" class="form-label">Area Kerja</label>
        <select class="custom-select select2" name="area" id="area">
            <option selected>Pilih Area</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal_bekerja" class="form-label">Tanggal Bekerja</label>
        <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script>
    $(document).ready(function() {
        $('#nama_tk').select2({
            placeholder: "Pilih Tenaga Kerja",
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
            $('#bidang').html('<option selected>Pilih Bidang</option>'); // Reset bidang
            $('#area').html('<option selected>Pilih Area</option>'); // Reset area

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
            $('#area').html('<option selected>Pilih Area</option>'); // Reset area

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
