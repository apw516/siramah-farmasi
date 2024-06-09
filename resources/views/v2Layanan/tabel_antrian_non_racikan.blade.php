<table id="tabel_antrian_non_racikan" class="table table-sm table-bordered table-hover  text-sm">
    <thead>
        <th>Nomor Antrian</th>
        <th>Nomor RM</th>
        <th>Nama Pasien</th>
        <th>Unit Pengirim</th>
        {{-- <th>Dokter Pengirim</th> --}}
        <th>Status</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach ($list as $l )
        @if($l->jenis_antrian == 'REGULER')
                <tr class="@if($l->status_order == 1) bg-light @endif">
                    <td>{{ $l->nomor_antrian}}</td>
                    <td>{{ $l->rm}}</td>
                    <td>{{ $l->nama_pasien}}</td>
                    <td>{{ $l->nama_unit}}</td>
                    {{-- <td>{{ $l->nama_dokter}}</td> --}}
                    <td>@if($l->status_order == '1')Sudah dilayani @else Dalam antrian @endif</td>
                    <td class="text-center"><button class="btn btn-sm btn-success pilihantrian" idantrian="{{ $l->id }}" kodekunjungan="{{ $l->kode_kunjungan }}"><i class="bi bi-prescription2"></i></button></td>
                </tr>
                @endif
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $('#tabel_antrian_non_racikan').DataTable({
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
