<table id="tabelobatreguler" class="table table-sm text-xs table-bordered table-hover">
    <thead>
        <th>Nama Obat</th>
        <th>Nama Generik</th>
        <th>Dosis</th>
        <th>Sediaan</th>
        <th>Aturan Pakai</th>
    </thead>
    <tbody>
        @foreach ($pencarian_obat as $o)
            <tr class="pilihobatreguler" sediaan="{{ $o->sediaan}}" kode_barang="{{ $o->kode_barang }}" nama_barang="{{ $o->nama_barang }}"
                dosis="{{ $o->dosis }}" aturanpakai="{{ $o->aturan_pakai }}" nama_generik={{ $o->nama_generik}}>
                <td>{{ $o->nama_barang }}</td>
                <td>{{ $o->nama_generik }}</td>
                <td>{{ $o->dosis }}</td>
                <td>{{ $o->sediaan }}</td>
                <td>{{ $o->aturan_pakai }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelobatreguler").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            "pageLength": 5,
            "searching": true
        })
    });
    $(".pilihobatreguler").on('click', function(event) {
        var max_fields = 10; //maximum input boxes allowed
        var wrapper = $(".field_input_obat"); //Fields wrapper
        var x = 1; //initlal text box count
        // e.preventDefault();
        nama_obat = $(this).attr('nama_barang')
        nama_generik = $(this).attr('nama_generik')
        dosis = $(this).attr('dosis')
        sediaan = $(this).attr('sediaan')
        kode_barang = $(this).attr('kode_barang')
        if (x < max_fields) { //max input box allowed
            x++; //text box increment
            $(wrapper).append(
                '<div class="form-row text-xs"><div class="form-group col-md-2"><label for="">Nama Obat</label><input readonly type="" class="form-control form-control-sm" id="" name="nama_obat" value="'+nama_obat+'"><input hidden readonly type="" class="form-control form-control-sm" id="" name="kodebarang" value="'+kode_barang+'"><input  hidden readonly type="" class="form-control form-control-sm" id="" name="idheader" value="0"><input hidden readonly type="" class="form-control form-control-sm" id="" name="iddetail" value="0"></div><div class="form-group col-md-1"><label for="inputPassword4">Jenis Resep</label><input readonly type="" class="form-control form-control-sm" id="" name="jenisresep" value="reguler"></div><div class="form-group col-md-1"><label for="inputPassword4">Dosis</label><input readonly type="" class="form-control form-control-sm" id="" name="dosis" value="'+dosis+'"></div><div class="form-group col-md-1"><label for="inputPassword4">Sediaan</label><input readonly type="" class="form-control form-control-sm" id="" name="sediaan" value="'+sediaan+'"></div><div class="form-group col-md-1"><label for="inputPassword4">Tipe anestesi</label> <select class="form-control form-control-sm" id="kronis" name="kronis"><option value="80">Reguler</option><option value="81">Kronis</option><option value="82">Kemoterapi</option><option value="83">Hibah</option></select></div><div class="form-group col-md-1"><label for="inputPassword4">Jumlah</label><input type="" class="form-control form-control-sm" id="" name="jumlah" value="0"></div><div class="form-group col-md-2"><label for="inputPassword4">Aturan pakai</label><textarea type="" class="form-control form-control-sm" id="" name="aturanpakai" value="" placeholder="Ketik aturan pakai ..."></textarea></div><div class="form-group col-md-2"><label for="inputPassword4">Keterangan</label><textarea type="" class="form-control form-control-sm" id="" name="keterangan" placeholder="Ketik keterangan obat jika ada ..."></textarea></div><i class="bi bi-x-square remove_field form-group col-md-1 text-danger" kode2=""></i></div>'
            );
            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })
        }
    });
</script>
