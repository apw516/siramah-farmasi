@extends('templates.main')
@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemakaian Obat</h1>
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
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Tanggal awal</label>
                    <input type="date" class="form-control" id="tanggalawal" placeholder="name@example.com"
                        value="{{ $now }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Tanggal akhir</label>
                    <input type="date" class="form-control" id="tanggalakhir" placeholder="name@example.com"
                        value="{{ $now }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Pilih Ruangan</label>
                    <select class="form-control" id="unit">
                        <option>Silahkan pilih</option>
                        @foreach ($mt_unit as $u)
                            <option value="{{ $u->kode_unit }}">{{ $u->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success" style="margin-top:32px" onclick="caridata()">Cari Data</button>
            </div>
        </div>
        <div class="v_dua" hidden>
            <iframe src="" width="100%" height="1000px"></iframe>
        </div>
    </section>
@endsection
<script>
    function caridata() {
        awal = $('#tanggalawal').val()
        akhir = $('#tanggalakhir').val()
        unit = $('#unit').val()
        $('.v_dua').removeAttr('hidden', true)
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                awal,akhir,unit
            },
            url: '<?= route('ambil_data_pemakaian') ?>',
            success: function(response) {
                $('.v_dua').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }
</script>
