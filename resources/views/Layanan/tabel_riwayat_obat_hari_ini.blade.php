<table id="riwayat_obat_hari_ini" class="table table-sm table-hover text-sm">
    <thead class="bg-danger">
        <th>Kode Layanan</th>
        <th>Nama Obat</th>
        <th>Aturan Pakai</th>
        <th>Tipe Resep</th>
        <th>Status</th>
        <th>Jumlah</th>
        <th>Total Layanan</th>
        <th>===</th>
    </thead>
    <tbody>
        @foreach ($detail as $d)
            {{-- @if ($h->id == $d->row_id_header) --}}
            <tr>
                <td>{{ $d->kode_layanan_header }}</td>
                <td>{{ $d->nama_barang }} {{ $d->nama_racik }}</td>
                <td>{{ $d->aturan_pakai }}</td>
                <td>{{ $d->nama_anestesi }} @if ($d->tipe_racik == 'S' || $d->tipe_racik == 'NS')
                        / {{ $d->tipe_racik }}
                    @endif
                </td>
                <td>
                    {{ $d->status_layanan_detail }}
                </td>
                <td>{{ $d->jumlah_layanan }}</td>
                <td>IDR {{ number_format($d->grantotal_layanan, 2) }}</td>
                <td>
                    @if ($d->tipe_racik == 'S' || $d->tipe_racik == 'NS')
                        <button class="badge btn-sm btn-info detailobat" iddetail={{ $d->id }} data-toggle="modal"
                            data-target="#modaldetailobat"><i class="bi bi-eye-fill"></i></button>
                    @endif
                    <button class="badge btn-sm btn-warning editobat" namabarang="{{ $d->nama_barang }}"
                        iddetail={{ $d->id }} aturanpakai="{{ $d->aturan_pakai }}" data-toggle="modal"
                        data-target="#modaleditobat"><i class="bi bi-pencil-square"></i></button>
                    <button class="badge btn-sm btn-danger returobat" iddetail={{ $d->id }}
                        namaobat="{{ $d->nama_barang }}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            {{-- @endif --}}
        @endforeach
    </tbody>
</table>
<!-- Modal -->
<div class="modal fade" id="modaldetailobat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Racikan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="v_detail_racikan">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modaleditobat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Aturan Pakai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="v_edit_aturan_pakai">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanedit_aturanpakai()">Simpan</button>
            </div>
        </div>
    </div>
</div>

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
    $(".detailobat").on('click', function(event) {
        id = $(this).attr('iddetail')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('detail_obat_racik') ?>',
            success: function(response) {
                $('.v_detail_racikan').html(response);
            }
        });
    });
    $(".editobat").on('click', function(event) {
        id = $(this).attr('iddetail')
        aturanpakai = $(this).attr('aturanpakai')
        namabarang = $(this).attr('namabarang')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id,
                aturanpakai,
                namabarang
            },
            url: '<?= route('edit_aturan_pakai') ?>',
            success: function(response) {
                $('.v_edit_aturan_pakai').html(response);
            }
        });
    });
    $(".returobat").on('click', function(event) {
        namaobat = $(this).attr('namaobat')
        iddetail = $(this).attr('iddetail')
        Swal.fire({
            title: 'Retur Obat ' + namaobat + ' ?',
            text: "Data obat akan dibatalkan ...",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya , Retur !',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                retur_obat(iddetail)
            } else {

            }
        })
    })

    function retur_obat(id) {
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('retur_obat') ?>',
            error: function(data) {
                spinner.hide()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
            },
            success: function(data) {
                spinner.hide()
                if (data.kode == 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oopss...',
                        text: data.message,
                        footer: ''
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Retur Obat berhasil ...',
                        text: data.message,
                        footer: ''
                    })
                    reload_form(rm, kodekunjungan)
                }
            }
        });
    }

    function simpanedit_aturanpakai() {
        iddetail = $('#iddetail').val()
        aturanpakai = $('#aturanpakai').val()
        kodekunjungan = $('#kodekunjungan').val()
        rm = $('#rm').val()
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                kodekunjungan,
                rm,
                iddetail,
                aturanpakai
            },
            url: '<?= route('simpanedit_aturanpakai') ?>',
            error: function(data) {
                spinner.hide()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
            },
            success: function(data) {
                spinner.hide()
                $('#modaleditobat').modal('hide');
                if (data.kode == 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oopss...',
                        text: data.message,
                        footer: ''
                    })
                } else {
                    reload_form(rm, kodekunjungan)
                }
            }
        });
    }
</script>
