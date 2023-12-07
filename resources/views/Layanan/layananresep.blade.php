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
                            <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Data
                                    Order</a></li>
                            <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Pencarian Pasien</a>
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
        })

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
        function get_pencarian_pasien()
        {
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
