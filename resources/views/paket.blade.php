@extends('layouts.main')

@section('title', 'Paket')

@section('content')
@php
    $total_kontrak_all = 0;
    $total_kontrak_tahunan_all = 0;
    $total_thr_bln = 0;
    $total_thr_thn = 0;
    $total_pakaian_all = 0;
    $total_jml_fix_cost = 0;
    $total_fix_cost = 0;
    $total_upah_pokok = 0;
    $total_tj_umum = 0;
    $total_tj_lokasi = 0;
    $total_tj_jabatan = 0;
    $total_tj_masakerja = 0;
    $total_tj_penyesuaian = 0;
    $total_tj_resiko = 0;
    $total_tj_shift = 0;
    $total_seluruh_variabel = 0;


    foreach ($data as $item) {
        $ump = $item->lokasi['ump']['ump'] ?? 0;
        $ump_sumbar = $item->ump_sumbar ?? 0;

        $upah_pokok = round($ump_sumbar * 0.92);
        $total_upah_pokok += $upah_pokok;

        $tj_umum = round($ump_sumbar * 0.08);
        $total_tj_umum += $tj_umum;

        // Pembulatan selisih dulu sebelum max untuk tiru Excel
        $selisih_ump = round($ump - $ump_sumbar);
        $tj_lokasi = $item->kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);
        $total_tj_lokasi += $tj_lokasi;

        $tj_jabatan = round($item->tunjangan_jabatan ?? 0);
        $total_tj_jabatan += $tj_jabatan;

        $tj_masakerja = round($item->tunjangan_masakerja ?? 0);
        $total_tj_masakerja += $tj_masakerja;

        $tj_suai = round($item->tunjangan_penyesuaian ?? 0);
        $total_tj_penyesuaian += $tj_suai;

        $tj_harianshift = round($item->harianshift['tunjangan_shift'] ?? 0);
        $total_tj_shift += $tj_harianshift;

        $tj_resiko = ($item->kode_resiko == 2) ? 0 : round($item->resiko['tunjangan_resiko'] ?? 0);
        $total_tj_resiko += $tj_resiko;

        $tj_presensi = round($upah_pokok * 0.08);
        $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi;

        $t_tetap = $tj_umum + $tj_jabatan + $tj_masakerja;

        $komponen_gaji = $upah_pokok + $t_tetap + $tj_lokasi;
        $bpjs_kesehatan = round(0.04 * $komponen_gaji);
        $bpjs_ketenagakerjaan = round(0.0689 * $komponen_gaji);

        $uang_jasa = $item->perusahaan_id == 38
            ? round(($upah_pokok + $t_tetap + $t_tdk_tetap) / 12)
            : 0;

        $kompensasi = round($komponen_gaji / 12);

        $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
        $fee_fix_cost = round(0.10 * $fix_cost);
        $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

        $total_fix_cost += $fix_cost;
        $total_jml_fix_cost += $jumlah_fix_cost;

        // Lembur
        $quota_jam_perkalian = 2 * ($item->kuota ?? 0);
        $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
        $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
        $fee_lembur = round(0.025 * $nilai_lembur);
        $total_variabel = $nilai_lembur + $fee_lembur;
        $total_seluruh_variabel += $total_variabel;

        $total_kontrak = $jumlah_fix_cost + $total_variabel;
        $total_kontrak_tahunan = $total_kontrak * 12;

        $total_kontrak_all += $total_kontrak;
        $total_kontrak_tahunan_all += $total_kontrak_tahunan;

        // THR
        $thr = round(($upah_pokok + $t_tetap) / 12);
        $fee_thr = round($thr * 0.05);
        $thr_bln = $thr + $fee_thr;
        $thr_thn = $thr_bln * 12;

        $total_thr_bln += $thr_bln;
        $total_thr_thn += $thr_thn;

        // Pakaian
        $pakaian = 600000;
        $fee_pakaian = round(0.05 * $pakaian);
        $total_pakaian = $pakaian + $fee_pakaian;
        $total_pakaian_all += $total_pakaian;
    }
@endphp

<h3 class="mt-4">Data Paket</h3>
<style>

    .scroll-container {
        overflow-x: auto;
        overflow-y: hidden;
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
    }

    .scroll-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }

    .scroll-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.5rem;
    }

    .scroll-row .card {
        flex: 0 0 auto;
    }
</style>

<div class="scroll-container">
    <div class="scroll-row">
        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-primary text-white mb-2">
                <div class="card-body">Total Jml Fix Cost/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-primary text-white mb-2">
                <div class="card-body">Total Variabel Cost/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}
                </div>
            </div>
        </div>
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
                <div class="card-body">Total Kontrak/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
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
                <div class="card-body">Total THR/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_thr_thn, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-primary text-white mb-2">
                <div class="card-body">Total Pakaian/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>
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
            <th>Paket</th>
            <th>Aktif Mulai</th>
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
    <tbody>
    @foreach ($data as $item)
        <tr>
            @php 
            $upah_pokok = $item->ump_sumbar * 0.92;
        $tj_umum = $item->ump_sumbar * 0.08;
        $selisih_ump = round(($item->lokasi['ump']['ump'] ?? 0) - ($item->ump_sumbar ?? 0));
        $tj_lokasi = $item->kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);
        $tj_jabatan = $item->tunjangan_jabatan;
        $tj_suai = $item->tunjangan_penyesuaian;
        $tj_resiko = $item->kode_resiko == 2 ? 0 : $item->resiko['tunjangan_resiko'];
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

        $quota_jam_perkalian = 2 * $item->kuota;
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
            <td>{{ $loop->iteration }}</td>
            <td>{{$item->osis_id}}</td>
            <td>{{$item->nama_tk}}</td>
            <td>{{$item->perusahaan}}</td>
            <td>{{$item->paket}}</td>
            <td>{{$item->aktif_mulai}}</td>
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
                        window.location.href = '/tambah-pengganti/{{ $item->karyawan_id }}';
                    } else {
                        // Jika klik batal, reload halaman
                        location.reload();
                    }
                });
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
            table.column(4).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        $('#filterAktifMulai').on('change', function () {
            var val = this.value;
            table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
        });
    }
});


</script>

@endsection