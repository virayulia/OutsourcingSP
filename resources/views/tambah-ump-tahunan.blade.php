@extends('layouts.main')

@section('title', 'UMP')

@section('content')

<h3 class="mt-4">Tambah UMP Tahunan</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-ump-tahunan">
                            <!-- Input Date Range -->
        @csrf
         @foreach ($data as $item)
            <div class="mb-3">
                <label for="ump_{{ $item->kode_lokasi }}" class="form-label">UMP/UMK {{ $item->lokasi }}</label>
                <input type="text" class="form-control uang" name="ump[{{ $item->kode_lokasi }}]" id="ump_{{ $item->kode_lokasi }}">
            </div>
        @endforeach
    
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
