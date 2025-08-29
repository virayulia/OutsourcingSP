@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<h1 class="mt-4">Dashboard</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
<div class="row">
    <div class="row ">
        <!-- Kiri: Chart Departemen -->
        <div class="col-lg-12 mb-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-center font-weight-bold mb-4">Distribusi Karyawan per Departemen</h6>
                    <div class="chart-bar" style="overflow-x: auto;">
                        <canvas id="departemenChart" style="height: 425px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Kanan: Chart kecil stacked -->
        <div class="col-6 d-flex flex-column gap-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-center font-weight-bold mb-3">Distribusi Jabatan</h6>
                    <canvas id="jabatanChart" style="height: 250px; max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-6 d-flex flex-column gap-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-center font-weight-bold mb-3">Distribusi Fungsi</h6>
                    <canvas id="fungsiChart" style="height: 250px; max-height: 250px;"></canvas>
                </div>
            </div>
        </div> 
    </div>
    <div class="row">
        <div class="col-4">
            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <h6 class="text-center small font-weight-bold mb-1">Gender</h6>
                    <canvas id="genderChart" style="height: 90px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <h6 class="text-center small font-weight-bold mb-1">Status</h6>
                    <canvas id="statusChart" style="height: 90px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <h6 class="text-center small font-weight-bold mb-1">Asal</h6>
                    <canvas id="asalChart" style="height: 90px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- Tabel Ringkasan Perusahaan -->
<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h6 class="text-center font-weight-bold mb-3">Ringkasan Perusahaan</h6>
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Perusahaan</th>
                    <th>Jumlah Aktif</th>
                    <th>Jumlah Terpilih</th>
                    <th>Selisih</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perusahaanCount as $label => $counts)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $counts['aktif'] }}</td>
                        <td>{{ $counts['jumlah'] }}</td>
                        <td>{{ $counts['jumlah'] - $counts['aktif'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Load Chart.js dan Plugin Label -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Simpan chart instance global agar bisa destroy
        const chartInstances = {};

        function destroyIfExists(id) {
            if (chartInstances[id]) {
                chartInstances[id].destroy();
            }
        }

        function makeBarChart(id, labels, dataValues, label, options = {}) {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            destroyIfExists(id);
            const ctx = canvas.getContext('2d');

            chartInstances[id] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: dataValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 20 } },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#000',
                            font: { weight: 'bold', size: 10 },
                            formatter: Math.round
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 100, font: { size: 10 } }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: { size: 10 }
                            }
                        }
                    },
                    ...options
                },
                plugins: [ChartDataLabels]
            });
        }

        function makePieChart(id, labels, dataValues, colors) {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            destroyIfExists(id);
            const ctx = canvas.getContext('2d');

            chartInstances[id] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // === Chart Departemen (Horizontal Bar) ===
        makeBarChart(
            'departemenChart',
            {!! json_encode(array_keys($departemenCount)) !!},
            {!! json_encode(array_map(fn($d) => (int) $d['jumlah'], $departemenCount)) !!},
            'Jumlah',
            {
                indexAxis: 'y',
                plugins: {
                    legend: { position: 'top' },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        font: { size: 10 }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 }, precision: 0 }
                    },
                    y: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        );

        // Aktif (tambahan dataset ke chart departemen)
        const departemenCanvas = document.getElementById('departemenChart');
        if (departemenCanvas && chartInstances['departemenChart']) {
            chartInstances['departemenChart'].data.datasets.push({
                label: 'Aktif',
                data: {!! json_encode(array_map(fn($d) => (int) $d['aktif'], $departemenCount)) !!},
                backgroundColor: 'rgba(252, 90, 90, 0.93)',
                borderColor: 'rgb(255, 102, 102)',
                borderWidth: 1
            });
            chartInstances['departemenChart'].update();
        }

        // === Chart lainnya ===
        makeBarChart('jabatanChart',
            {!! json_encode(array_keys($jabatanCount)) !!},
            {!! json_encode(array_values($jabatanCount)) !!},
            'Jumlah OS'
        );

        makeBarChart('fungsiChart',
            {!! json_encode(array_keys($fungsiCount)) !!},
            {!! json_encode(array_values($fungsiCount)) !!},
            'Jumlah OS'
        );

        makePieChart('genderChart',
            {!! json_encode(array_keys($genderCount)) !!},
            {!! json_encode(array_values($genderCount)) !!},
            ['#36A2EB', '#FF6384']
        );

        makePieChart('statusChart',
            {!! json_encode(array_keys($statusAktifCount)) !!},
            {!! json_encode(array_values($statusAktifCount)) !!},
            ['#4BC0C0', '#FFCE56']
        );

        makePieChart('asalChart',
            {!! json_encode(array_keys($asalKecamatanCount)) !!},
            {!! json_encode(array_values($asalKecamatanCount)) !!},
            [
                '#FF6384', '#36A2EB', '#FFCE56', '#7CB342','#F06292','#4BC0C0',
                '#9966FF', '#FF9F40', '#C9CBCF', '#F67019',
                '#00A950', '#B2912F', '#EC932F',
                '#8E24AA', '#26C6DA', '#D4E157'
            ]
        );
    });
</script>




@endsection
