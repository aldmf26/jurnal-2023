<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        {{-- @include('pembelian_bk.nav') --}}
        <style>
            @media only screen and (max-width: 767px) {

                .label_hilang {
                    display: none
                }
            }
        </style>

        @csrf
        {{-- @if (!empty($approve))
                <button class="float-end btn btn-primary btn-sm"><i class="fas fa-check"></i> Approve</button>
                <br>
                <br>
            @endif --}}
        <section class="row">
            <form action="{{ route('congan.edit_congan') }}" method="post">
                @csrf
                @foreach ($congan as $no => $c)
                    <input type="hidden" value="{{ $c->no_nota }}" name="no_nota">
                    <div class="row">
                        <div class="col-lg-12">
                            <hr style="border: 1px solid black">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control" name="tgl[]" value="{{ $c->tgl }}">
                            <input type="hidden" class="form-control" name="id_invoice_congan[]"
                                value="{{ $c->id_invoice_congan }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label for="">Pemilik</label>
                            <input type="text" class="form-control" name="pemilik[]" value="{{ $c->pemilik }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label for="">Keterangan</label>
                            <input type="text" class="form-control" name="ket[]" value="{{ $c->ket }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label for="">Persen Air</label>
                            <input type="text" class="form-control persen_air{{ $no }}" name="persen_air[]"
                                value="{{ $c->persen_air }}">
                        </div>
                    </div>
                    <div class="row mb-4 mt-4">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                        <div class="col-lg-4 label_hilang">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                        <div class="col-lg-4 label_hilang">
                            <div class="row">
                                <div class="col-lg-6 col-4"><label for="">Grade</label></div>
                                <div class="col-lg-3 col-4"><label for="">Gr</label></div>
                                <div class="col-lg-3 col-4"><label for="">Harga</label></div>
                            </div>
                        </div>
                        @php
                            $total_rp = 0;
                            $gr = 0;
                        @endphp
                        @foreach ($grade as $key => $g)
                            @php
                                $letter = chr(97 + $key);
                                $persen = DB::selectOne(
                                    "SELECT a.gr, a.hrga  FROM tb_cong as a where a.no_nota = '$c->no_nota' and a.id_grade = '$g->id_grade_cong' and a.ket = '$c->ket'",
                                );
                            @endphp
                            <div class="col-lg-4 mt-2" style="border-right: 1px solid black">
                                <div class="row">
                                    <div class="col-lg-6 col-4">
                                        <input type="hidden" name="id_grade{{ $no }}[]"
                                            value="{{ $g->id_grade_cong }}">
                                        {{-- <input type="text" class="form-control" value="{{ $g->nm_grade }}"
                                            readonly> --}}
                                        <span>{{ $g->nm_grade }}</span>
                                    </div>
                                    <div class="col-lg-3 col-4">

                                        <input type="text"
                                            class="form-control gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                            count="{{ $no }}" name="gr{{ $no }}[]"
                                            hruf="{{ $letter }}"
                                            value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                    </div>
                                    <div class="col-lg-3 col-4">
                                        <input type="text"
                                            class="form-control harga harga{{ $no }}{{ $letter }}"
                                            count="{{ $no }}" hruf="{{ $letter }}"
                                            value="{{ empty($persen->hrga) ? '0' : $persen->hrga }}"
                                            name="harga{{ $no }}[]">

                                        @php
                                            $gram = empty($persen->gr) ? '0' : $persen->gr;
                                            $hgra = empty($persen->hrga) ? '0' : $persen->hrga;
                                        @endphp
                                        <input type="hidden"
                                            class="ttl_hrga{{ $no }} ttl_hrga{{ $no }}{{ $letter }}"
                                            value="{{ $gram * $hgra }}">
                                    </div>
                                </div>
                            </div>
                            @php
                                $gr += empty($persen->gr) ? '0' : $persen->gr;
                                $total_rp +=
                                    (empty($persen->gr) ? '0' : $persen->gr) *
                                    (empty($persen->hrga) ? '0' : $persen->hrga);
                            @endphp
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <table style="padding: 10px">
                                <tr>
                                    <td>
                                        <h6>Total Gram &nbsp; {{ $gr }}</h6>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total_gram{{ $no }}"
                                            readonly value="{{ $c->gr }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Harga Beli &nbsp;</h6>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hrga_beli{{ $no }}"
                                            name="hrga_beli[]" value="{{ $c->hrga_beli }}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Harga({{ 100 - $c->persen_air }}%) &nbsp;</h6>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hrga_seratus{{ $no }}"
                                            readonly
                                            value="Rp. {{ number_format(($total_rp / $gr) * ((100 - $c->persen_air) / 100), 0) }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Harga(%) &nbsp;</h6>
                                    </td>
                                    <td><input type="text" class="form-control hrga_persen{{ $no }}"
                                            value="Rp. {{ number_format($total_rp / $gr, 0) }}" readonly></td>
                                    <input type="hidden" name="count[]" value="{{ $no }}">
                                </tr>
                            </table>

                        </div>
                    </div>
                @endforeach
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
                        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
                            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <a href="{{ route('congan.index') }}"
                            class="float-end btn btn-outline-primary me-2">Batal</a>
                    </div>
                </div>
            </form>
        </section>











    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {

                $("form").on("keypress", function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        return false;
                    }
                });

                aksiBtn("form");

                $('.harga').keyup(function() {
                    var count = $(this).attr('count');
                    var letter = $(this).attr('hruf');

                    var gr = $('.gr' + count + letter).val();
                    var harga = $('.harga' + count + letter).val();

                    var ttl = parseFloat(gr) * parseFloat(harga);

                    var ttl_hrga = $('.ttl_hrga' + count + letter).val(ttl);

                    var total_harga = $('.ttl_hrga' + count).toArray().reduce(function(acc, input) {
                        var value = parseFloat($(input).val()) || 0;
                        return acc + value;
                    }, 0);


                    var total = $('.gr' + count).toArray().reduce(function(acc, input) {
                        var value = parseFloat($(input).val()) || 0;
                        return acc + value;
                    }, 0);

                    var hrga_persen = parseFloat(total_harga) / parseFloat(total)
                    var persen_air = $('.persen_air' + count).val();

                    var totalRupiah = hrga_persen.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    $('.hrga_persen' + count).val(totalRupiah);

                    var hrga_kurang = parseFloat(hrga_persen) * ((100 - parseFloat(persen_air)) / 100);

                    var totalRupiah2 = hrga_kurang.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    $('.hrga_seratus' + count).val(totalRupiah2);
                });
            });
        </script>
    @endsection
</x-theme.app>
