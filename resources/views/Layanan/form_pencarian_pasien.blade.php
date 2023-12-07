<form class="form-inline">
    <div class="form-group mb-2">
        <label for="staticEmail2" class="sr-only">Tanggal</label>
        <input type="date" class="form-control" id="tanggalcari" value="{{ $now }}">
    </div>
    <div class="form-group mx-sm-3 mb-2">
        <label for="inputPassword2" class="sr-only">Poliklinik</label>
        <select class="form-control" id="poliklinik">
            <option value="0">Silahkan Pilih Poliklinik</option>
            @foreach ($unit as $u)
                <option value="{{ $u->kode_unit }}">{{ $u->nama_unit }}</option>
            @endforeach
        </select>
    </div>
    <button onclick="caripasien()" type="button" class="btn btn-primary mb-2">Cari Pasien</button>
</form>
<div class="row">
    <div class="col-md-12">
        <div class="v_hasil_pencarian_pasien">

        </div>
    </div>
</div>
<script>
    function caripasien() {
        tgl = $('#tanggalcari').val()
        unit = $('#poliklinik').val()
        $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tgl,
                    unit
                },
                url: '<?= route('ambil_data_pencarian_pasien') ?>',
                success: function(response) {
                    $('.v_hasil_pencarian_pasien').html(response);
                    // $('#daftarpxumum').attr('disabled', true);
                }
            });

    }
</script>
