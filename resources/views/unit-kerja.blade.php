@extends('layouts.main')

@section('title', 'Unit Kerja')

@section('content')
<h3 class="mt-4">Unit Kerja</h3>
<div class="mb-3">
    <a href="/gettambah-unit" class="btn btn-primary">Tambah Unit</a>
    <!-- <a href="/gettambah-bidang" class="btn btn-success">Tambah Bidang</a>
    <a href="/gettambah-area" class="btn btn-info">Tambah Area</a> -->
</div>
<table class="table datatable">
    <thead>
        <tr>
            <th>No.</th>
            <th>ID Unit</th>
            <th>Unit Kerja</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->unit_id }}</td>
            <td>{{ $item->unit_kerja }}</td>

            <td>
                <a href="/getupdate-unit/{{ $item->unit_id }}" class="btn btn-warning">
                    <i class="fa fa-pencil-square-o"></i> Edit
                </a>
                <a href="{{ url('delete-unit', $item->unit_id) }}" onclick="return confirm('Apakah Yakin Hapus Data Ini?')" class="btn btn-danger">
                    <i class="fa fa-trash-o"></i> Delete
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<script>
    $(document).ready(function () {
        $('.datatable').each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    // Semua fitur default: search, sort, paging aktif
                    processing: true,
                    serverSide: false
                });
            }
        });
    });
</script>
@endsection
