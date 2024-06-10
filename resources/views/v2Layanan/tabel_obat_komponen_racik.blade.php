<table id="tabelkomponenobat" class="table table-sm table-bordered">
    <thead>
        <th>Nama Obat</th>
        <th>Nama Generik</th>
        <th>stok</th>
        <th>Dosis</th>
        <th>Sediaan</th>
        <th>Aturan Pakai</th>
    </thead>
    <tbody>
        @foreach ($obat as $o)
            @if ($o->stok_current > 0)
                <tr class="pilihobatkomponen" sediaan="{{ $o->sediaan }}" kode_barang="{{ $o->kode_barang }}"
                    nama_barang="{{ $o->nama_barang }}" dosis="{{ $o->dosis }}" aturanpakai="{{ $o->aturan_pakai }}"
                    nama_generik={{ $o->nama_generik }}>
                    <td>{{ $o->nama_barang }}</td>
                    <td>{{ $o->nama_generik }}</td>
                    <td>{{ $o->stok_current }}</td>
                    <td>{{ $o->dosis }}</td>
                    <td>{{ $o->sediaan }}</td>
                    <td>{{ $o->aturan_pakai }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelkomponenobat").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            "pageLength": 5,
            "searching": true
        })
    });
    $(".pilihobatkomponen").on('click', function(event) {
        var max_fields = 10; //maximum input boxes allowed
        var wrapper = $(".field_komponen_racik"); //Fields wrapper
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
                '<div class="form-row text-xs"><div class="form-group col-md-3"><label for="">Nama Obat</label><input readonly type="" class="form-control form-control-sm" id="" name="namaobat" value="' +
                nama_obat +
                '"><input hidden readonly type="" class="form-control form-control-sm" id="" name="kodebarang" value="' +
                kode_barang +
                '"></div><div class="form-group col-md-2"><label for="inputPassword4">Nama Generik</label><input readonly type="" class="form-control form-control-sm" id="" name="namagenerik" value="' +
                nama_generik +
                '"></div><div class="form-group col-md-1"><label for="inputPassword4">Sediaan</label><input readonly type="" class="form-control form-control-sm" id="" name="sediaan" value="' +
                sediaan +
                '"></div><div class="form-group col-md-1"><label for="inputPassword4">Dosis</label><input readonly type="" class="form-control form-control-sm" id="" name="dosis" value="' +
                dosis +
                '"></div><div class="form-group col-md-1"><label for="inputPassword4">Dosis Racik</label><input type="" class="form-control form-control-sm" id="" name="dosisracik" value="0"></div><i class="bi bi-x-square remove_field form-group col-md-2 text-danger" kode2=""></i></div>'
            );
            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })
        }
    });
</script>
