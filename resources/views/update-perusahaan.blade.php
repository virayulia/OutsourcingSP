@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')
<h3 class="mt-4">Update Perusahaan</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/update-perusahaan/{{$dataP->perusahaan_id}}">
<!-- Input Date Range -->
@csrf
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" name="nama" id="nama" value="{{$dataP->perusahaan}}">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection