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
        <div class="col-md-12">
            <div id="v_1" class="v_1">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab"><i
                                        class="bi bi-list-ul"></i> Data
                                    Order</a></li>
                            <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab"><i
                                        class="bi bi-search"></i> Pencarian Pasien</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#riwayat_R" data-toggle="tab"><i
                                        class="bi bi-clock-history"></i> Riwayat Resep</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="v_order_poli_klinik">

                                </div>
                            </div>
                            <div class="tab-pane" id="timeline">
                                <div class="v_pencarian_pasien">

                                </div>
                            </div>
                            <div class="tab-pane" id="riwayat_R">
                                <div class="v_riwayat_r">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="inputEmail4">Tanggal Awal</label>
                                            <input type="date" class="form-control" id="tglawal_r_r"
                                                value="{{ $now }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4">Tanggal Akhir</label>
                                            <input type="date" class="form-control" id="tglakhir_r_r"
                                                value="{{ $now }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <button class="btn btn-primary" style="margin-top:32px"
                                                onclick="get_riwayat_resep()"><i class="bi bi-search"></i> Cari
                                                Riwayat</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="v_t_riwayat_r">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div hidden id="v_2" class="v_2">

            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            get_orderan_poli()
            get_pencarian_pasien()
            get_riwayat_resep()
        })

        function get_riwayat_resep() {
            spinner = $('#loader')
            spinner.show();
            tglawal = $('#tglawal_r_r').val()
            tglakhir = $('#tglakhir_r_r').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tglawal,
                    tglakhir
                },
                url: '<?= route('ambil_riwayat_resep') ?>',
                success: function(response) {
                    spinner.hide();
                    $('.v_t_riwayat_r').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }

        function get_orderan_poli() {
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                url: '<?= route('ambil_orderan_poli') ?>',
                success: function(response) {
                    $('.v_order_poli_klinik').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }

        function get_pencarian_pasien() {
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                url: '<?= route('ambil_form_pencarian_pasien') ?>',
                success: function(response) {
                    $('.v_pencarian_pasien').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });
        }
    </script>
@endsection
