@extends('layouts.main')

@section('title', 'Unit Kerja')

@section('content')

<h3 class="mt-4">Tambah Unit Kerja</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-unit">
                            <!-- Input Date Range -->
        @csrf
            <div class="mb-3">
                <label for="unit_id" class="form-label">ID Unit</label>
                <input type="text" class="form-control" name="unit_id" id="unit_id">
            </div>
            <div class="mb-3">
                <label for="unit" class="form-label">Unit Kerja</label>
                <input type="text" class="form-control" name="unit" id="unit">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
@endsection
