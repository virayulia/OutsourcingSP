<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark">
        <div class="sb-sidenav-menu">
            <div class="nav">
                
                <!-- <div class="sb-sidenav-menu-heading">Core</div> -->
                <a class="nav-link" href="{{ url('/dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <!-- <div class="sb-sidenav-menu-heading">Interface</div> -->
                <a class="nav-link" href="{{ url('/paket') }}">
                    <i class="fas fa-chart-area"></i> Data Paket
                </a>
                <a class="nav-link" href="{{ url('/penempatan') }}">
                    <i class="fas fa-chart-area"></i> Data Penempatan
                </a>
                <a class="nav-link" href="{{ url('/karyawan') }}">
                    <i class="fas fa-table"></i> Karyawan
                </a>
                <a class="nav-link" href="{{ url('/perusahaan') }}">
                    <i class="fas fa-table"></i> Vendor/Perusahaan
                </a>
                <a class="nav-link" href="{{ url('/unit-kerja') }}">
                    <i class="fas fa-table"></i> Unit Kerja
                </a>
                <a class="nav-link" href="{{ url('/datapaket') }}">
                    <i class="fas fa-table"></i> Paket
                </a>
                <a class="nav-link" href="{{ url('/ump') }}">
                    <i class="fas fa-table"></i> UMP
                </a>
            </div>
        </div>
        <!-- <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Start Bootstrap
        </div> -->
    </nav>
</div>
