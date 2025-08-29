@extends('layouts.main')

@section('title', 'Paket')

@section('content')

<h3 class="mt-4">Tambah Paket</h3>
<!-- Input Date Range -->
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-paket">
@csrf
    <div class="mb-3">
        <label for="paket" class="form-label">Paket</label>
        <input type="text" class="form-control" name="paket" id="paket">
    </div>
    <div class="mb-3">
        <label for="kuota_paket" class="form-label">Kuota Paket</label>
        <input type="number" class="form-control" name="kuota_paket" id="kuota_paket">
    </div>
    <div class="mb-3">
        <label for="unit_kerja" class="form-label">Unit Kerja</label>
        <select class="custom-select select2" name="unit_kerja" id="unit_kerja">
            <option selected>Pilih Unit Kerja</option>
            @foreach ($unit as $item)
                <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<script>
    $(document).ready(function() {
        $('#unit_kerja').select2({
            placeholder: "Pilih Tenaga Kerja",
            allowClear: false,
            width: '100%' // Pastikan lebarnya sesuai dengan form-control
        });
    });
</script>
@endsection
