<table id="tabel_obat_racik" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Nama Barang /Layanan</th>
        <th>Tipe</th>
        <th>Stok Current</th>
        <th>Satuan</th>
        <th>Dosis</th>
        <th>Harga Jual</th>
    </thead>
    <tbody>
        @foreach ($pencarian_obat as $p)
            <tr class="pilihobat" dosis="{{ $p->dosis }}" satuan="{{ $p->satuan }}" kode="{{ $p->kode_barang }}"
                namaobat="{{ $p->nama_barang }}" tarif2="{{ $p->harga_jual }}"
                tarif="IDR {{ number_format($p->harga_jual, 2) }}" stok_current="{{ $p->stok_current }}"
                no="{{ $p->NO }}" aturan="{{ $p->aturan_pakai }}">
                <td>{{ $p->nama_barang }}</td>
                <td>{{ $p->nama_tipe }}</td>
                <td>{{ $p->stok_current }}</td>
                <td>{{ $p->satuan }}</td>
                <td>{{ $p->dosis }}</td>
                <td>IDR {{ number_format($p->harga_jual, 2) }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabel_obat_racik").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": true,
            "pageLength": 3,
            "searching": true
        })
    });
    $('#tabel_obat_racik').on('click', '.pilihobat', function() {
        namaobat = $(this).attr('namaobat')
        tarif = $(this).attr('tarif')
        stok_current = $(this).attr('stok_current')
        aturan = $(this).attr('aturan')
        no = $(this).attr('no')
        tarif2 = $(this).attr('tarif2')
        kode = $(this).attr('kode')
        satuan = $(this).attr('satuan')
        dosis = $(this).attr('dosis')
        $('#pre_kode_racik').val(kode)
        $('#pre_id_ti_racik').val(no)
        $('#pre_nama_barang_racik').val(namaobat)
        $('#pre_harga_racik').val(tarif)
        $('#harga2_racik').val(tarif2)
        $('#pre_stok_racik').val(stok_current)
        $('#pre_dosis_racik').val(aturan)
        $('#pre_satuan_racik').val(satuan)
        $('#dosis_obat').val(dosis)
        $('#pre_sub_racik').val(0)
        $('#pre_sub_2_racik').val(0)
        $('#modalcariobat').modal('hide')
    });
</script>
