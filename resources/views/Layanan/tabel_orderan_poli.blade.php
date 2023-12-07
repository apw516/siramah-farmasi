<table id="tableorderan" class="table table-bordered text-sm table-striped">
    <thead class="bg-dark">
        <th>Nama Obat</th>
        <th>Jumlah</th>
        <th>Aturan Pakai</th>
        <th>Dokter Pengirim</th>
        <th>Unit Pengirim</th>
    </thead>
    <tbody>
        @foreach ($orderan as $o)
            <tr>
                <td>{{ $o->kode_barang }}</td>
                <td>{{ $o->jumlah_layanan }}</td>
                <td>{{ $o->aturan_pakai }}</td>
                <td>{{ $o->nama_dokter }}</td>
                <td>{{ $o->nama_unit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#tableorderan').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 3
        });
    });
</script>
