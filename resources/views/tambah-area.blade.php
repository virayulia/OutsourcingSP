@extends('layouts.main')

@section('title', 'Bidang')

@section('content')

<h3 class="mt-4">Tambah Area</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-area">
    @csrf
    <div class="mb-3">
        <label for="unit" class="form-label">Unit Kerja</label>
        <select class="custom-select select2" name="unit" id="unit">
            <option selected>Pilih Unit</option>
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
        <label for="area" class="form-label">Area</label>
        <input type="text" class="form-control" name="area" id="area">
    </div>
        <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script>
    $(document).ready(function() {
        $('#unit').select2({
            placeholder: "Pilih Unit",
            allowClear: false,
            width: '100%' // Pastikan lebarnya sesuai dengan form-control
        });
    });

    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({ width: '100%' });

        // Ketika Unit dipilih
        $('#unit').change(function() {
            var unit_id = $(this).val();
            $('#bidang').html('<option value="" selected>Pilih Bidang</option>'); // Reset bidang
            if (unit_id) {
                $.ajax({
                    url: '/get-bidang/' + unit_id,
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


    });
</script>
@endsection
