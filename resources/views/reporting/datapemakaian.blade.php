{{-- <iframe src="{{ url('pdflaporanpemakaian/'.$tglawal.'/'.$tglakhir.'/'.$unit)}}" frameborder="0" width="100%" height="1000px"></iframe> --}}
@foreach ($data_kunjungan as $k)
<div class="card">
    <div class="card-header">{{ $k->no_sep }} | {{ $k->kode_kunjungan }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Resep Order</div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $d )
                                    @if($d->kj == $k->kode_kunjungan)
                                    <tr>
                                        <td>{{ $d->barangorder}}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Resep Yang dilayani</div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $d )
                                    @if($d->kj == $k->kode_kunjungan)
                                    <tr>
                                        <td>{{ $d->barangfar}}</td>
                                        <td>{{ $d->barangfar}}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endforeach
