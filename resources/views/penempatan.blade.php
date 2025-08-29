@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')
<!-- @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif -->

<h3 class="mt-4">Penempatan</h3>
<!-- Tombol Ikon Excel -->
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal" title="Upload Kolektif Data">
    <i class="fas fa-file-excel fa-lg"></i>
</button>
<!-- Di samping tombol Upload Excel -->
<a href="{{ asset('templates/template_import.xlsx') }}" class="btn btn-outline-success" download>
    <i class="fas fa-download"></i> Template
</a>
<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-excel me-2"></i>Import Data Karyawan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ url('/import-karyawan') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-upload"></i> Import
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- <a href="/gettambah-penempatan" class="btn btn-primary mb-3">Tambah Data</a> -->
 <div class="row mb-3">
    <div class="col-md-4">
        <label for="filterPaket">Filter Paket</label>
        <select id="filterPaket" class="form-control">
            <option value="">Semua</option>
        @php
            $paket = collect($data)->pluck('paket')->unique()->toArray();
            usort($paket, function($a, $b) {
                preg_match('/\d+/', $a, $matchesA);
                preg_match('/\d+/', $b, $matchesB);
                return $matchesA[0] - $matchesB[0]; // Membandingkan angka dalam string
            });
        @endphp
        @foreach ($paket as $paket)
            <option value="{{ $paket }}">{{ $paket }}</option>
        @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="filterAktifMulai">Filter Aktif Mulai</label>
        <select id="filterAktifMulai" class="form-control">
        <option value="">Semua</option>
        @php
            $tanggal = collect($data)->pluck('aktif_mulai')->unique()->toArray();

            // Mengonversi tanggal ke objek DateTime dan mengurutkannya
            usort($tanggal, function($a, $b) {
                $dateA = DateTime::createFromFormat('F Y', $a);
                $dateB = DateTime::createFromFormat('F Y', $b);
                return $dateA <=> $dateB; // Mengurutkan berdasarkan objek DateTime
            });
        @endphp
        @foreach ($tanggal as $tgl)
            <option value="{{ $tgl }}">{{ $tgl }}</option>
        @endforeach
    </select>
    </div>
</div>
<table id="datatablesSimple">
    <thead>
        <tr>
            <th>No.</th>
            <th>OSIS ID</th>
            <th>Nama</th>
            <th>Vendor/Perusahaan</th>
            <th>Unit Kerja</th>
            <th>Paket</th>
            <th>Jabatan</th>
            <th>Aktif Mulai</th>
            <th>Status</th>
            <th>Action</th>
            <!-- <th>Upah Pokok</th>
            <th>Tj. Umum</th>
            <th>Tj. Lokasi</th>
            <th>Tj. Jabatan</th>
            <th>Tj. Suai</th>
            <th>Tj. Resiko</th>
            <th>Tj. Presensi</th> -->
            
        </tr>
    </thead>
    <tbody>
    @foreach ($data as $item)
        <tr>
            <th>{{ $loop->iteration }}</th>
            <td>{{$item->osis_id}}</td>
            <td>{{$item->nama_tk}}</td>
            <td>{{$item->perusahaan}}</td>
            <td>{{$item->unit_kerja['unit_kerja']}}</td>
            <td>{{$item->paket}}</td>
            <td>{{$item->jabatan}}</td>
            <td>{{$item->aktif_mulai }}</td>
            <td>{{$item->status_aktif}}</td>
            <td>
                @if($item->status_aktif === 'Aktif')
                    <a href="#" class="btn btn-danger btn-sm btn-berhenti" data-id="{{ $item->karyawan_id }}">
                        <i class="fa fa-times-circle"></i> Berhentikan
                    </a>
                @elseif($item->status_aktif === 'Berhenti')
                    <a href="/tambah-pengganti/{{ $item->karyawan_id }}" class="btn btn-success btn-sm">
                        <i class="fa fa-user-plus"></i> Tambah Pengganti
                    </a>
                @endif
            </td>
        </tr>
    @endforeach 
    </tbody>
</table>

<script>
// $(document).on('click', '.btn-berhenti', function(e) {
//     e.preventDefault();
//     const id = $(this).data('id');

//     Swal.fire({
//         title: 'Apakah Anda yakin?',
//         text: "Ingin memberhentikan karyawan ini?",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#d33',
//         cancelButtonColor: '#3085d6',
//         confirmButtonText: 'Ya, berhentikan'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             // Kirim request ke server
//             $.get("/set-berhenti/" + id, function(response) {
//                 Swal.fire({
//                     title: 'Karyawan diberhentikan!',
//                     text: 'Apakah Anda ingin menambahkan pengganti sekarang?',
//                     icon: 'success',
//                     showCancelButton: true,
//                     confirmButtonText: 'Tambah Pengganti',
//                     cancelButtonText: 'Batal',
//                     confirmButtonColor: '#28a745',
//                     cancelButtonColor: '#6c757d',
//                 }).then((nextStep) => {
//                     if (nextStep.isConfirmed) {
//                         window.location.href = '/tambah-pengganti/' + id;
//                     } else {
//                         // Jika klik batal, reload halaman
//                         location.reload();
//                     }
//                 });
//             });
//         }
//     });
// });
$(document).on('click', '.btn-berhenti', function(e) {
    e.preventDefault();
    const id = $(this).data('id');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Ingin memberhentikan karyawan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, berhentikan'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan inputan catatan
            Swal.fire({
                title: 'Masukkan Catatan Berhenti',
                input: 'textarea',
                inputPlaceholder: 'Contoh: Mengundurkan diri karena alasan pribadi...',
                inputAttributes: {
                    'aria-label': 'Catatan Berhenti'
                },
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
            }).then((inputResult) => {
                if (inputResult.isConfirmed && inputResult.value) {
                    const catatan = inputResult.value;

                    // Kirim request ke server pakai AJAX POST
                    $.ajax({
                        url: "/set-berhenti",
                        type: "POST",
                        data: {
                            id: id,
                            catatan: catatan,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Karyawan diberhentikan!',
                                text: 'Apakah Anda ingin menambahkan pengganti sekarang?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Tambah Pengganti',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d',
                            }).then((nextStep) => {
                                if (nextStep.isConfirmed) {
                                    window.location.href = '/tambah-pengganti/' + id;
                                } else {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                        }
                    });
                }
            });
        }
    });
});

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
$(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#datatablesSimple')) {
        var table = $('#datatablesSimple').DataTable({
            processing: true,
            serverSide: false
        });

        $('#filterPaket').on('change', function () {
            var val = this.value;
            table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        $('#filterAktifMulai').on('change', function () {
            var val = this.value;
            table.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
        });
    }
});
</script>
@endsection