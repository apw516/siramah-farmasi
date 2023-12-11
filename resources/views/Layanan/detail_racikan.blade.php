<table id="tabel_detail_racikan" class="table table-sm table-bordered">
    <thead>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Qty</th>
        <th>Kemasan</th>
    </thead>
    <tbody>
        @foreach ($detail as $d)
            <tr>
                <td>{{ $d->kode_barang }}</td>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->qty }}</td>
                <td>{{ $d->kemasan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
$(function() {
        $('#tabel_detail_racikan').DataTable({
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
