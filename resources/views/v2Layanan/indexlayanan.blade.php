@extends('templates.main')
@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Layanan Resep</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Layanan Resep</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="v_satu">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Antrian Non Racikan</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Tanggal pencarian</label>
                                        <input type="date" class="form-control form-control-sm"
                                            id="tanggalantriannonracikan" placeholder="name@example.com"
                                            value="{{ $now }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-sm" onclick="get_tabel_non_racikan()"
                                        style="margin-top:32px"><i class="bi bi-search mr-2"></i>Cari Antrian</button>
                                </div>
                            </div>
                            <div class="v_tabel_antrian_non_racikan"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Antrian Racikan</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Tanggal pencarian</label>
                                        <input type="date" class="form-control form-control-sm"
                                            id="tanggalantrianracikan" placeholder="name@example.com"
                                            value="{{ $now }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-sm" onclick="get_tabel_racikan()"
                                        style="margin-top:32px"><i class="bi bi-search mr-2"></i>Cari Antrian</button>
                                </div>
                            </div>
                            <div class="v_tabel_antrian_racikan"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Pencarian Pasien</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tanggal Awal</label>
                                <input type="date" class="form-control" id="tanggalawal"
                                    aria-describedby="emailHelp" value="{{ $now }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggalakhir"
                                    aria-describedby="emailHelp" value="{{ $now }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button style="margin-top:32px"class="btn btn-success" onclick="caripasien_manual()"><i class="bi bi-search mr-2"></i>Cari Pasien</button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="v_dta_pasien">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div hidden class="v_dua">

        </div>
    </section>
    <script>
        $(document).ready(function() {
            get_tabel_racikan()
            get_tabel_non_racikan()
            caripasien_manual()
        })

        function get_tabel_non_racikan() {
            tanggal = $('#tanggalantriannonracikan').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal
                },
                url: '<?= route('ambil_antrian_non_racikan') ?>',
                success: function(response) {
                    $('.v_tabel_antrian_non_racikan').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }

        function get_tabel_racikan() {
            tanggal = $('#tanggalantrianracikan').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal
                },
                url: '<?= route('ambil_antrian_racikan') ?>',
                success: function(response) {
                    $('.v_tabel_antrian_racikan').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }
        function caripasien_manual()
        {
            awal = $('#tanggalawal').val()
            akhir = $('#tanggalakhir').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    awal,akhir
                },
                url: '<?= route('tampildatapasien') ?>',
                success: function(response) {
                    $('.v_dta_pasien').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }
    </script>
@endsection
