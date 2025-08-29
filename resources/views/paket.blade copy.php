@extends('layouts.main')

@section('title', 'Paket')

@section('content')
@php
    $total_kontrak_all = 0;
    $total_kontrak_tahunan_all = 0;
    $total_thr_bln = 0;
    $total_thr_thn = 0;
    $total_pakaian_all = 0;

    foreach ($data as $item) {
        $upah_pokok = $item->ump_sumbar * 0.92;
        $tj_umum = $item->ump_sumbar * 0.08;
        $tj_lokasi = $item->ump_lokasi - $item->ump_sumbar;
        $tj_jabatan = $item->tunjangan_jabatan;
        $tj_suai = $item->tunjangan_penyesuaian;
        $tj_resiko = $item->kode_resiko == 1 ? 0 : $item->tunjangan_resiko;
        $tj_presensi = $upah_pokok * 0.08;
        $tj_harianshift = $item->tunjangan_shift ?? 0;
        $tj_masakerja = $item->tunjangan_masakerja ?? 0;

        $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi;
        $t_tetap = $tj_umum + $tj_jabatan + $tj_masakerja;

        $bpjs_kesehatan = 0.04 * ($upah_pokok + $t_tetap + $tj_lokasi);
        $bpjs_ketenagakerjaan = 0.0689 * ($upah_pokok + $t_tetap + $tj_lokasi);

        $uang_jasa = ($item->perusahaan_id == 38 ? ($upah_pokok + $t_tetap + $t_tdk_tetap) / 12 : 0);
        $kompensasi = ($upah_pokok + $t_tetap + $tj_lokasi) / 12;

        $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
        $fee_fix_cost = round(0.10 * $fix_cost);
        $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

        $quota_jam_perkalian = 2 * $item->quota_jam_real;
        $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
        $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
        $fee_lembur = 0.025 * $nilai_lembur;
        $total_variabel = $nilai_lembur + $fee_lembur;

        $total_kontrak = $jumlah_fix_cost + $total_variabel;
        $total_kontrak_tahunan = $total_kontrak*12;

        $total_kontrak_all += $total_kontrak;
        $total_kontrak_tahunan_all += $total_kontrak_tahunan;

        $thr = round(($upah_pokok+$t_tetap)/12);
        $fee_thr = round($thr*0.05);
        $thr_bln = $thr+$fee_thr;
        $thr_thn = $thr_bln*12;

        $total_thr_bln += $thr_bln;
        $total_thr_thn += $thr_thn;

        $pakaian = 600000;
        $fee_pakaian = round(0.05*$pakaian);
        $total_pakaian = $pakaian+$fee_pakaian;
        $total_pakaian_all += $total_pakaian;
       
    }

