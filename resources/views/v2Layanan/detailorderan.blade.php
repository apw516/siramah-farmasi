<style>
    div.ex3 {
        height: 380px;
        width: 100%;
        overflow-y: auto;
    }

    div.ex1 {
        height: 850px;
        width: 100%;
        overflow-y: auto;
    }
</style>
<button class="btn btn-danger" onclick="kembali()"><i class="bi bi-backspace mr-1"></i>
    Kembali</button>
<div class="container-fluid">
    <div class="row mt-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">Detail Pasien</div>
                <div class="card-body ex3">
                    <table class="table text-sm">
                        @foreach ($ts_kunjungan as $t)
                            <tr>
                                <td class="text-bold" width="30%">Nomor RM</td>
                                <td class="font-italic">{{ $t->no_rm }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Nama Pasien</td>
                                <td class="font-italic">{{ $t->nama_pasien }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Alamat</td>
                                <td class="font-italic">{{ $t->alamat_pasien }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Penjamin</td>
                                <td class="font-italic">{{ $t->nama_penjamin }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Pengirim</td>
                                <td class="font-italic">{{ $t->nama_unit }} | {{ $t->nama_dokter }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-bold bg-info">Riwayat Obat yang sudah dilayani</div>
                <div class="card-body ex3">
                    <div class="riwayat_order_hari_ini">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header text-bold"><i class="bi bi-list-ul mr-1"></i> Data resep yang dikirim</div>
        <div class="card-body">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalcariobat"><i
                    class="bi bi-plus mr-1"></i> Cari Obat</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalbuatracikan"><i
                    class="bi bi-plus mr-1"></i> Buat Racikan</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalriwayatracikan"><i
                    class="bi bi-search mr-1"></i>Riwayat Racikan</button>
            <form action="" method="post" class="form_input_obat mb-2">
                <div class="field_input_obat" id="field_input_obat_fix">
                    <div>
                        @foreach ($data_resep as $d)
                            <div class='row mt-2 text-xs'>
                                <div class='col-md-2'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Nama Obat</label>
                                        <input readonly type='text' class='form-control form-control-sm'
                                            id='nama_obat' name='nama_obat'
                                            value='{{ $d->nama_barang }} @if (isset($d->nama_racikan)) {{ $d->nama_racikan }} @endif'>
                                        <input hidden readonly type='text' class='form-control form-control-sm'
                                            id='kodebarang' name='kodebarang'
                                            value='{{ $d->kode_barang }}'placeholder='name@example.com'>
                                        <input hidden readonly type='text' class='form-control form-control-sm'
                                            id='idheader' name='idheader'
                                            value='{{ $d->id_header }}'placeholder='name@example.com'>
                                        <input hidden readonly type='text' class='form-control form-control-sm'
                                            id='iddetail' name='iddetail'
                                            value='{{ $d->id_detail }}'placeholder='name@example.com'>
                                    </div>
                                </div>
                                <div class='col-md-1'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Jenis Resep</label>
                                        <input readonly type='text' class='form-control form-control-sm'
                                            id='jenisresep' name='jenisresep' value='{{ $d->kategori_resep }}'
                                            placeholder='name@example.com'>
                                    </div>
                                </div>
                                <div class='col-md-1'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Dosis</label>
                                        <input readonly type='text' class='form-control form-control-sm'
                                            id='dosis' name='dosis' value='{{ $d->dosis }}'
                                            placeholder='name@example.com'>
                                    </div>
                                </div>
                                <div class='col-md-1'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Sediaan</label>
                                        <input readonly type='text' class='form-control form-control-sm'
                                            id='sediaan' name='sediaan'
                                            value='{{ $d->sediaan }} @if ($d->kemasan == '1') KAPSUL @elseif($d->kemasan == '2') KERTAS PERKAMEN @elseif($d->kemasan == '3') POT SALEP @endif'
                                            placeholder='name@example.com'>
                                    </div>
                                </div>
                                <div class='col-md-1'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Tipe anestesi</label>
                                        <select class='form-control form-control-sm' id='kronis' name='kronis'>
                                            <option value='80' @if ($d->tipe_anestesi == 0) selected @endif>
                                                Reguler</option>
                                            <option value='81' @if ($d->tipe_anestesi == 1) selected @endif>
                                                Kronis
                                            </option>
                                            <option value='82' @if ($d->tipe_anestesi == 2) selected @endif>
                                                Kemoterapi
                                            </option>
                                            <option value='83' @if ($d->tipe_anestesi == 3) selected @endif>
                                                Hibah
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class='col-md-1'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Jumlah</label>
                                        <input type='text' class='form-control form-control-sm' id='jumlah'
                                            name='jumlah' value='{{ $d->jumlah_layanan }}'
                                            placeholder='name@example.com'>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Aturan Pakai</label>
                                        <textarea type='text' class='form-control form-control-sm' id='aturanpakai' name='aturanpakai' value=''
                                            placeholder='Ketik aturan pakai ...'>{{ $d->aturan_pakai }}</textarea>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                    <div class='form-group'>
                                        <label for='exampleFormControlInput1'>Keterangan</label>
                                        <textarea type='text' class='form-control form-control-sm' id='keterangan' name='keterangan' value=''
                                            placeholder='Ketik keterangan obat jika ada ...'>{{ $d->keterangan }}</textarea>
                                    </div>
                                </div>
                                <i class='bi bi-x-square remove_field form-group col-md-1 text-danger' kode2=''
                                    subtot='' jenis='' nama_barang='' kode_barang='' id_stok=''
                                    harga2='' satuan='' stok='' qty='' harga=''
                                    disc='' dosis='' sub='' sub2='' status='80'
                                    jenisracik='racikan'></i>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" type="button" onclick="simpanpelayanan()"><i
                    class="bi bi-box-arrow-down mr-1"></i> Simpan</button>
        </div>
    </div>
    <input hidden type="text" value="{{ $kodekunjungan }}" id="kodekunjungan">
    <input hidden type="text" value="{{ $idantrian }}" id="idantrian">
</div>

<!-- Modal -->
<div class="modal fade" id="modalcariobat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Silahkan cari obat ...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exampleInputPassword1">Cari obat</label>
                            <input id="namaobatreguler" type="text" class="form-control form-control-sm"
                                placeholder="Masukan nama obat ..." id="exampleInputPassword1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-sm" style="margin-top:32px" id="cariobatreguler"><i
                                class="bi bi-search mr-1"></i>Cari Obat</button>
                    </div>
                </div>
                <div class="v_tabel_obat_reguler mt-2">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalbuatracikan" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Silahkan buat racikan obat ...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header font-italic bg-light">Header Racikan</div>
                    <div class="card-body">
                        <form class="formheaderracikan">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Nama Racikan</label>
                                <input type="email" class="form-control" id="namaracikan" name="namaracikan"
                                    placeholder="masukan nama racikan ...">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Jumlah Racikan</label>
                                        <input type="email" class="form-control" id="jumlahracikan"
                                            name="jumlahracikan" placeholder="Masukan jumlah racikan ...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Aturan Pakai</label>
                                        <textarea class="form-control" id="aturanpakairacikan" name="aturanpakairacikan" rows="3"
                                            placeholder="Masukan aturan pakai racikan ..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Tipe Racikan</label>
                                        <select class="form-control" id="tiperacikan" name="tiperacikan">
                                            <option value="1">Powder</option>
                                            <option value="2">Non-Powder</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Kemasan</label>
                                        <select class="form-control" id="kemasanracikan" name="kemasanracikan">
                                            <option value="1">Kapsul</option>
                                            <option value="2">Kertas Perkamen</option>
                                            <option value="3">Pot Salep</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-light">Komponen Obat Racik</div>
                    <div class="card-body">
                        <div class="form-inline">
                            <div class="form-group mx-sm-5 mb-2">
                                <label for="inputPassword2" class="sr-only">Password</label>
                                <input type="text" class="form-control" id="namakomponen"
                                    placeholder="Masukan nama obat ...">
                            </div>
                            <button type="button" class="btn btn-primary mb-2 carikomponenracik" id="carikomponenracik"><i
                                    class="bi bi-search"></i> Cari Obat</button>
                        </div>
                        <div class="v_tabel_obat_komponen mt-3">

                        </div>
                        <label for="" class="mt-2 mb-2">List Komponen Racikan</label>
                        <form action="" method="post" class="formlistkomponenracik">
                            <div class="field_komponen_racik" id="id_field">
                                <div id="">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="simpanracikan()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalriwayatracikan" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Riwayat Racikan Farmasi ...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="v_riwayat_racikan">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    var input = document.getElementById("namaobatreguler");
    input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("cariobatreguler").click();
        }
    });
    var input = document.getElementById("namakomponen");
    input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("carikomponenracik").click();
        }
    });
    function kembali() {
        $('.v_satu').removeAttr('hidden', true)
        $('.v_dua').attr('hidden', true)
        get_tabel_racikan()
        get_tabel_non_racikan()
        caripasien_manual()
    }
    $(document).ready(function() {
        ambilriwayatobat_today()
        ambilriwayatracikan()
    })
    var wrapper = $(".field_input_obat");
    $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
        e.preventDefault();
        $(this).parent('div').remove();
    })

    function simpanpelayanan() {
        var data = $('.form_input_obat').serializeArray();
        kodekunjungan = $('#kodekunjungan').val()
        idantrian = $('#idantrian').val()
        spinner = $('#loader2')
        spinner.show();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                data: JSON.stringify(data),
                kodekunjungan,
                idantrian
            },
            url: '<?= route('simpan_pelayanan_resep_reguler') ?>',
            error: function(data) {
                spinner.hide()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
            },
            success: function(data) {
                spinner.hide()
                if (data.kode == 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oopss...',
                        text: data.message,
                        footer: ''
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'OK',
                        text: data.message,
                        footer: ''
                    })
                    document.getElementById('field_input_obat_fix').innerHTML = "";
                    ambilriwayatobat_today()
                }
            }
        });
    }

    function ambilriwayatobat_today() {
        kodekunjungan = $('#kodekunjungan').val()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kodekunjungan
            },
            url: '<?= route('riwayat_obat_hari_ini') ?>',
            success: function(response) {
                $('.riwayat_order_hari_ini').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }

    function cariobatreguler() {
        nama = $('#namaobatreguler').val()
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                nama
            },
            url: '<?= route('cari_obat_reguler2') ?>',
            success: function(response) {
                $('.v_tabel_obat_reguler').html(response);
                spinner.hide()
            }
        });
    }

    function carikomponenracikan() {
        nama = $('#namakomponen').val()
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                nama,
            },
            url: '<?= route('cari_obat_komponen_racik') ?>',
            success: function(response) {
                $('.v_tabel_obat_komponen').html(response);
                spinner.hide()
            }
        });
    }

    function simpanracikan() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data racikan akan disimpan ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, simpan"
        }).then((result) => {
            if (result.isConfirmed) {
                var dataheader = $('.formheaderracikan').serializeArray();
                var datalist = $('.formlistkomponenracik').serializeArray();
                kodekunjungan = $('#kodekunjungan').val()
                spinner = $('#loader')
                spinner.show();
                $.ajax({
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        dataheader: JSON.stringify(dataheader),
                        datalist: JSON.stringify(datalist),
                        kodekunjungan
                    },
                    url: '<?= route('v2_add_draft_komponen') ?>',
                    error: function(data) {
                        spinner.hide()
                        Swal.fire({
                            icon: 'error',
                            title: 'Ooops....',
                            text: 'Sepertinya ada masalah......',
                            footer: ''
                        })
                    },
                    success: function(response, data) {
                        spinner.hide()
                        var wrapper = $(".field_input_obat");
                        $('#modalobatracik').modal('hide');
                        document.getElementById('id_field').innerHTML = "";
                        $(wrapper).append(response);
                        $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                            e.preventDefault();
                            $(this).parent('div').remove();
                            x--;
                        })
                    }
                });
            }
        });
    }
    $("#cariobatreguler").on('click', function(event) {
        nama = $('#namaobatreguler').val()
        spinner = $('#loader2')
        spinner.show();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                nama
            },
            url: '<?= route('cari_obat_reguler2') ?>',
            success: function(response) {
                $('.v_tabel_obat_reguler').html(response);
                spinner.hide()
            }
        });
    })
    $("#carikomponenracik").on('click', function(event) {
        nama = $('#namakomponen').val()
        spinner = $('#loader2')
        spinner.show();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                nama,
            },
            url: '<?= route('cari_obat_komponen_racik') ?>',
            success: function(response) {
                $('.v_tabel_obat_komponen').html(response);
                spinner.hide()
            }
        });
    })
    function ambilriwayatracikan()
    {
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}"
            },
            url: '<?= route('riwayat_racikan_farmasi') ?>',
            success: function(response) {
                $('.v_riwayat_racikan').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }
</script>
