<table id="tabelpasiien" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Tanggal masuk</th>
        <th>No RM</th>
        <th>Nama Pasien</th>
        <th>Alamat</th>
        <th>Unit Asal</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach ($list as $l)
            <tr>
                <td>{{ $l->tgl_masuk }}</td>
                <td>{{ $l->no_rm }}</td>
                <td>{{ $l->nama_pasien }}</td>
                <td>{{ $l->alamat }}</td>
                <td>{{ $l->nama_unit }}</td>
                <td><button class="btn btn-sm btn-success pilihantrian" kodekunjungan="{{ $l->kode_kunjungan}}" idantrian="0">Pilih</button></td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#tabelpasiien').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
    $(".pilihantrian").on('click', function(event) {
        $('.v_satu').attr('Hidden', true)
        $('.v_dua').removeAttr('Hidden', true)
        kodekunjungan = $(this).attr('kodekunjungan')
        idantrian = $(this).attr('idantrian')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kodekunjungan,idantrian
            },
            url: '<?= route('ambil_detail_orderan') ?>',
            success: function(response) {
                $('.v_dua').html(response);
                // $('#daftarpxumum').attr('disabled', true);
            }
        });
    });
