<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIRAMAH - FARMASI</title>
    <link rel="icon" href="{{ asset('public/adminlte/dist/img/LOGOX.svg') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('public/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet"
        href="{{ asset('public/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('public/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ asset('public/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <script src="{{ asset('public/adminlte/dist/js/jquery-3.js') }}"></script>
    <script src="{{ asset('public/adminlte/dist/js/jquery-ui.min.js') }}"></script>
    {{-- <script src="{{ asset('public/adminlte/plugins/jquery/jquery.min.js') }}"></script> --}}
    <!-- Bootstrap -->
    <script src="{{ asset('public/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('public/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('public/adminlte/dist/js/adminlte.js') }}"></script>

    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{ asset('public/adminlte/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('public/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('public/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .datepicker {
            z-index: 1600 !important;
            /* has to be larger than 1050 */
        }

        .ui-front {
            z-index: 9999999 !important;
        }

        .modal {
            overflow: auto !important;
        }

        .preloader2 {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #fff;
            opacity: 0.9;
        }

        .preloader2 .loading {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font: 14px arial;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">
    <div class="preloader2" id="loader2">
        <div class="loading">
            <img src="{{ asset('public/img/fb.gif') }}" width="80">
            <p>Harap Tunggu</p>
        </div>
    </div>
    <div class="wrapper">
        @include('templates.navbar')
        @include('templates.sidebar')
        <div class="content-wrapper">
            @yield('container')
        </div>
        <footer class="main-footer">
            <strong>Copyright &copy; 2023 <a href="https://adminlte.io">SemeruSmart</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>
    <script>
        $(".preloader2").fadeOut();
    </script>
</body>
<script>
    function logout() {
        Swal.fire({
            title: "Anda Yakin Ingin Log out ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Logout !"
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = "<?= route('logout') ?>";
            }
        });
    }
</script>

</html>
