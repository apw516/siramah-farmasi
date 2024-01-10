<table id="tabel_master_barang" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Kelompok Barang</th>
        <th>Sediaan</th>
        <th>Aturan Pakai</th>
    </thead>
    <tbody>
        @foreach ($mt_barang as $b)
            <tr kode_barang={{ $b->kode_barang }} class="pilihbarang" data-toggle="modal" data-target="#modalkartu_stok">
                <td>{{ $b->kode_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td>{{ $b->klp_barang }}</td>
                <td>{{ $b->sediaan }}</td>
                <td>{{ $b->aturan_pakai }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<!-- Modal -->
<div class="modal fade" id="modalkartu_stok" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kartu Stok Obat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="v_stok_obat">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#tabel_master_barang').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
    $(".pilihbarang").on('click', function(event) {
        kode_barang = $(this).attr('kode_barang')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode_barang
            },
            url: '<?= route('ambil_detail_stok') ?>',
            success: function(response) {
                $('.v_stok_obat').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    });
</script>
