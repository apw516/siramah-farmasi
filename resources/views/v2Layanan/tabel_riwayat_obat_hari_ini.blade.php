<button class="btn btn-success mb-2 cetakesemuaetiket" kodekunjungan="{{ $kodekunjungan }}"><i
        class="bi bi-printer mr-1"></i> Cetak Semua Etiket</button>
@foreach ($get_header as $gh)
    <div class="accordion" id="accordionExample{{ $gh->id }}">
        <div class="card">
            <div class="card-header" id="headingOne{{ $gh->id }}" data-toggle="collapse"
                data-target="#collapseOne{{ $gh->id }}" aria-expanded="true"
                aria-controls="collapseOne{{ $gh->id }}">
                <div class="row ">
                    <div class="col-md-3">
                        <p class="text-bold text-dark">{{ $gh->kode_layanan_header }} | {{ $gh->nama_unit }} | Unit kirim : {{ $gh->unit_pengirim }}</p>
                    </div>
                    <div class="col-md-9">
                        <div class="btn-group float-right" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-primary cetaknotaall"
                                kodekunjungan="{{ $kodekunjungan }}" kodeheader="{{ $gh->kode_layanan_header}}" idheader="{{$gh->id}}"><i class="bi bi-printer mr-1"></i>Cetak
                                Nota</button>
                            <button type="button" class="btn btn-success cetaketiketall"
                                kodekunjungan="{{ $kodekunjungan }}" idheader={{ $gh->id }}><i
                                    class="bi bi-printer mr-1"></i>Cetak
                                Etiket</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="collapseOne{{ $gh->id }}" class="collapse" aria-labelledby="headingOne{{ $gh->id }}"
                data-parent="#accordionExample{{ $gh->id }}">
                <div class="card-body">

                    <table
                        id="tabelriwayat_hariini{{ $gh->id }}"class="table table-sm table-bordered table-hover mt-3">
                        <thead>
                            <th>Nama barang</th>
                            <th>Satuan barang</th>
                            <th>Tipe anestesi</th>
                            <th>Jumlah layanan</th>
                            <th>Aturan pakai</th>
                            <th>Keterangan</th>
                            <th width="10%">Action</th>
                        </thead>
                        <tbody>
                            @foreach ($list as $l)
                                @if ($l->kode_layanan_header == $gh->kode_layanan_header)
                                    @if (strlen($l->nama_barang) > 1 || strlen($l->nama_racik) > 1)
                                        <tr>
                                            <td>{{ $l->nama_barang }} {{ $l->nama_racik }}</td>
                                            <td>{{ $l->satuan_barang }} {{ $l->kemasan }}</td>
                                            <td>
                                                @if ($l->tipe_anestesi == 80)
                                                    Reguler
                                                @elseif($l->tipe_anestesi == 81)
                                                    Kronis
                                                @endif
                                            </td>
                                            <td>{{ $l->jumlah_layanan }}</td>
                                            <td>{{ $l->aturan_pakai }}</td>
                                            <td>{{ $l->keterangan01 }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info" data-toggle="tooltip"
                                                    data-placement="top" title="Cetak etiket"><i
                                                        class="bi bi-printer"></i></button>
                                                <button class="btn btn-sm btn-danger" data-toggle="tooltip"
                                                    data-placement="top" title="Retur obat"><i
                                                        class="bi bi-x-circle"></i></button>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
<script>
    $(".cetakesemuaetiket").on('click', function(event) {
        kodekunjungan = $(this).attr('kodekunjungan')
        window.open('cetaketiket_2_all/' + kodekunjungan);
    })
    $(".cetaketiketall").on('click', function(event) {
        kodekunjungan = $(this).attr('kodekunjungan')
        idheader = $(this).attr('idheader')
        window.open('cetaketiket_2/' + idheader);
    })
    $(".cetaknotaall").on('click', function(event) {
        kodekunjungan = $(this).attr('kodekunjungan')
        kodeheader = $(this).attr('kodeheader')
        idheader = $(this).attr('idheader')
        window.open('cetaknotafarmasi_2/' + kodekunjungan +'/'+ kodeheader+'/'+idheader);
    })
</script>
