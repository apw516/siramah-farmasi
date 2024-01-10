<table id="tabelstokbarang" class="table table-sm table-bordered table-hover">
    <thead>
        <th>No</th>
        <th>Tanggal Stok</th>
        <th>Nama Barang</th>
        <th>Nama Unit</th>
        <th>Keterangan</th>
        <th>Stok Last</th>
        <th>Stok In</th>
        <th>Stok Out</th>
        <th>Stok Current</th>
    </thead>
    <tbody>
        @foreach ($detail as $d )
            <tr>
                <td>{{ $d->no}}</td>
                <td>{{ $d->tgl_stok}}</td>
                <td>{{ $d->nama_barang}}</td>
                <td>{{ $d->nama_unit}}</td>
                <td>{{ $d->keterangan}}</td>
                <td>{{ $d->stok_last}}</td>
                <td>{{ $d->stok_in}}</td>
                <td>{{ $d->stok_out}}</td>
                <td>{{ $d->stok_current}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
   $(function() {
        $('#tabelstokbarang').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
