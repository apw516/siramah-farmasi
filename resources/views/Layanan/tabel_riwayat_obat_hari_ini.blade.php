<table id="riwayat_obat_hari_ini" class="table table-sm table-hover text-sm">
    <thead class="bg-danger">
        <th>Nama Obat</th>
        <th>Aturan Pakai</th>
        <th>Tipe Resep</th>
        <th>Jumlah</th>
        <th>Total Layanan</th>
    </thead>
    <tbody>
        @foreach ($detail as $d)
            {{-- @if ($h->id == $d->row_id_header) --}}
            <tr>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->aturan_pakai }}</td>
                <td>{{ $d->nama_anestesi }}</td>
                <td>{{ $d->jumlah_layanan }}</td>
                <td>IDR {{ number_format($d->grantotal_layanan, 2) }}</td>
            </tr>
            {{-- @endif --}}
        @endforeach
    </tbody>
</table>
<script>
   $(function() {
        $('#riwayat_obat_hari_ini').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 5
        });
    });
</script>
