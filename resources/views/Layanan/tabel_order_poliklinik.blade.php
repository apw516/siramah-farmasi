<table id="tabelorder" class="table table-sm table-hover text-xs">
    <thead>
        <th>Tgl Order</th>
        <th>Nomor RM</th>
        <th>Nama Pasien</th>
        <th>Alamat</th>
        <th>Unit Pengirim</th>
        <th>Dokter Pengirim</th>
        <th>Status</th>
        <th>===</th>
    </thead>
    <tbody>
        @foreach ($cari_order as $c)
            <tr>
                <td>{{ $c->tgl_entry }}</td>
                <td>{{ $c->no_rm }}</td>
                <td>{{ $c->nama_pasien }}</td>
                <td>{{ $c->alamat }}</td>
                <td>{{ $c->nama_unit_pengirim }}</td>
                <td>{{ $c->nama_dokter }}</td>
                <td>{{ $c->status_order }}</td>
                <td><button class="badge badge-success pilihorderan" rm="{{ $c->no_rm}}" kodekunjungan = {{ $c->kode_kunjungan }}><i class="bi bi-heart-pulse"></i></button></td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#tabelorder').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
    $(".pilihorderan").on('click', function(event) {
        $('#v_1').attr('Hidden', true)
        $('#v_2').removeAttr('Hidden', true)
        rm = $(this).attr('rm')
        kodekunjungan = $(this).attr('kodekunjungan')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                rm,
                kodekunjungan
            },
            url: '<?= route('ambil_form_resep') ?>',
            success: function(response) {
                $('.v_2').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    });
</script>
