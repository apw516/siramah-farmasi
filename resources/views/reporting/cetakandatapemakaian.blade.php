@foreach ($data_kunjungan as $k )
    <div class="card">
        <div class="card-header">{{ $k->no_sep }} | {{ $k->kode_kunjungan }}</div>
    </div>
@endforeach
