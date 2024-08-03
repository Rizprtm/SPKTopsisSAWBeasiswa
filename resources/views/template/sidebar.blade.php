<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link bg-primary">
        {{-- <img src="{{ asset('assets/img/d.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">SPK BEASISWA</span>
    </a>


    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/img/amongus.webp') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Admin</a>
            </div>
        </div> --}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                @if (Auth::user()->role == 'admin')
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>
                                Home
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('periode_beasiswa') }}" class="nav-link">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>
                                Periode Beasiswa
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('criteriaweights') }}" class="nav-link">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>
                                Criteria & Weight
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('admin/alternative') }}" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Mahasiswa
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/decision') }}" class="nav-link">
                            <i class="nav-icon fas fa-table"></i>
                            <p>
                                Decision Matrix
                            </p>
                        </a>
                    </li>
                    <li class="nav-header">SAW</li>

                    <li class="nav-item">
                        <a href="{{ url('admin/normalizationSAW') }}" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Normalized Matrix
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('admin/rankSAW') }}" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Rank
                            </p>
                        </a>
                    </li>
                    <li class="nav-header">TOPSIS</li>

                    <li class="nav-item">
                        <a href="{{ url('admin/normalization') }}" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Normalized Matrix
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/weightednormalization') }}" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Weighted Normalization
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/idealsolution') }}" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Ideal Solution
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/idealsolutiondistance') }}" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Ideal Solution Distance
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/rank') }}" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Rank
                            </p>
                        </a>
                    </li>


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    @endif
    @if (Auth::user()->role == 'co_admin')
        <li class="nav-item">
            <a href="{{ url('co_admin') }}" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>
                    Home
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/alternative') }}" class="nav-link">
                <i class="nav-icon fas fa-database"></i>
                <p>
                    Mahasiswa
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/decision') }}" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>
                    Decision Matrix
                </p>
            </a>
        </li>
        <li class="nav-header">SAW</li>

        <li class="nav-item">
            <a href="{{ url('admin/normalizationSAW') }}" class="nav-link">
                <i class="nav-icon far fa-chart-bar"></i>
                <p>
                    Normalized Matrix
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('admin/rankSAW') }}" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                    Rank
                </p>
            </a>
        </li>
        <li class="nav-header">TOPSIS</li>

        <li class="nav-item">
            <a href="{{ url('admin/normalization') }}" class="nav-link">
                <i class="nav-icon far fa-chart-bar"></i>
                <p>
                    Normalized Matrix
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/weightednormalization') }}" class="nav-link">
                <i class="nav-icon far fa-chart-bar"></i>
                <p>
                    Weighted Normalization
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/idealsolution') }}" class="nav-link">
                <i class="nav-icon far fa-chart-bar"></i>
                <p>
                    Ideal Solution
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/idealsolutiondistance') }}" class="nav-link">
                <i class="nav-icon far fa-chart-bar"></i>
                <p>
                    Ideal Solution Distance
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/rank') }}" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                    Rank
                </p>
            </a>
        </li>
    @endif
    @if (Auth::user()->role == 'mahasiswa')
        <li class="nav-item">
            <a href="{{ url('profile') }}" class="nav-link">
                <i class="nav-icon far fa-user"></i>
                <p>
                    Profile
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('periode_beasiswa') }}" class="nav-link">
                <i class="nav-icon fas fa-cube"></i>
                <p>
                    Periode Beasiswa
                </p>
            </a>
        </li>
    @endif
    <!-- Sidebar -->

    <!-- /.sidebar -->
</aside>
