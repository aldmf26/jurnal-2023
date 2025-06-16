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
                    width: 220px !important;
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
                        <div class="col-lg-8 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead">Kategori</th>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead text-end" width="12%">Putih / Beras Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">Putih / Beras Rp/gr</th>
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
                                    @php
                                        $prevKelompok = null;
                                        $sub_gr = 0;
                                        $sub_gr_kuning = 0;
                                        $sub_total_rp = 0;
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
                                        {{-- @if ($prevKelompok !== null && $prevKelompok != $g->kelompok)
                                           
                                            <tr>
                                                <td>
                                                    <h6>Total Gram </h6>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control " readonly
                                                        value="{{ $sub_gr + $sub_gr_kuning }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <h6>Harga({{ 100 - $c->persen_air }}%) &nbsp;</h6>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" readonly
                                                        value="Rp. {{ number_format(($sub_total_rp / ($sub_gr + $sub_gr_kuning)) * ((100 - $c->persen_air) / 100), 0) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h6>Harga(100%) </h6>
                                                </td>
                                                <td><input type="text" class="form-control"
                                                        value="Rp. {{ number_format($sub_total_rp / ($sub_gr + $sub_gr_kuning), 0) }}"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr style="background: #e0e0e0; font-weight: bold;">
                                                <td colspan="8">&nbsp;</td>
                                            </tr>


                                            @php
                                                $sub_gr = 0;
                                                $sub_gr_kuning = 0;
                                                $sub_total_rp = 0;
                                            @endphp
                                        @endif --}}

                                        @php $prevKelompok = $g->kelompok; @endphp
                                        <style>
                                            .bg-gr_isi {
                                                background-color: #A2D0FA !important;
                                                color: white !important;
                                            }
                                        </style>
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
                                                    {{ $g->nm_grade }}
                                                    <input type="hidden"
                                                        class="form-control  inputan2 nm_grade nm_grade{{ $g->id_grade_cong }}"
                                                        id_grade="{{ $g->id_grade_cong }}"
                                                        name="nm_grade{{ $no }}[]"
                                                        value="{{ $g->nm_grade }}">
                                                @else
                                                    {{ $g->nm_grade }}
                                                @endif
                                            </td>
                                            <td class="text-end {{ !empty($persen->gr) ? 'bg-gr_isi' : '' }}">

                                                <input type="text"
                                                    class="form-control inputan text-end gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" name="gr{{ $no }}[]"
                                                    hruf="{{ $letter }}"
                                                    value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                            </td>

                                            @if ($posisi_id == 1)
                                                <td class="text-end {{ !empty($persen->gr) ? 'bg-gr_isi' : '' }}">
                                                    <input type="text"
                                                        class="form-control text-end inputan harga harga{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}"
                                                        name="harga{{ $no }}[]">
                                                    @php
                                                        $gram = empty($persen->gr) ? '0' : $persen->gr;
                                                        $hgra =
                                                            empty($persen->hrga) || $persen->hrga == 0
                                                                ? '0'
                                                                : $persen->hrga;
                                                    @endphp
                                                    <input type="hidden"
                                                        class="ttl_hrga{{ $no }} ttl_hrga{{ $no }}{{ $letter }}"
                                                        value="{{ $gram * $hgra }}">
                                                </td>
                                            @else
                                            @endif
                                            <td class="text-end {{ !empty($persen->gr) ? 'bg-gr_isi' : '' }}">
                                                {{ empty($persen->gr) ? 0 : number_format(($persen->gr / ($c->gr + $c->gr_kuning)) * 100, 0) }}
                                                %
                                            </td>
                                            <td class="text-end {{ !empty($persen->gr_kuning) ? 'bg-gr_isi' : '' }}">

                                                <input type="text"
                                                    class="form-control inputan text-end gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" name="gr_kuning{{ $no }}[]"
                                                    hruf="{{ $letter }}"
                                                    value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                            </td>
                                            @if ($posisi_id == 1)
                                                <td
                                                    class="text-end {{ !empty($persen->gr_kuning) ? 'bg-gr_isi' : '' }}">

                                                    <input type="text"
                                                        class="form-control text-end inputan harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu->hrga_kuning ?? 0 : $persen->hrga_kuning }}"
                                                        name="harga_kuning{{ $no }}[]">
                                                    @php
                                                        $gram_kuning = empty($persen->gr_kuning)
                                                            ? '0'
                                                            : $persen->gr_kuning;
                                                        $hgra_kuning =
                                                            empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                                                                ? '0'
                                                                : $persen->hrga_kuning;
                                                    @endphp
                                                    <input type="hidden"
                                                        class="ttl_hrga_kuning{{ $no }} ttl_hrga_kuning{{ $no }}{{ $letter }}"
                                                        value="{{ $gram_kuning * $hgra_kuning }}">
                                                </td>
                                            @else
                                            @endif
                                            <td class="text-end {{ !empty($persen->gr_kuning) ? 'bg-gr_isi' : '' }}">
                                                {{ empty($persen->gr_kuning) ? 0 : number_format(($persen->gr_kuning / ($c->gr + $c->gr_kuning)) * 100, 0) }}
                                                %
                                            </td>

                                        </tr>
                                        @php
                                            $gram = $persen->gr ?? 0;
                                            $hgra =
                                                empty($persen->hrga) || $persen->hrga == 0
                                                    ? $hrga_dlu->hrga ?? 0
                                                    : $persen->hrga;
                                            $gram_kuning = $persen->gr_kuning ?? 0;
                                            $hgra_kuning =
                                                empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                                                    ? $hrga_dlu->hrga_kuning ?? 0
                                                    : $persen->hrga_kuning;

                                            $gr += $gram + $gram_kuning;
                                            $total_rp += $gram * $hgra + $gram_kuning * $hgra_kuning;

                                            $sub_gr += $gram;
                                            $sub_gr_kuning += $gram_kuning;
                                            $sub_total_rp += $gram * $hgra + $gram_kuning * $hgra_kuning;
                                        @endphp
                                    @endforeach
                                    {{-- @if ($prevKelompok !== null)
                                        
                                        <tr>
                                            <td>
                                                <h6>Total Gram </h6>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control " readonly
                                                    value="{{ $sub_gr + $sub_gr_kuning }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <h6>Harga({{ 100 - $c->persen_air }}%) &nbsp;</h6>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" readonly
                                                    value="Rp. {{ $sub_gr + $sub_gr_kuning == 0 ? 0 : number_format(($sub_total_rp / ($sub_gr + $sub_gr_kuning)) * ((100 - $c->persen_air) / 100), 0) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h6>Harga(100%) </h6>
                                            </td>
                                            <td><input type="text" class="form-control"
                                                    value="Rp. {{ $sub_gr + $sub_gr_kuning == 0 ? 0 : number_format($sub_total_rp / ($sub_gr + $sub_gr_kuning), 0) }}"
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr style="background: #e0e0e0; font-weight: bold;">
                                            <td colspan="8">&nbsp;</td>
                                        </tr>
                                    @endif --}}
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-5">
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

                                        <input type="text"
                                            class="form-control harga_ambil hrga_beli{{ $no }}"
                                            name="hrga_beli[]" value="{{ $c->hrga_beli }}" required>

                                    </td>
                                    <td>


                                        @if ($c->selesai == 'Y')
                                            <span class="ms-4 badge bg-success">Harga sudah fix</span>
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-primary ms-4 harga_fix"
                                                no_nota="{{ $no_nota }}">Harga
                                                fix</a>
                                            <span class="badge ms-4 bg-success harga_selsai_muncul"
                                                style="display: none">Harga sudah
                                                fix</span>
                                        @endif
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
                                        <h6>Harga(100%) </h6>
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
                $(".harga_fix").on("click", function(e) {
                    var no_nota = $(this).attr('no_nota');
                    var hrga = $('.harga_ambil').val();

                    if (hrga === '0' || hrga === '') {
                        Toastify({
                            text: "Harga tidak boleh kosong",
                            duration: 3000,
                            style: {
                                background: "#FCEDE9",
                                color: "#7F8B8B"
                            },
                            close: true,
                            avatar: "https://cdn-icons-png.flaticon.com/512/564/564619.png"
                        }).showToast();
                    } else {
                        $.ajax({
                            type: "get",
                            url: "{{ route('congan.harga_fix') }}",
                            data: {
                                no_nota: no_nota
                            },
                            success: function(data) {
                                $('.harga_fix').hide();
                                $('.harga_selsai_muncul').show();



                                Toastify({
                                    text: "Harga berhasil di simpan",
                                    duration: 3000,
                                    style: {
                                        background: "#EAF7EE",
                                        color: "#7F8B8B"
                                    },
                                    close: true,
                                    avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
                                }).showToast();
                            }

                        })
                    }

                });


            });
        </script>
    @endsection
</x-theme.app>
