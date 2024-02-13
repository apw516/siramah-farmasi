<table id="tabel_obat_reguler" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Nama Barang /Layanan</th>
        <th>Tipe</th>
        <th>Stok Current</th>
        <th>Satuan</th>
        <th>Harga Jual</th>
    </thead>
    <tbody>
        @foreach ($pencarian_obat as $p)
            <tr class="pilihobat" satuan="{{ $p->satuan }}" kode="{{ $p->kode_barang }}" namaobat="{{ $p->nama_barang }}"
                tarif2="{{ $p->harga_jual }}" tarif="IDR {{ number_format($p->harga_jual, 2) }}"
                stok_current="{{ $p->stok_current }}" no="{{ $p->NO }}" aturan="{{ $p->aturan_pakai }}">
                <td>{{ $p->nama_barang }}</td>
                <td>{{ $p->nama_tipe }}</td>
                <td>{{ $p->stok_current }}</td>
                <td>{{ $p->satuan }}</td>
                <td>IDR {{ number_format($p->harga_jual, 2) }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabel_obat_reguler").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": true,
            "pageLength": 10,
            "searching": true
        })
    });
    $('#tabel_obat_reguler').on('click', '.pilihobat', function() {
        $('#modalcari_obatreguler').modal('hide')
        var target = document.getElementById('pre_stok');
        target.focus();
        namaobat = $(this).attr('namaobat')
        tarif = $(this).attr('tarif')
        stok_current = $(this).attr('stok_current')
        aturan = $(this).attr('aturan')
        no = $(this).attr('no')
        tarif2 = $(this).attr('tarif2')
        kode = $(this).attr('kode')
        satuan = $(this).attr('satuan')
        $('#pre_kode').val(kode)
        $('#pre_id_ti').val(no)
        $('#pre_nama_barang').val(namaobat)
        $('#pre_harga').val(tarif)
        $('#harga2').val(tarif2)
        $('#pre_stok').val(stok_current)
        $('#pre_dosis').val(aturan)
        $('#pre_satuan').val(satuan)
        $('#pre_sub').val(0)
        $('#pre_sub_2').val(0)
        $('#modalcariobat').modal('hide')
        $("#pre_qty").val(0);
        $("#pre_qty").focus();
    });
</script>
