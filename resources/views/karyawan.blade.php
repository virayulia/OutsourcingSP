@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')

<h3 class="mt-4">Karyawan</h3>
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="/gettambah-karyawan" class="btn btn-primary">Tambah Data</a>

    <!-- Tombol Ikon Excel -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importMutasiModal" title="Upload Mutasi Promosi Kolektif">
        <i class="fas fa-file-excel fa-lg"></i>
    </button>

    <!-- Tombol Download Template -->
    <a href="{{ asset('templates/templateMutasiPromosi_import.xlsx') }}" class="btn btn-outline-success" download>
        <i class="fas fa-download"></i> Template
    </a>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importMutasiModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-excel me-2"></i>Import Mutasi Karyawan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ url('/import-mutasi') }}" method="POST" enctype="multipart/form-data">
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
<br>

<!-- <input type="text" id="search-id" placeholder="Cari berdasarkan ID"> -->
<table class="table datatable" id="datatableSimple">
    <thead>
        <tr>
            <th>No.</th>
            <th>OSIS ID</th>
            <th>KTP</th>
            <th>Nama</th>
            <th>Perusahaan</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->osis_id }}</td>
            <td>{{ $item->ktp }}</td>
            <td>{{ $item->nama_tk }}</td>
            <td>{{ $item->perusahaan->perusahaan }}</td>
            <td>
                <a href="/detail-karyawan/{{ $item->karyawan_id }}" class="btn btn-info">
                    <i class="fa fa-info-circle"></i> Info
                </a>
                <a href="/getupdate-karyawan/{{ $item->karyawan_id }}" class="btn btn-warning">
                    <i class="fa fa-pencil-square-o"></i> Edit
                </a>
                <a href="{{ url('delete-karyawan', $item->karyawan_id) }}" onclick="return confirm('Apakah Yakin Hapus Data Ini?')" class="btn btn-danger">
                    <i class="fa fa-trash-o"></i> Delete
                </a>
                <button type="button" class="btn btn-secondary btn-mutasi"
                    data-bs-toggle="modal"
                    data-bs-target="#mutasiModal"
                    data-id="{{ $item->karyawan_id }}"
                    data-nama="{{ $item->nama_tk }}"
                    data-paket="{{ $paketKaryawan[$item->karyawan_id]->nama_paket ?? 'Belum ada' }}">
                    <i class="fa fa-random"></i> Mutasi
                </button>
                <button type="button" class="btn btn-secondary btn-promosi"
                    data-bs-toggle="modal"
                    data-bs-target="#promosiModal"
                    data-id="{{ $item->karyawan_id }}"
                    data-nama="{{ $item->nama_tk }}"
                    data-jabatan="{{ $jabatan[$item->karyawan_id]->jabatan ?? 'Belum ada' }}">
                    <i class="fa fa-random"></i> Promosi
                </button>
                <button type="button" class="btn btn-warning btn-edit-shift"
                        data-bs-toggle="modal"
                        data-bs-target="#editShiftModal"
                        data-id="{{ $item->karyawan_id }}"
                        data-nama="{{ $item->nama_tk }}"
                        data-shift="{{ $harianShift[$item->karyawan_id]->harianshift ?? 'Belum ada' }}">
                    <i class="fa fa-calendar"></i> Edit Harian
                </button>
                <button type="button" class="btn btn-success btn-edit-area"
                        data-bs-toggle="modal"
                        data-bs-target="#editAreaModal"
                        data-id="{{ $item->karyawan_id }}"
                        data-nama="{{ $item->nama_tk }}"
                        data-area="{{ $area[$item->karyawan_id]->area ?? 'Belum ada' }}">
                    <i class="fa fa-calendar"></i> Edit Area
                </button>
                <button type="button" class="btn btn-primary btn-edit-pakaian"
                        data-bs-toggle="modal"
                        data-bs-target="#editPakaianModal"
                        data-id="{{ $item->karyawan_id }}"
                        data-nama="{{ $item->nama_tk }}"
                         data-nilai="{{ $item->pakaianTerakhir->nilai_jatah ?? 'Belum ada' }}"
    data-baju="{{ $item->pakaianTerakhir->ukuran_baju ?? 'Belum ada' }}"
    data-celana="{{ $item->pakaianTerakhir->ukuran_celana ?? 'Belum ada' }}">
                    <i class="fa fa-calendar"></i> Edit Pakaian
                </button>


            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Modal mutasi -->
 <div class="modal fade" id="mutasiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/mutasi-paket') }}" method="POST">
      @csrf
      <input type="hidden" name="karyawan_id" id="mutasi_karyawan_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mutasiModalLabel">Mutasi Paket - <span id="mutasi_nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Paket saat ini:</strong> <span id="mutasi_paket_sekarang"></span></p>
          <div class="mb-3">
            <label for="paket_id" class="form-label">Mutasi ke Paket:</label>
            <select name="paket_id" class="form-select" required>
              <option value="">-- Pilih Paket --</option>
              @foreach ($paketList as $paket)
              <option value="{{ $paket->paket_id }}">{{ $paket->paket }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="beg_date" class="form-label">Tanggal TMT</label>
            <input type="date" class="form-control" name="beg_date">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Promosi -->
 <div class="modal fade" id="promosiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/promosi-jabatan') }}" method="POST">
      @csrf
      <input type="hidden" name="karyawan_id" id="promosi_karyawan_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mutasiModalLabel">Promosi Jabatan - <span id="promosi_nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Jabatan saat ini:</strong> <span id="promosi_jabatan_sekarang"></span></p>
          <div class="mb-3">
            <label for="kode_jabatan" class="form-label">Promosi ke Jabatan:</label>
            <select name="kode_jabatan" class="form-select" required>
              <option value="">-- Pilih Jabatan --</option>
              @foreach ($jabatanList as $jabatan)
              <option value="{{ $jabatan->kode_jabatan }}">{{ $jabatan->jabatan }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="beg_date" class="form-label">Tanggal TMT</label>
            <input type="date" class="form-control" name="beg_date">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Promosi</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Harian/Shift -->
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/ganti-shift') }}" method="POST">
      @csrf
      <input type="hidden" name="karyawan_id" id="shift_karyawan_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Harian/Shift - <span id="shift_nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Shift saat ini:</strong> <span id="shift_saat_ini"></span></p>
          <div class="mb-3">
            <label class="form-label">Pilih Shift Baru:</label>
            <select name="kode_harianshift" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="1">Harian</option>
              <option value="2">Shift</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal Berlaku</label>
            <input type="date" class="form-control" name="beg_date" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Area -->
<div class="modal fade" id="editAreaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/ganti-area') }}" method="POST">
      @csrf
      <input type="hidden" name="karyawan_id" id="area_karyawan_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Area - <span id="area_nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Area saat ini:</strong> <span id="area_saat_ini"></span></p>
          <div class="mb-3">
            <label class="form-label">Pilih Area Baru:</label>
            <select name="area_id" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="1">Lapangan</option>
              <option value="2">Non Lapangan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Pakaian -->
<div class="modal fade" id="editPakaianModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/ganti-pakaian') }}" method="POST">
      @csrf
      <input type="hidden" name="karyawan_id" id="pakaian_karyawan_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Pakaian - <span id="pakaian_nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Ukuran Baju & Celana saat ini:</strong> <span id="baju_saat_ini"></span>, <span id="celana_saat_ini"></span></p>
          <div class="mb-3">
            <label class="form-label">Ukuran Baju:</label>
            <select name="ukuran_baju" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="S">S</option>
              <option value="M">M</option>
              <option value="L">L</option>
              <option value="XL">XL</option>
              <option value="XXL">XXL</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Ukuran Celana</label>
            <input type="number" class="form-control" name="ukuran_celana" min="25" max="45" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal Berlaku</label>
            <input type="date" class="form-control" name="beg_date" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

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

$(document).ready(function() {
    var table = $('#datatableSimple').DataTable();

    // Buat search khusus untuk kolom ke-0 (ID)
    $('#search-id').on('keyup', function() {
        table.column(1).search(this.value).draw(); // Kolom ke-0 berarti kolom ID
    });
});

$(document).on('click', '.btn-mutasi', function () {
    const nama = $(this).data('nama');
    const paket = $(this).data('paket');
    const id = $(this).data('id');

    $('#mutasi_nama').text(nama);
    $('#mutasi_paket_sekarang').text(paket);
    $('#mutasi_karyawan_id').val(id);
});

$(document).on('click', '.btn-promosi', function () {
    const nama = $(this).data('nama');
    const paket = $(this).data('jabatan');
    const id = $(this).data('id');

    $('#promosi_nama').text(nama);
    $('#promosi_jabatan_sekarang').text(paket);
    $('#promosi_karyawan_id').val(id);
});

$(document).on('click', '.btn-edit-shift', function () {
    const nama = $(this).data('nama');
    const shift = $(this).data('shift');
    const id = $(this).data('id');

    $('#shift_nama').text(nama);
    $('#shift_saat_ini').text(shift);
    $('#shift_karyawan_id').val(id);
});

$(document).on('click', '.btn-edit-area', function () {
    const nama = $(this).data('nama');
    const area = $(this).data('area');
    const id = $(this).data('id');

    $('#area_nama').text(nama);
    $('#area_saat_ini').text(area);
    $('#area_karyawan_id').val(id);
});

$(document).on('click', '.btn-edit-pakaian', function () {
    const nama = $(this).data('nama');
    const id = $(this).data('id');
    const nilai = $(this).data('nilai');
    const baju = $(this).data('baju');
    const celana = $(this).data('celana');

    $('#pakaian_nama').text(nama);
    $('#pakaian_karyawan_id').val(id);
    $('#nilai_saat_ini').text(nilai);
    $('#baju_saat_ini').text(baju);
    $('#celana_saat_ini').text(celana);
});



</script>

@endsection
