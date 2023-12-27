<div class="row">
    <div class="col-md-3">
        <div class="card card-dark card-outline text-xs text-dark">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $nama }}</h3>
                <p class="text-muted text-center">{{ $unit }}</p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>RM</b> <a class="float-right text-dark">{{ $rm }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Alamat</b> <a class="float-right text-dark">{{ $alamat }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Dokter</b> <a class="float-right text-dark">{{ $dokter }}</a>
                    </li>
                </ul>
                <input hidden type="text" value="{{ $id }}" name="idheader" id="idheader">
                <a href="#" class="btn btn-dark btn-block" onclick="cetaketiket()"><b> <i
                            class="bi bi-printer-fill"></i> etiket</b></a>
                <a href="#" class="btn btn-primary btn-block" onclick="cetaknota()"><b> <i
                            class="bi bi-printer-fill"></i> Nota</b></a>

            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-dark">Data Obat</div>
            <div class="card-body">
                <table id="tabeldetailresep" class="table table-sm table-bordered text-xs table-hover">
                    <thead>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
                        <th>Aturan Pakai</th>
                        <th>Jenis Resep</th>
                    </thead>
                    <tbody>
                        @foreach ($detailresep as $d)
                            <tr>
                                <td>{{ $d->nama_barang }}</td>
                                <td>{{ $d->jumlah_layanan }} {{ $d->satuan_barang }}</td>
                                <td>{{ $d->aturan_pakai }}</td>
                                <td>{{ $d->kategori_resep }} | {{ $d->nama_anestesi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#tabeldetailresep').DataTable({
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

    function cetaketiket() {
        id = $('#idheader').val()
        window.open('cetaketiket/' + id);
    }

    function cetaknota() {
        id = $('#idheader').val()
        window.open('cetaknota/' + id);

    }
