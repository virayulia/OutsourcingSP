@extends('layouts.main')

@section('title', 'UMP')

@section('content')

<h3 class="mt-4">Tambah UMP </h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-ump">
@csrf

    <div class="mb-3">
        <label for="kode_lokasi" class="form-label">Lokasi</label>
            <select class="form-select" name="kode_lokasi" id="kode_lokasi">
            <option selected>Pilih Lokasi</option>
            @foreach ($data as $item)
                <option value="{{$item->kode_lokasi}}">{{$item->lokasi}}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="ump" class="form-label">Nilai UMP/UMK</label>
        <input type="text" class="form-control uang" name="ump" id="ump">
    </div>
    <div class="mb-3">
        <label for="tahun" class="form-label">Tahun</label>
        <input type="number" class="form-control" name="tahun" id="tahun">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

        <script>
            $(document).ready(function(){
                $('.uang').mask('000.000.000.000', {reverse: true});
            });
        </script>
@endsection
