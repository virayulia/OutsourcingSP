@extends('layouts.main')

@section('title', 'Vendor/Perusahaan')

@section('content')

<h3 class="mt-4">Vendor/Perusahaan</h3>
<a href="/gettambah-perusahaan " class="btn btn-primary mb-3">Tambah Data</a>
<table class="table datatable">
    <thead>
        <tr>
            <th>No.</th>
            <th>Perusahaan</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>No.</th>
            <th>Perusahaan</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
    @foreach ($data as $item)
        <tr>
            <td>{{ $loop->iteration }}.</td>
            <td>{{ $item->perusahaan }}</td>
            <td>
                <a href="/getupdate-perusahaan/{{ $item->perusahaan_id }}" class="btn btn-warning">
                    <i class="fa fa-pencil-square-o"></i> Edit
                </a>
                <a href="{{ url('delete-perusahaan', $item->perusahaan_id) }}" onclick="return confirm('Apakah Yakin Hapus Data Ini?')" class="btn btn-danger">
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