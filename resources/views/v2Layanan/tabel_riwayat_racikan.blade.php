@foreach ($header as $h)
    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header bg-light" id="headingOne{{ $h->id }}">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left text-bold text-dark" type="button"
                        data-toggle="collapse" data-target="#collapseOne{{ $h->id }}" aria-expanded="true"
                        aria-controls="collapseOne{{ $h->id }}">
                        {{ $h->nama_racikan }} | Jumlah racikan : {{ $h->jumlah_racikan }} <p class="float-right">
                            {{ $h->tgl_entry }}</p>
                    </button>
                </h2>
            </div>

            <div id="collapseOne{{ $h->id }}" class="collapse" aria-labelledby="headingOne{{ $h->id }}"
                data-parent="#accordionExample">
                <div class="card-body">
                    <button class="btn btn-warning mb-3 pilihracikan" idheader="{{ $h->id }}"><i
                            class="bi bi-plus mr-1"></i> Pilih</button>
                    <table class="table table-sm table-bordered">
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Dosis Awal</th>
                        <th>Dosis Racik</th>
                        <tbody>
                            @foreach ($detail as $d)
                                @if ($d->id_header == $h->id)
                                    <tr>
                                        <td>{{ $d->nama_barang }}</td>
                                        <td>{{ $d->qty }}</td>
                                        <td>{{ $d->dosis_awal }}</td>
                                        <td>{{ $d->dosis_racik }}</td>
                                    </tr>
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
    $(".pilihracikan").on('click', function(event) {
        id = $(this).attr('idheader')
        spinner = $('#loader')
        spinner.show();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('v2_add_riwayat_racik') ?>',
            error: function(data) {
                spinner.hide()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
            },
            success: function(response, data) {
                spinner.hide()
                var wrapper = $(".field_input_obat");
                $('#riwayatracikan').modal('hide');
                $(wrapper).append(response);
                $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                    e.preventDefault();
                    $(this).parent('div').remove();
                    x--;
                })
            }
        });
    })
</script>
