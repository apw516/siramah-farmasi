<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset('public/adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SIRAMAH FARMASI</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('public/adminlte/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{auth()->user()->nama }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="" class="nav-link @if($menu == 'dashboard') active @endif">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Dashboard Farmasi
                        </p>
                    </a>
                </li>
                <li class="nav-header">FARMASI</li>
                <li class="nav-item">
                    <a href="{{ route('layananresep')}}" class="nav-link @if($menu == 'Layananresep') active @endif">
                        <i class="nav-icon fas fa-archive"></i>
                        <p>
                            Layanan Resep
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kartustok')}}" class="nav-link @if($menu == 'kartustok') active @endif">
                        <i class="nav-icon fas fa-archive"></i>
                        <p>
                           Kartu Stok
                        </p>
                    </a>
                </li>
                <li class="nav-header">Akun</li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="nav-icon far fa-circle text-danger"></i>
                        <p class="text">Info Akun</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="logout()">
                        <i class="nav-icon far fa-circle text-warning"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
