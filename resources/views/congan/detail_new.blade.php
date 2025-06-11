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
        <style>
            @media (max-width: 767.98px) {
                .inputan {
                    width: 90px !important;
                }

                .inputan2 {
                    width: 170px !important;
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
            @php
                $posisi_id = Auth::user()->posisi_id;
            @endphp
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
                        <div class="col-lg-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead">Kategori</th>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead text-end" width="12%">Putih Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">Putih Rp/gr</th>
                                        @else
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                        <th class="dhead text-end" width="12%">Kuning Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">Kuning Rp/gr</th>
                                        @else
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_rp = 0;
                                        $gr = 0;
                                        $prevKategori = null;
                                    @endphp
                                    @foreach ($grade as $key => $g)
                                        @php
                                            $persen = DB::selectOne(
                                                "SELECT a.gr, a.hrga, a.gr_kuning, a.hrga_kuning  FROM tb_cong as a where a.no_nota = '$c->no_nota' and a.id_grade = '$g->id_grade_cong' and a.ket = '$c->ket'",
                                            );
                                            $letter = chr(97 + $key);

                                            $hrga_dlu = DB::table('tb_cong')
                                                ->where('id_grade', $g->id_grade_cong)
                                                ->where('no_nota', '!=', $no_nota)
                                                ->where('hrga', '!=', 0)
                                                ->orderBy('no_nota', 'desc')
                                                ->first();
                                            $hrga_dlu_kuning = DB::table('tb_cong')
                                                ->where('id_grade', $g->id_grade_cong)
                                                ->where('no_nota', '!=', $no_nota)
                                                ->where('hrga_kuning', '!=', 0)
                                                ->orderBy('no_nota', 'desc')
                                                ->first();

                                        @endphp
                                        <tr>
                                            <td>
                                                @if ($g->nm_kategori !== $prevKategori)
                                                    {{ $g->nm_kategori }}
                                                    @php $prevKategori = $g->nm_kategori; @endphp
                                                @endif
                                            </td>
                                            <input type="hidden" name="id_grade{{ $no }}[]"
                                                value="{{ $g->id_grade_cong }}">
                                            <td>
                                                @if ($posisi_id == 1)
                                                    <input type="text"
                                                        class="form-control  inputan2 nm_grade nm_grade{{ $g->id_grade_cong }}"
                                                        id_grade="{{ $g->id_grade_cong }}"
                                                        name="nm_grade{{ $no }}[]"
                                                        value="{{ $g->nm_grade }}">
                                                @else
                                                    {{ $g->nm_grade }}
                                                @endif
                                            </td>
                                            <td class="text-end">

                                                <input type="text"
                                                    class="form-control inputan text-end gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" name="gr{{ $no }}[]"
                                                    hruf="{{ $letter }}"
                                                    value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                            </td>

                                            @if ($posisi_id == 1)
                                                <td class="text-end">

                                                    <input type="text"
                                                        class="form-control text-end inputan harga harga{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->hrga) ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}"
                                                        name="harga{{ $no }}[]">
                                                    @php
                                                        $gram = empty($persen->gr) ? '0' : $persen->gr;
                                                        $hgra = empty($persen->hrga) ? '0' : $persen->hrga;
                                                    @endphp
                                                    <input type="hidden"
                                                        class="ttl_hrga{{ $no }} ttl_hrga{{ $no }}{{ $letter }}"
                                                        value="{{ $gram * $hgra }}">
                                                </td>
                                            @else
                                            @endif


                                            {{-- <td class="text-end tl_harga{{ $no }}{{ $letter }}">
                                                Rp
                                                {{ number_format(($persen->gr ?? 0) * ($persen->hrga ?? 0), 0, ',', '.') }}
                                            </td> --}}
                                            <td class="text-end">
                                                {{ empty($persen->gr) ? 0 : number_format(($persen->gr / $c->gr) * 100, 2) }}
                                            </td>
                                            <td class="text-end">

                                                <input type="text"
                                                    class="form-control inputan text-end gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" name="gr_kuning{{ $no }}[]"
                                                    hruf="{{ $letter }}"
                                                    value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                            </td>
                                            @if ($posisi_id == 1)
                                                <td class="text-end">

                                                    <input type="text"
                                                        class="form-control text-end inputan harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->hrga_kuning) ? $hrga_dlu->hrga_kuning ?? 0 : $persen->hrga_kuning }}"
                                                        name="harga_kuning{{ $no }}[]">
                                                    @php
                                                        $gram_kuning = empty($persen->gr_kuning)
                                                            ? '0'
                                                            : $persen->gr_kuning;
                                                        $hgra_kuning = empty($persen->hrga_kuning)
                                                            ? '0'
                                                            : $persen->hrga_kuning;
                                                    @endphp
                                                    <input type="hidden"
                                                        class="ttl_hrga_kuning{{ $no }} ttl_hrga_kuning{{ $no }}{{ $letter }}"
                                                        value="{{ $gram_kuning * $hgra_kuning }}">
                                                </td>
                                            @else
                                            @endif
                                            <td class="text-end">
                                                {{ empty($persen->gr_kuning) ? 0 : number_format(($persen->gr_kuning / $c->gr_kuning) * 100, 2) }}
                                            </td>

                                        </tr>
                                        @php
                                            $gr += ($persen->gr ?? 0) + ($persen->gr_kuning ?? 0);
                                            $total_rp +=
                                                ($persen->gr ?? 0) * ($persen->hrga ?? 0) +
                                                ($persen->gr_kuning ?? 0) * ($persen->hrga_kuning ?? 0);
                                        @endphp
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <table style="padding: 10px">
                                {{-- <tr>
                                    <td>
                                        <h6>Total Gram Putih </h6>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total_gram{{ $no }}"
                                            readonly value="{{ $c->gr }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Total Gram Kuning </h6>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control total_gram_kuning{{ $no }}" readonly
                                            value="{{ $c->gr_kuning }}">
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td>
                                        <h6>Total Gram </h6>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control gradndtotal_gram{{ $no }}" readonly
                                            value="{{ $c->gr_kuning + $c->gr }}">
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
                                        <h6>Harga(%) &nbsp; {{ $total_rp }}</h6>
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
                        @if ($posisi_id == 1)
                            <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
                            <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
                                <span class="spinner-border spinner-border-sm " role="status"
                                    aria-hidden="true"></span>
                                Loading...
                            </button>
                        @else
                        @endif

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

                $(".nm_grade").on("keyup", function(e) {
                    var id_grade = $(this).attr('id_grade');
                    var nm_grade = $(this).val();

                    $('.nm_grade' + id_grade).val(nm_grade);
                });
            });
        </script>
    @endsection
</x-theme.app>
