@extends('layouts.main')

@section('title', 'Bidang')

@section('content')

<h3 class="mt-4">Tambah Bidang</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-bidang">
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
        <label for="bidang" class="form-label">Bidang</label>
        <input type="text" class="form-control" name="bidang" id="bidang">
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
</script>
@endsection
