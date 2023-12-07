<table id="tabelpasien" class="table table-sm table-hover">
    <thead>
        <th>Tanggal Masuk</th>
        <th>Nomor RM</th>
        <th>Nama Pasien</th>
        <th>Alamat</th>
        <th>Unit Asal</th>
        <th>Penjamin</th>
    </thead>
    <tbody>
        @foreach ($kunjungan as $k )
            <tr class="pilihpasien" kodekunjungan={{ $k->kode_kunjungan }} rm={{ $k->no_rm}}>
                <td>{{ $k->tgl_masuk}}</td>
                <td>{{ $k->no_rm}}</td>
                <td>{{ $k->nama_pasien}}</td>
                <td>{{ $k->alamat}}</td>
                <td>{{ $k->nama_unit}}</td>
                <td>{{ $k->nama_penjamin}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#tabelpasien').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
    $(".pilihpasien").on('click', function(event) {
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