@endphp
<h3 class="mt-4">Data Paket</h3>
<div class="row g-1">
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <div class="card bg-primary text-white mb-2">
            <div class="card-body">Total Kontrak/Bln</div>
            <div class="card-footer">
                Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <div class="card bg-primary text-white mb-2">
            <div class="card-body">Total THR/Bln</div>
            <div class="card-footer">
                Rp{{ number_format($total_thr_bln, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <div class="card bg-primary text-white mb-2">
            <div class="card-body">Total Kontrak/Thn</div>
            <div class="card-footer">
                Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <div class="card bg-primary text-white mb-2">
            <div class="card-body">Total THR/Thn</div>
            <div class="card-footer">
                Rp{{ number_format($total_thr_thn, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <div class="card bg-primary text-white mb-2">
            <div class="card-body">Total Pakaian</div>
            <div class="card-footer">
                Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>
<!-- Filter Paket & Search Table (berdampingan) -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
  <div>
    <label for="filterPaket">Filter Paket:</label>
    <select id="filterPaket">
      <option value="">Semua Paket</option>
      <option value="1">Paket 1</option>
      <option value="2">Paket 2</option>
      <option value="3">Paket 3</option>
      <!-- Tambah sesuai kebutuhan -->
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
            <th>Paket</th>
            <th>Upah Pokok</th>
            <th>Tj. Umum</th>
            <th>Tj. Lokasi</th>
            <th>Tj. Jabatan</th>
            <th>Tj. Masa Kerja</th>
            <th>Tj. Suai</th>
            <th>Tj. Resiko</th>
            <th>Tj. Shift</th>
            <th>Tj. Presensi</th>
            <th>T.Tetap</th>
            <th>T.Tidak Tetap</th>
            <th>BPJS Kesehatan</th>
            <th>BPJS Ketenagakerjaan</th>
            <th>Uang Jasa</th>
            <th>Kompensasi</th>
            <th>Fix Cost</th>
            <th>Fee Fix Cost</th>
            <th>Jumlah Fix Cost</th>
            <th>Quota Jam Perkalian</th>
            <th>Tarif Lembur</th>
            <th>Nilai Lembur</th>
            <th>Fee Lembur</th>
            <th>Total Variabel</th>
            <th>Total Kontrak/Bln</th>
            <th>Total Kontrak/Thn</th>
            <th>THR</th>
            <th>Fee THR</th>
            <th>THR/Bln</th>
            <th>THR/Thn</th>
            <th>Total Pakaian</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>No.</th>
            <th>OSIS ID</th>
            <th>Nama</th>
            <th>Vendor/Perusahaan</th>
            <th>Paket</th>
            <th>Upah Pokok</th>
            <th>Tj. Umum</th>
            <th>Tj. Lokasi</th>
            <th>Tj. Jabatan</th>
            <th>Tj. Masa Kerja</th>
            <th>Tj. Suai</th>
            <th>Tj. Resiko</th>
            <th>Tj. Shift</th>
            <th>Tj. Presensi</th>
            <th>T.Tetap</th>
            <th>T.Tidak Tetap</th>
            <th>BPJS Kesehatan</th>
            <th>BPJS Ketenagakerjaan</th>
            <th>Uang Jasa</th>
            <th>Kompensasi</th>
            <th>Fix Cost</th>
            <th>Fee Fix Cost</th>
            <th>Jumlah Fix Cost</th>
            <th>Quota Jam Perkalian</th>
            <th>Tarif Lembur</th>
            <th>Nilai Lembur</th>
            <th>Fee Lembur</th>
            <th>Total Variabel</th>
            <th>Total Kontrak/Bln</th>
            <th>Total Kontrak/Thn</th>
            <th>THR</th>
            <th>Fee THR</th>
            <th>THR/Bln</th>
            <th>THR/Thn</th>
            <th>Total Pakaian</th>
        </tr>
    </tfoot>
    <tbody>
    @foreach ($data as $item)
        <tr>
            @php 
            $upah_pokok = $item->ump_sumbar * 0.92;
        $tj_umum = $item->ump_sumbar * 0.08;
        $tj_lokasi = $item->ump_lokasi - $item->ump_sumbar;
        $tj_jabatan = $item->tunjangan_jabatan;
        $tj_suai = $item->tunjangan_penyesuaian;
        $tj_resiko = $item->kode_resiko == 1 ? 0 : $item->tunjangan_resiko;
        $tj_presensi = $upah_pokok * 0.08;
        $tj_harianshift = $item->tunjangan_shift ?? 0;
        $tj_masakerja = $item->tunjangan_masakerja ?? 0;

        $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi;
        $t_tetap = $tj_umum + $tj_jabatan + $tj_masakerja;

        $bpjs_kesehatan = 0.04 * ($upah_pokok + $t_tetap + $tj_lokasi);
        $bpjs_ketenagakerjaan = 0.0689 * ($upah_pokok + $t_tetap + $tj_lokasi);

        $uang_jasa = ($item->perusahaan_id == 38 ? ($upah_pokok + $t_tetap + $t_tdk_tetap) / 12 : 0);
        $kompensasi = ($upah_pokok + $t_tetap + $tj_lokasi) / 12;

        $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
        $fee_fix_cost = round(0.10 * $fix_cost);
        $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

        $quota_jam_perkalian = 2 * $item->quota_jam_real;
        $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
        $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
        $fee_lembur = 0.025 * $nilai_lembur;
        $total_variabel = $nilai_lembur + $fee_lembur;

        $total_kontrak = $jumlah_fix_cost + $total_variabel;
        $total_kontrak_tahunan = $total_kontrak*12;

        $total_kontrak_all += $total_kontrak;
        $total_kontrak_tahunan_all += $total_kontrak_tahunan;

        $thr = round(($upah_pokok+$t_tetap)/12);
        $fee_thr = round($thr*0.05);
        $thr_bln = $thr+$fee_thr;
        $thr_thn = $thr_bln*12;

        $total_thr_bln += $thr_bln;
        $total_thr_thn += $thr_thn;

        $pakaian = 600000;
        $fee_pakaian = round(0.05*$pakaian);
        $total_pakaian = $pakaian+$fee_pakaian;

            @endphp
            <th>{{ $loop->iteration }}</th>
            <td>{{$item->osis_id}}</td>
            <td>{{$item->nama_tk}}</td>
            <td>{{$item->perusahaan}}</td>
            <td>{{$item->paket}}</td>
            <td>{{ number_format($upah_pokok, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_umum, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_lokasi, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_jabatan, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_masakerja, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_suai, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_resiko, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_harianshift, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_presensi, 0, ',', '.') }}</td>
            <td>{{ number_format($t_tetap, 0, ',', '.') }}</td>
            <td>{{ number_format($t_tdk_tetap, 0, ',', '.') }}</td>
            <td>{{ number_format($bpjs_kesehatan, 0, ',', '.') }}</td>
            <td>{{ number_format($bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
            <td>{{ number_format($uang_jasa, 0, ',', '.') }}</td>
            <td>{{ number_format($kompensasi, 0, ',', '.') }}</td>
            <td>{{ number_format($fix_cost, 0, ',', '.') }}</td>
            <td>{{ number_format($fee_fix_cost, 0, ',', '.') }}</td>
            <td>{{ number_format($jumlah_fix_cost, 0, ',', '.') }}</td>
            <td>{{ number_format($quota_jam_perkalian, 0, ',', '.') }}</td>
            <td>{{ number_format($tarif_lembur, 0, ',', '.') }}</td>
            <td>{{ number_format($nilai_lembur, 0, ',', '.') }}</td>
            <td>{{ number_format($fee_lembur, 0, ',', '.') }}</td>
            <td>{{ number_format($total_variabel, 0, ',', '.') }}</td>
            <td>{{ number_format($total_kontrak, 0, ',', '.') }}</td>
            <td>{{ number_format($total_kontrak_tahunan, 0, ',', '.') }}</td>
            <td>{{ number_format($thr, 0, ',', '.') }}</td>
            <td>{{ number_format($fee_thr, 0, ',', '.') }}</td>
            <td>{{ number_format($thr_bln, 0, ',', '.') }}</td>
            <td>{{ number_format($thr_thn, 0, ',', '.') }}</td>
            <td>{{ number_format($total_pakaian, 0, ',', '.') }}</td>
        </tr>
        
    @endforeach 
    </tbody>
</table>

<script>
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
            // Kirim request ke server
            $.get("/set-berhenti/" + id, function(response) {
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
                        window.location.href = '/tambah-pengganti/{{ $item->penempatan_id }}';
                    } else {
                        // Jika klik batal, reload halaman
                        location.reload();
                    }
                });
            });
        }
    });
});

  document.addEventListener('DOMContentLoaded', function () {
    const table = new simpleDatatables.DataTable("#datatablesSimple");

    const filterSelect = document.getElementById("filterPaket");

    filterSelect.addEventListener("change", function () {
      const value = this.value;

      if (value === "") {
        table.columns(4).search(""); // kosongkan filter kolom Paket
      } else {
        table.columns(4).search("^" + value + "$", true, false); // filter persis Paket X
      }

      table.update();
    });
  });

</script>

@endsection