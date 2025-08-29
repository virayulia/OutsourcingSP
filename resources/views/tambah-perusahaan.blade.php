@extends('layouts.main')

@section('title', 'Perusahaan')

@section('content')
        <h3 class="mt-4">Tambah Perusahaan</h3>
        <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-perusahaan">
                            <!-- Input Date Range -->
        @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" id="nama">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        
@endsection