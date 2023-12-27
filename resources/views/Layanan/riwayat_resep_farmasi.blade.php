    <table id="tabel_Rr" class="table table-sm table-bordered text-sm table-hover">
        <thead>
            <th>Tanggal Entry</th>
            <th>No RM</th>
            <th>Nama Pasien</th>
            <th>Alamat</th>
            <th>Unit Pengirim</th>
            <th>Dokter Pengirim</th>
        </thead>
        <tbody>
            @foreach ($riwayat as $r)
                <tr idheader="{{ $r->id_header }}" rm="{{ $r->no_rm }}" nama="{{ $r->nama_pasien }}"
                    alamat="{{ $r->alamat }}" dokter="{{ $r->dokter_pengirim }}" unitpengirim="{{ $r->unit_pengirim }}"
                    class="detailresepnya" data-toggle="modal" data-target="#modaldetailresep">
                    <td>{{ $r->tgl_entry }}</td>
                    <td>{{ $r->no_rm }}</td>
                    <td>{{ $r->nama_pasien }}</td>
                    <td>{{ $r->alamat }}</td>
                    <td>{{ $r->unit_pengirim }}</td>
                    <td>{{ $r->dokter_pengirim }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Modal -->
    <div class="modal fade" id="modaldetailresep" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Resep</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="v_d_r">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#tabel_Rr').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 6
            });
        });
        $(".detailresepnya").on('click', function(event) {
            id = $(this).attr('idheader')
            rm = $(this).attr('rm')
            nama = $(this).attr('nama')
            alamat = $(this).attr('alamat')
            dokter = $(this).attr('dokter')
            unit = $(this).attr('unitpengirim')
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id,
                    rm,
                    nama,
                    alamat,
                    dokter,
                    unit
                },
                url: '<?= route('detail_resep_obat') ?>',
                success: function(response) {
                    $('.v_d_r').html(response);
                }
            });
        });
