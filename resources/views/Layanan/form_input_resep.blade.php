<button class="btn btn-lg btn-danger mb-4" onclick="kembali()"><i class="bi bi-backspace"></i> Kembali</button>
<input hidden type="text" value="{{ $datakunjungan[0]->kode_kunjungan }}" id="kodekunjungan">
<input hidden type="text" value="{{ $datakunjungan[0]->no_rm }}" id="rm">
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-info">Data Pasien</div>
            <div class="card-body">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <h3 class="profile-username text-center text-bold">{{ $datapasien[0]->nama_px }}</h3>

                    <p class="text-dark text-center text-bold">{{ $datapasien[0]->alamatnya }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Nomor RM</b> <a class="float-right text-dark text-bold">{{ $datapasien[0]->no_rm }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Tanggal lahir</b> <a
                                class="float-right  text-dark text-bold">{{ $datapasien[0]->tgl_lahir }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Penjamin</b> <a
                                class="float-right text-dark text-bold">{{ $datakunjungan[0]->nama_penjamin }}</a>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">Data Order</div>
            <div class="card-body">
                <div class="v_data_order">

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-info">Riwayat Obat Hari ini</div>
            <div class="card-body">
                <div class="riwayat_obat_hari_ini">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#activity2" data-toggle="tab">Resep Obat</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="active tab-pane" id="activity2">
                    <div class="v_obat_reguler">
                        <button class="btn btn-warning" data-toggle="modal" data-target="#modalcari_obatreguler"><i
                                class="bi bi-search"></i> Cari Obat Reguler</button>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#modalcari_obatracik"><i
                                class="bi bi-search"></i> Cari Obat Racik</button>
                        <div class="row mt-1">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Nama Barang / Layanan</label>
                                    <input readonly type="text" class="form-control text-sm" id="pre_nama_barang"
                                        placeholder="Nama Barang / Layanan">
                                    <input hidden readonly type="text" class="form-control text-xs" id="pre_kode"
                                        placeholder="Nama Barang / Layanan">
                                    <input hidden readonly type="text" class="form-control text-xs" id="pre_id_ti"
                                        placeholder="Nama Barang / Layanan">
                                    <input hidden type="text" class="form-control text-xs" id="harga2"
                                        placeholder="Nama Barang / Layanan">
                                    <input hidden readonly type="text" class="form-control text-xs" id="pre_satuan"
                                        placeholder="Nama Barang / Layanan">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Stok</label>
                                    <input readonly type="text" class="form-control text-sm" id="pre_stok"
                                        placeholder="stok">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">QTY</label>
                                    <input type="text" class="form-control" id="pre_qty" placeholder="qty"
                                        value="0" oninput="hitungsubtotal1()">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Harga</label>
                                    <input readonly type="text" class="form-control text-sm" id="pre_harga"
                                        placeholder="Harga">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Disc(%)</label>
                                    <input type="text" class="form-control" id="pre_disc" placeholder="Discount"
                                        value="0" oninput="hitungdiskon()">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Aturan Pakai</label>
                                    <textarea type="text" class="form-control text-xs" id="pre_dosis" placeholder="Dosis Pakai"></textarea>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Sub Total</label>
                                    <input readonly type="text" class="form-control text-xs" id="pre_sub"
                                        placeholder="Sub total" value="0">
                                    <input hidden readonly type="text" class="form-control text-xs" id="pre_sub_2"
                                        placeholder="Sub total" value="0">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Status</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                            id="status" value="81">
                                        <label class="form-check-label" for="inlineRadio1">KRONIS</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                            id="status" value="83">
                                        <label class="form-check-label" for="inlineRadio2">HIBAH</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                            id="status" value="80">
                                        <label class="form-check-label" for="inlineRadio3">REGULER</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                            id="status" value="82" checked>
                                        <label class="form-check-label" for="inlineRadio3">KEMOTHERAPI</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Action</label><br>
                                    <button class="btn btn-secondary btn-sm" onclick="simpandraft()"><i
                                            class="bi bi-arrow-down mr-1"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">Draft Layanan Resep</div>
                            <div class="card-body">
                                <form action="" method="post" class="form_draf_obat_reguler">
                                    <div class="input_obat">
                                        <div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer bg-warning">
                                <div class="gt_obat_reguler">

                                </div>
                                <button class="btn btn-success float-right" id="btnsimpanorder"
                                    onclick="simpanorderan_far()">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalcari_obatreguler" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cari Obat Requler</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-inline">
                    <div class="form-group mx-sm-5 mb-2">
                        <label for="inputPassword2" class="sr-only">Masukan Nama Obat</label>
                        <input type="text" class="form-control" id="namaobat"
                            placeholder="Masukan nama obat ...">
                    </div>
                    <button type="button" class="btn btn-primary mb-2 cariobat" id="cariobat">Cari Obat</button>
                </form>
                <div class="col-md-12">
                    <div class="v_tabel_obat">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalcari_obatracik" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cari Komponen Obat Racik</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="header_racikan">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Nama Racikan</label>
                        <input type="email" class="form-control" id="komponen_nama_racikan"
                            name="komponen_nama_racikan" aria-describedby="emailHelp">
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Qty Racikan</label>
                                <input type="text" class="form-control" id="qtyracikan" name="qtyracikan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Aturan Pakai</label>
                                <textarea type="text" class="form-control" id="aturanpakairacik" name="aturanpakairacik"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Tipe Racikan</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tiperacikan"
                                        id="tiperacikan" value="1" checked>
                                    <label class="form-check-label" for="inlineRadio1">Powder</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tiperacikan"
                                        id="tiperacikan" value="2">
                                    <label class="form-check-label" for="inlineRadio2">Non - Powder</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Kemasan</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kemasan" id="kemasan"
                                        value="1" checked>
                                    <label class="form-check-label" for="inlineRadio1">Kapsul</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kemasan" id="kemasan"
                                        value="2">
                                    <label class="form-check-label" for="inlineRadio2">Kertas Perkamen</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kemasan" id="kemasan"
                                        value="3">
                                    <label class="form-check-label" for="inlineRadio2">Pot Salep</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Cari Obat</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="namaobatracik"
                                            placeholder="Masukan nama obat ...">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-primary mb-2 cariobatracik"
                                            id="cariobatracik">Cari
                                            Obat</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-md-12">
                    <div class="v_tabel_obat_racik">

                    </div>
                </div>
                <div class="col-md-12">
                    {{-- <div class="row mt-1"> --}}
                    {{-- <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Nama Barang / Layanan</label>
                                <input readonly type="text" class="form-control text-sm"
                                    id="pre_nama_barang_racik" placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_kode_racik" placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_id_ti_racik" placeholder="Nama Barang / Layanan">
                                <input hidden type="text" class="form-control text-xs" id="harga2_racik"
                                    placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_satuan_racik" placeholder="Nama Barang / Layanan">
                            </div>
                        </div> --}}
                    {{-- <div class="col-sm-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Stok</label>
                                <input readonly type="text" class="form-control text-sm" id="pre_stok_racik"
                                    placeholder="stok">
                            </div>
                        </div> --}}
                    {{-- <div class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis</label>
                                <input readonly type="text" class="form-control" id="dosis_obat"
                                    placeholder="dosis_obat">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis Racik</label>
                                <input type="text" class="form-control text-sm" id="dosis_racik"
                                    placeholder="dosis racik ..." value="0">
                            </div>
                        </div> --}}
                    {{-- <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Disc(%)</label>
                                <input type="text" class="form-control" id="pre_disc"
                                    placeholder="Discount_racik" value="0" oninput="hitungdiskon()">
                            </div>
                        </div>
                        <div hidden class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis Pakai</label>
                                <textarea type="text" class="form-control text-xs" id="pre_dosis_racik" placeholder="Dosis Pakai"></textarea>
                            </div>
                        </div>
                        <div hidden class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Sub Total</label>
                                <input readonly type="text" class="form-control text-xs" id="pre_sub_racik"
                                    placeholder="Sub total" value="0">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_sub_2_racik" placeholder="Sub total" value="0">
                            </div>
                        </div>
                        <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Status</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="81">
                                    <label class="form-check-label" for="inlineRadio1">KRONIS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="83">
                                    <label class="form-check-label" for="inlineRadio2">HIBAH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="80" checked>
                                    <label class="form-check-label" for="inlineRadio3">REGULER</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="82">
                                    <label class="form-check-label" for="inlineRadio3">KEMOTHERAPI</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Action</label><br>
                                <button class="btn btn-secondary btn-sm" onclick="simpandraft_racik()"><i
                                        class="bi bi-arrow-down mr-1"></i></button>
                            </div>
                        </div> --}}
                    {{-- </div> --}}
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Nama Barang / Layanan</label>
                                <input readonly type="text" class="form-control text-sm"
                                    id="pre_nama_barang_racik" placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_kode_racik" placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_id_ti_racik" placeholder="Nama Barang / Layanan">
                                <input hidden type="text" class="form-control text-xs" id="harga2_racik"
                                    placeholder="Nama Barang / Layanan">
                                <input hidden readonly type="text" class="form-control text-xs"
                                    id="pre_satuan_racik" placeholder="Nama Barang / Layanan">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Stok</label>
                                <input readonly type="text" class="form-control text-sm" id="pre_stok_racik"
                                    placeholder="stok">
                            </div>
                        </div>
                        <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">QTY</label>
                                <input type="text" class="form-control" id="pre_qty" placeholder="qty"
                                    value="0" oninput="hitungsubtotal1()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Harga</label>
                                <input readonly type="text" class="form-control text-sm" id="pre_harga_racik"
                                    placeholder="Harga">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis</label>
                                <input readonly type="text" class="form-control" id="dosis_obat"
                                    placeholder="dosis_obat">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis Racik</label>
                                <input type="text" class="form-control text-sm" id="dosis_racik"
                                    placeholder="dosis racik ..." value="0">
                            </div>
                        </div>
                        <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Disc(%)</label>
                                <input type="text" class="form-control" id="pre_disc" placeholder="Discount"
                                    value="0" oninput="hitungdiskon()">
                            </div>
                        </div>
                        <div hidden class="col-md-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Dosis Pakai</label>
                                <textarea type="text" class="form-control text-xs" id="pre_dosis" placeholder="Dosis Pakai"></textarea>
                            </div>
                        </div>
                        <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Sub Total</label>
                                <input readonly type="text" class="form-control text-xs" id="pre_sub"
                                    placeholder="Sub total" value="0">
                                <input hidden readonly type="text" class="form-control text-xs" id="pre_sub_2"
                                    placeholder="Sub total" value="0">
                            </div>
                        </div>
                        <div hidden class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Status</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="81">
                                    <label class="form-check-label" for="inlineRadio1">KRONIS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="83">
                                    <label class="form-check-label" for="inlineRadio2">HIBAH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="80" checked>
                                    <label class="form-check-label" for="inlineRadio3">REGULER</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="status" value="82">
                                    <label class="form-check-label" for="inlineRadio3">KEMOTHERAPI</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Action</label><br>
                                <button class="btn btn-secondary btn-sm" onclick="simpandraft_racik()"><i
                                        class="bi bi-arrow-down mr-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark">Komponen Obat Racik</div>
                        <div class="card-body">
                            <form action="" method="post" class="form_draf_obat_racik">
                                <div class="input_obat_racik">
                                    <div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-danger">
                            <form action="" class="formtotal_racikan">
                                <div class="grantotal_racikan">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="simpankomponen_racik()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        kode = $('#kodekunjungan').val()
        get_data_order_poli(kode)
        get_riwayat_obat_tdy(kode)
    })

    function kembali() {
        $('#v_1').removeAttr('Hidden', true)
        $('#v_2').attr('Hidden', true)
    }

    function get_data_order_poli(kode) {
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode
            },
            url: '<?= route('ambil_data_order_poli') ?>',
            success: function(response) {
                $('.v_data_order').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }

    function get_riwayat_obat_tdy(kode) {
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode
            },
            url: '<?= route('riwayat_obat_hari_ini') ?>',
            success: function(response) {
                $('.riwayat_obat_hari_ini').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }
    var input = document.getElementById("namaobat");
    input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("cariobat").click();
        }
    });
    var input = document.getElementById("namaobatracik");
    input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("cariobatracik").click();
        }
    });
    $(".cariobat").on('click', function(event) {
        spinner = $('#loader')
        spinner.show();
        namaobat = $('#namaobat').val()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                namaobat
            },
            url: '<?= route('cari_obat_reguler') ?>',
            success: function(response) {
                $('.v_tabel_obat').html(response);
                spinner.hide()
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    });
    $(".cariobatracik").on('click', function(event) {
        spinner = $('#loader')
        spinner.show();
        namaobat = $('#namaobatracik').val()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                namaobat
            },
            url: '<?= route('cari_obat_racik') ?>',
            success: function(response) {
                $('.v_tabel_obat_racik').html(response);
                spinner.hide()
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    });

    function hitungsubtotal1() {
        diskon = $('#pre_disc').val()
        if (diskon > 0) {
            total = $('#harga2').val() * $('#pre_qty').val()
            diskon = $('#pre_disc').val()
            hitung = diskon / 100 * total
            total2 = total - hitung
            total1 = total2.toLocaleString("IDN", {
                style: "currency",
                currency: "IDR"
            })
            $('#pre_sub').val(total1)
            $('#pre_sub_2').val(total2)
        } else {
            total1 = $('#pre_qty').val() * Math.round($('#harga2').val())
            total = total1.toLocaleString("IDN", {
                style: "currency",
                currency: "IDR"
            })
            total3 = total1
            $('#pre_sub').val(total)
            $('#pre_sub_2').val(total3)
        }
    }

    function hitungdiskon() {
        total = $('#harga2').val() * $('#pre_qty').val()
        diskon = $('#pre_disc').val()
        hitung = diskon / 100 * total
        total2 = total - hitung
        total1 = total2.toLocaleString("IDN", {
            style: "currency",
            currency: "IDR"
        })
        $('#pre_sub').val(total1)
        $('#pre_sub_2').val(total2)
    }

    function simpandraft() {
        var max_fields = 10;
        var wrapper = $(".input_obat");
        var x = 1;
        kode = $('#pre_kode').val()
        namabarang = $('#pre_nama_barang').val()
        harga = $('#pre_harga').val()
        id_stok = $('#pre_id_ti').val()
        harga2 = $('#harga2').val()
        stok_curr = $('#pre_stok').val()
        qty = $('#pre_qty').val()
        disc = $('#pre_disc').val()
        dosis = $('#pre_dosis').val()
        satuan = $('#pre_satuan').val()
        subtot = $('#pre_sub').val()
        subtot2 = $('#pre_sub_2').val()
        status = $('#status:checked').val()
        if (status == 80) {
            so = 'REGULER'
        } else if (status == 81) {
            so = 'KRONIS'
        } else if (status == 82) {
            so = 'KEMOTHERAPI'
        } else {
            so = 'HIBAH'
        }
        if (qty == '0' || kode == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Nama atau Jumlah obat tidak boleh kosong !',
                footer: '<a href="">Why do I have this issue?</a>'
            })
        } else {
            gt_lama = $('#gt_total_layanan_reguler').val()
            gt_total_item = $('#gt_total_item').val()
            gt_total_resep = $('#gt_total_resep').val()
            resep_kronis = $('#resep_kronis').val()
            resep_hibah = $('#resep_hibah').val()
            resep_reguler = $('#resep_reguler').val()
            resep_kemo = $('#resep_kemo').val()
            jasa_baca_reguler = $('#jasa_baca_reguler').val()
            if (x < max_fields) { //max input box allowed
                x++; //text box increment
                $(wrapper).append(
                    '<div class="form-row text-xs"><div class="form-group col-md-2"><label for="">Nama Barang / Tindakan</label><input readonly type="" class="form-control form-control-sm text-xs edit_field" id="" name="nama_barang_order" value="' +
                    namabarang +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="kode_barang_order" value="' +
                    kode +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="id_stok_order" value="' +
                    id_stok +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="harga2_order" value="' +
                    harga2 +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="sub_total_order_2" value="' +
                    subtot2 +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="status_order_2" value="' +
                    status +
                    '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="id_racik" value="0"></div><div class="form-group col-md-1"><label for="inputPassword4">Stok</label><input readonly type="" class="form-control form-control-sm" id="" name="stok_curr_order" value="' +
                    stok_curr +
                    '"></div><div class="form-group col-md-1"><label for="inputPassword4">Qty</label><input readonly type="" class="form-control form-control-sm" id="" name="qty_order" value="' +
                    qty +
                    '"></div><div class="form-group col-md-1"><label for="inputPassword4">Satuan</label><input readonly type="" class="form-control form-control-sm" id="" name="satuan_order" value="' +
                    satuan +
                    '"></div><div class="form-group col-md-1"><label for="inputPassword4">Harga</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="harga_order" value="' +
                    harga +
                    '"></div><div class="form-group col-md-1"><label for="inputPassword4">Diskon</label><input readonly type="" class="form-control form-control-sm" id="" name="disc_order" value="' +
                    disc +
                    '"></div><div class="form-group col-md-2"><label for="inputPassword4">Aturan Pakai</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="dosis_order" value="' +
                    dosis +
                    '"></div><div class="form-group col-md-1"><label for="inputPassword4">Status</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="status_order_1" value="' +
                    so +
                    '"></div><div hidden class="form-group col-md-1"><label for="inputPassword4">Tipe</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="status_order_3" value="NON-RACIKAN"></div><div class="form-group col-md-1"><label for="inputPassword4">Sub Total</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="sub_total_order" value="' +
                    subtot +
                    '"></div><i class="bi bi-x-square remove_field form-group col-md-1 text-danger" kode2="' +
                    kode + '" subtot="' + subtot2 + '" jenis="' + status + '" nama_barang="' + namabarang +
                    '" kode_barang="' + kode + '" id_stok="' + id_stok + '" harga2="' + harga2 + '" satuan="' +
                    satuan + '" stok="' + stok_curr + '" qty="' + qty + '" harga="' + harga + '" disc="' + disc +
                    '" dosis="' + disc + '" sub="' + subtot + '" sub2="' + subtot2 + '" status="' + status +
                    '" jenisracik="non-racikan""></i></div>'
                );
                $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                    qty_item = $(this).attr('qty')
                    harga_item = $(this).attr('harga2')
                    total_layanan_1 = $(this).attr('subtot')
                    jenisracik = $(this).attr('jenisracik')
                    status = $(this).attr('status')
                    $('#' + kode).removeAttr('status', true)
                    e.preventDefault();
                    $(this).parent('div').remove();
                    x--;
                    gt_lama_2 = $('#gt_total_layanan_reguler').val()
                    gt_total_item_2 = $('#gt_total_item').val()
                    gt_total_resep_2 = $('#gt_total_resep').val()
                    resep_kronis_2 = $('#resep_kronis').val()
                    resep_hibah_2 = $('#resep_hibah').val()
                    resep_reguler_2 = $('#resep_reguler').val()
                    resep_kemo_2 = $('#resep_kemo').val()
                    jasa_baca_reguler_2 = $('#jasa_baca_reguler').val()
                    $.ajax({
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            qty_item,
                            harga_item,
                            status,
                            gt_lama_2,
                            gt_total_item_2,
                            gt_total_resep_2,
                            resep_kronis_2,
                            resep_hibah_2,
                            resep_reguler_2,
                            resep_kemo_2,
                            jasa_baca_reguler_2,
                            total_layanan_1,
                            jenisracik
                        },
                        url: '<?= route('minus_grand_total') ?>',
                        success: function(response) {
                            $('.gt_obat_reguler').html(response);
                        }
                    });
                })
            }
            var data1 = $('.form_draf_obat_reguler').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data1: JSON.stringify(data1),
                    harga2,
                    qty,
                    disc,
                    status,
                    gt_lama,
                    gt_total_item,
                    gt_total_resep,
                    resep_kronis,
                    resep_hibah,
                    resep_reguler,
                    resep_kemo,
                    jasa_baca_reguler
                },
                url: '<?= route('jumlah_grand_total_obat_reguler') ?>',
                success: function(response) {
                    $('.gt_obat_reguler').html(response);
                }
            });
        }
    }

    function simpandraft_racik() {
        spinner = $('#loader')
        spinner.show();
        var max_fields = 10;
        var wrapper = $(".input_obat_racik");
        var x = 1;
        tiperacikan = $('#tiperacikan:checked').val()
        kemasan = $('#kemasan:checked').val()
        qtyracikan = $('#qtyracikan').val()
        nama_barang = $('#pre_nama_barang_racik').val()
        kode_barang = $('#pre_kode_racik').val()
        id_stok = $('#pre_id_ti_racik').val()
        harga_obat = $('#harga2_racik').val()
        v_harga_obat = $('#pre_harga_racik').val()
        satuan_obat = $('#pre_satuan_racik').val()
        stok_obat = $('#pre_stok_racik').val()
        dosis_obat = $('#dosis_obat').val()
        dosis_racik = $('#dosis_racik').val()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                dosis_racik,
                qtyracikan,
                dosis_obat,
                harga_obat,
                tiperacikan,
                kemasan
            },
            url: '<?= route('hitunganracikan') ?>',
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
                if (data.kode == 200) {
                    subtotalracik_2 = data.subtotal
                    qtytotal_racik = data.qtytotal
                    v_subtot = data.v_subtot
                    if (x < max_fields) {
                        x++; //text box increment
                        $(wrapper).append(
                            '<div class="form-row text-xs"><div class="form-group col-md-3"><label for="">Nama Barang / Tindakan</label><input readonly type="" class="form-control form-control-sm text-xs edit_field" id="" name="nama_barang_order" value="' +
                            nama_barang +
                            '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="kode_barang_order" value="' +
                            kode_barang +
                            '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="id_stok_order" value="' +
                            id_stok +
                            '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="harga2_order" value="' +
                            subtotalracik_2 +
                            '"></div><div class="form-group col-md-1"><label for="inputPassword4">Stok</label><input readonly type="" class="form-control form-control-sm" id="" name="stok_curr_order" value="' +
                            stok_obat +
                            '"></div><div class="form-group col-md-1"><label for="inputPassword4">Qty</label><input readonly type="" class="form-control form-control-sm" id="" name="qty_order" value="' +
                            qtytotal_racik +
                            '"></div><div class="form-group col-md-1"><label for="inputPassword4">Dosis</label><input readonly type="" class="form-control form-control-sm" id="" name="dosis_order" value="' +
                            dosis_racik +
                            '"></div><div class="form-group col-md-1"><label for="inputPassword4">Satuan</label><input readonly type="" class="form-control form-control-sm" id="" name="satuan_order" value="' +
                            satuan_obat +
                            '"></div><div class="form-group col-md-2"><label for="inputPassword4">Harga</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="harga_order" value="' +
                            harga_obat +
                            '"></div><div class="form-group col-md-2"><label for="inputPassword4">Sub Total</label><input readonly type="" class="form-control form-control-sm text-xs" id="" name="sub_total_order_v" value="' +
                            v_subtot +
                            '"><input hidden readonly type="" class="form-control form-control-sm text-xs" id="" name="sub_total_order" value="' +
                            subtotalracik_2 +
                            '"></div><i class="bi bi-x-square remove_field form-group col-md-1 text-danger" kode2="' +
                            kode_barang + '" gtkomponen="' + subtotalracik_2 + '"  nama_barang="' +
                            nama_barang +
                            '" kode_barang="' + kode_barang + '" id_stok="' + id_stok + '" harga2="' +
                            harga_obat + '" satuan="' +
                            satuan_obat + '" stok="' + stok_obat + '" qty="' + qtytotal_racik +
                            '" harga="' +
                            harga_obat + '"></i></div>'
                        );
                        $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                            totalkomponen = $('#gt_total_komponen').val()
                            gt_total_layanan_racikan = $('#gt_total_layanan_racikan').val()
                            harga = $(this).attr('harga2')
                            e.preventDefault();
                            $(this).parent('div').remove();
                            x--;
                            $.ajax({
                                type: 'post',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    totalkomponen,
                                    gt_total_layanan_racikan,
                                    harga
                                },
                                url: '<?= route('minus_grand_total_racikan') ?>',
                                success: function(response) {
                                    $('.grantotal_racikan').html(response);
                                }
                            });
                        })
                        gt_total_komponen = $('#gt_total_komponen').val()
                        gt_total_layanan_racikan = $('#gt_total_layanan_racikan').val()
                        jasa_resep_racik = $('#jasa_resep_racik').val()
                        jasa_embalase_racik = $('#jasa_embalase_racik').val()
                        var komponenracik = $('.form_draf_obat_racik').serializeArray();
                        $.ajax({
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                harga_obat,
                                dosis_racik,
                                qtytotal_racik,
                                gt_total_komponen,
                                gt_total_layanan_racikan,
                                jasa_resep_racik,
                                jasa_embalase_racik,
                                komponenracik: JSON.stringify(komponenracik),
                                tiperacikan,
                                kemasan,
                                dosis_obat
                            },
                            url: '<?= route('jumlah_grand_total_racikan') ?>',
                            success: function(response) {
                                $('.grantotal_racikan').html(response);
                            }
                        });
                    }
                } else {
                    alert(data.message)
                }
            }
        })
    }

    function simpanorderan_far() {
        var data1 = $('.form_draf_obat_reguler').serializeArray();
        kodekunjungan = $('#kodekunjungan').val()
        rm = $('#rm').val()
        spinner = $('#loader')
        spinner.show();
        $('#btnsimpanorder').attr('disabled',true)
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                data1: JSON.stringify(data1),
                kodekunjungan
            },
            url: '<?= route('simpanorderan_far') ?>',
            error: function(data) {
                spinner.hide()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
                $('#btnsimpanorder').removeAttr('disabled',true)
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
                    $('#btnsimpanorder').removeAttr('disabled',true)
                } else {
                    $('#btnsimpanorder').removeAttr('disabled',true)
                    Swal.fire({
                        title: 'Data Berhasil disimpan !',
                        text: "Cetak nota pembayaran . . .",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Cetak',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire(
                                'Berhasil dicetak !',
                                'Nota berhasil dicetak ...',
                                'success'
                            )
                            window.open('cetaketiket/' + data.idheader);
                            reload_form(rm, kodekunjungan)
                        } else {
                            reload_form(rm, kodekunjungan)
                        }
                    })
                }
            }
        });
    }

    function simpanorderan_far_racik() {
        var data1 = $('.form_draf_komponen_obat_racik').serializeArray();
        kodekunjungan = $('#kodekunjungan').val()
        rm = $('#rm').val()
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                data1: JSON.stringify(data1),
                kodekunjungan
            },
            url: '<?= route('simpanorderan_far_racik') ?>',
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
                        title: 'Data Berhasil disimpan !',
                        text: "Cetak nota pembayaran . . .",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Cetak',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire(
                                'Berhasil dicetak !',
                                'Nota berhasil dicetak ...',
                                'success'
                            )
                            reload_form(rm, kodekunjungan)
                        } else {
                            reload_form(rm, kodekunjungan)
                        }
                    })
                }
            }
        });
    }

    function reload_form(rm, kodekunjungan) {
        $('#v_1').attr('Hidden', true)
        $('#v_2').removeAttr('Hidden', true)
        rm
        kodekunjungan
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                rm,
                kodekunjungan
            },
            url: '<?= route('ambil_form_resep') ?>',
            success: function(response) {
                $('.v_2').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    }

    function simpankomponen_racik() {
        spinner = $('#loader')
        spinner.show();
        $('#modalcari_obatracik').modal('hide')
        var max_fields = 10;
        // var wrapper = $(".input_komponen_obat_racik");
        var wrapper = $(".input_obat");
        var x = 1;
        var header = $('.header_racikan').serializeArray();
        var detail_racik = $('.form_draf_obat_racik').serializeArray();
        var gt = $('#grand_total_layanan_racik_b').val()
        if (x < max_fields) {
            x++; //text box increment
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    header: JSON.stringify(header),
                    detail_racik: JSON.stringify(detail_racik),
                    gt
                },
                url: '<?= route('post_komponen_racik') ?>',
                success: function(response) {
                    // wrapper.after(html);
                    // $('#daftarpxumum').attr('disabled', true);
                    $(wrapper).append(response);
                    $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                        qty_item = $(this).attr('qty')
                        harga_item = $(this).attr('harga2')
                        total_layanan_1 = $(this).attr('subtot')
                        status = $(this).attr('status')
                        jenisracik = $(this).attr('jenisracik')
                        e.preventDefault();
                        $(this).parent('div').remove();
                        x--;
                        gt_lama_2 = $('#gt_total_layanan_reguler').val()
                        gt_total_item_2 = $('#gt_total_item').val()
                        gt_total_resep_2 = $('#gt_total_resep').val()
                        resep_kronis_2 = $('#resep_kronis').val()
                        resep_hibah_2 = $('#resep_hibah').val()
                        resep_reguler_2 = $('#resep_reguler').val()
                        resep_kemo_2 = $('#resep_kemo').val()
                        jasa_baca_reguler_2 = $('#jasa_baca_reguler').val()
                        $.ajax({
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                qty_item,
                                harga_item,
                                status,
                                gt_lama_2,
                                gt_total_item_2,
                                gt_total_resep_2,
                                resep_kronis_2,
                                resep_hibah_2,
                                resep_reguler_2,
                                resep_kemo_2,
                                jasa_baca_reguler_2,
                                total_layanan_1,
                                jenisracik
                            },
                            url: '<?= route('minus_grand_total') ?>',
                            success: function(response) {
                                $('.gt_obat_reguler').html(response);
                            }
                        });
                    })
                    var data1 = $('.form_draf_obat_reguler').serializeArray();
                    $.ajax({
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            data1: JSON.stringify(data1)
                        },
                        url: '<?= route('jumlah_grand_total_obat_reguler') ?>',
                        success: function(response) {
                            $('.gt_obat_reguler').html(response);
                        }
                    });
                }
            });
        }
    }
</script>
