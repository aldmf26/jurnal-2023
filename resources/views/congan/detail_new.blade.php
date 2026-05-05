<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6"></div>
        </div>
    </x-slot>
    <x-slot name="cardBody">

        <style>
            .bg-gr_isi {
                background-color: #A2D0FA !important;
                color: white !important;
            }

            /* Desktop */
            .table-grade td,
            .table-grade th {
                padding: 4px 8px !important;
                vertical-align: middle !important;
                font-size: 13px !important;
            }

            .inputan {
                height: 28px !important;
                padding: 2px 6px !important;
                font-size: 13px !important;
                text-align: right;
                width: 100px !important;
            }

            /* Mobile */
            @media only screen and (max-width: 767px) {
                .label_hilang {
                    display: none;
                }

                .table-grade td,
                .table-grade th {
                    padding: 3px 5px !important;
                    font-size: 12px !important;
                }

                .inputan {
                    width: 70px !important;
                    height: 20px !important;
                    font-size: 10px !important;
                }

                .inputan2 {
                    width: 220px !important;
                }

                .td-empty {
                    padding: 2px !important;
                    line-height: 1 !important;
                }
            }
        </style>

        @csrf
        <section class="row">
            <div class="col-lg-8">
                <a href="{{ route('congan.export_congan', ['no_nota' => $no_nota]) }}"
                    class="btn btn-success float-end me-2"><i class="fas fa-file-excel"></i> Export</a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#import"
                    class="btn btn-primary float-end me-2"><i class="fas fa-file-import"></i> Import</a>
            </div>

            @php $posisi_id = Auth::user()->posisi_id; @endphp

            <form action="{{ route('congan.edit_congan') }}" method="post">
                @csrf
                @foreach ($congan as $no => $c)
                    <input type="hidden" value="{{ $c->no_nota }}" name="no_nota">
                    <div class="row">
                        <div class="col-lg-12">
                            <hr style="border: 1px solid black">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" name="tgl[]" value="{{ $c->tgl }}">
                            <input type="hidden" class="form-control" name="id_invoice_congan[]"
                                value="{{ $c->id_invoice_congan }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label>Pemilik</label>
                            <input type="text" class="form-control" name="pemilik[]" value="{{ $c->pemilik }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label>Keterangan</label>
                            <input type="text" class="form-control" name="ket[]" value="{{ $c->ket }}">
                        </div>
                        <div class="col-lg-2 col-4">
                            <label>Persen Air</label>
                            <input type="text" class="form-control persen_air{{ $no }}" name="persen_air[]"
                                value="{{ $c->persen_air }}">
                        </div>
                    </div>

                    <div class="row mb-4 mt-4">
                        <div class="col-lg-10 table-responsive">

                            {{-- Tabel 1: Grade Putih/Kuning --}}
                            <table class="table table-bordered table-sm table-grade">
                                <thead>
                                    <tr>
                                        <th class="dhead">Kategori</th>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead text-end" width="12%">Putih/Beras Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">Putih/Beras Rp/gr</th>
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                        <th class="dhead text-end" width="12%">Kuning Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">Kuning Rp/gr</th>
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_rp = 0;
                                        $gr = 0;
                                        $prevKategori = null;
                                        $prevKelompok = null;
                                        $sub_gr = 0;
                                        $sub_gr_kuning = 0;
                                        $sub_total_rp = 0;
                                        $akumulasi_gr = 0;
                                        $akumulasi_gr_kuning = 0;
                                        $akumulasi_total_rp = 0;
                                        $nomor = 0;
                                    @endphp

                                    @foreach ($grade as $key => $g)
                                        @php
                                            $persen = DB::selectOne(
                                                "SELECT a.gr, a.hrga, a.gr_kuning, a.hrga_kuning, a.gr_beras, a.hrga_beras
                                                FROM tb_cong as a
                                                WHERE a.no_nota = '$c->no_nota'
                                                AND a.id_grade = '$g->id_grade_cong'
                                                AND a.ket = '$c->ket'",
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
                                            $nomor += 1;
                                            $prevKelompok = $g->kelompok;
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
                                                <input type="hidden"
                                                    class="form-control inputan2 nm_grade nm_grade{{ $g->id_grade_cong }}"
                                                    id_grade="{{ $g->id_grade_cong }}"
                                                    name="nm_grade{{ $no }}[]" value="{{ $g->nm_grade }}">
                                                {{ $g->nm_grade }}
                                            </td>

                                            {{-- Putih Gr --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->putih == 'Y')
                                                    <input type="text"
                                                        class="form-control form-control-sm inputan text-end gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" name="gr{{ $no }}[]"
                                                        hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                                @else
                                                    <input type="hidden" name="gr{{ $no }}[]"
                                                        class="gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                                @endif
                                            </td>

                                            {{-- Putih Rp/gr --}}
                                            @if ($posisi_id == 1)
                                                <td
                                                    class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                    @if ($g->putih == 'Y')
                                                        <input type="text"
                                                            class="form-control form-control-sm text-end inputan harga harga{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}"
                                                            name="harga{{ $no }}[]">
                                                    @else
                                                        <input type="hidden" name="harga{{ $no }}[]"
                                                            class="harga harga{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}">
                                                    @endif
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
                                                <input type="hidden" name="harga{{ $no }}[]"
                                                    class="harga harga{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" hruf="{{ $letter }}"
                                                    value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}">
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
                                            @endif

                                            {{-- Comp Putih --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->putih == 'Y')
                                                    {{ empty($persen->gr) ? 0 : number_format(($persen->gr / ($c->gr + $c->gr_kuning + $c->gr_beras)) * 100, 0) }}%
                                                @endif
                                            </td>

                                            {{-- Kuning Gr --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->kuning == 'Y')
                                                    <input type="text"
                                                        class="form-control form-control-sm inputan text-end gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}"
                                                        name="gr_kuning{{ $no }}[]"
                                                        hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                                @else
                                                    <input type="hidden" name="gr_kuning{{ $no }}[]"
                                                        class="gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                                @endif
                                            </td>

                                            {{-- Kuning Rp/gr --}}
                                            @if ($posisi_id == 1)
                                                <td
                                                    class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                    @if ($g->kuning == 'Y')
                                                        <input type="text"
                                                            class="form-control form-control-sm text-end inputan harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}"
                                                            name="harga_kuning{{ $no }}[]">
                                                    @else
                                                        <input type="hidden"
                                                            name="harga_kuning{{ $no }}[]"
                                                            class="harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}">
                                                    @endif
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
                                                <input type="hidden" name="harga_kuning{{ $no }}[]"
                                                    class="harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" hruf="{{ $letter }}"
                                                    value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}">
                                                @php
                                                    $gram_kuning = empty($persen->gr_kuning) ? '0' : $persen->gr_kuning;
                                                    $hgra_kuning =
                                                        empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                                                            ? '0'
                                                            : $persen->hrga_kuning;
                                                @endphp
                                                <input type="hidden"
                                                    class="ttl_hrga_kuning{{ $no }} ttl_hrga_kuning{{ $no }}{{ $letter }}"
                                                    value="{{ $gram_kuning * $hgra_kuning }}">
                                            @endif

                                            {{-- Comp Kuning --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->kuning == 'Y')
                                                    {{ empty($persen->gr_kuning) ? 0 : number_format(($persen->gr_kuning / ($c->gr + $c->gr_kuning + $c->gr_beras)) * 100, 0) }}%
                                                @endif
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
                                                    ? $hrga_dlu_kuning->hrga_kuning ?? 0
                                                    : $persen->hrga_kuning;
                                            $gr += $gram + $gram_kuning;
                                            $total_rp += $gram * $hgra + $gram_kuning * $hgra_kuning;
                                            $sub_gr += $gram;
                                            $sub_gr_kuning += $gram_kuning;
                                            $sub_total_rp += $gram * $hgra + $gram_kuning * $hgra_kuning;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Tabel 2: Grade D/VPTH --}}
                            <table class="table table-bordered table-sm table-grade">
                                <thead>
                                    <tr>
                                        <th class="dhead">Kategori</th>
                                        <th class="dhead">Grade</th>
                                        <th class="dhead text-end" width="12%">D Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">D Rp/gr</th>
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                        <th class="dhead text-end" width="12%">VPTH Gr</th>
                                        @if ($posisi_id == 1)
                                            <th class="dhead text-end" width="12%">VPTH Rp/gr</th>
                                        @endif
                                        <th class="dhead text-end">Comp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_rp2 = 0;
                                        $gr2 = 0;
                                        $prevKategori = null;
                                        $prevKelompok = null;
                                        $sub_gr = 0;
                                        $sub_gr_kuning = 0;
                                        $sub_total_rp = 0;
                                        $akumulasi_gr = 0;
                                        $akumulasi_gr_kuning = 0;
                                        $akumulasi_total_rp = 0;
                                        $nomor = 0;
                                    @endphp

                                    @foreach ($grade2 as $key => $g)
                                        @php
                                            $persen = DB::selectOne(
                                                "SELECT a.gr, a.hrga, a.gr_kuning, a.hrga_kuning, a.gr_beras, a.hrga_beras
                                                FROM tb_cong as a
                                                WHERE a.no_nota = '$c->no_nota'
                                                AND a.id_grade = '$g->id_grade_cong'
                                                AND a.ket = '$c->ket'",
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
                                            $nomor += 1;
                                            $prevKelompok = $g->kelompok;
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
                                                <input type="hidden"
                                                    class="form-control inputan2 nm_grade nm_grade{{ $g->id_grade_cong }}"
                                                    id_grade="{{ $g->id_grade_cong }}"
                                                    name="nm_grade{{ $no }}[]"
                                                    value="{{ $g->nm_grade }}">
                                                {{ $g->nm_grade }}
                                            </td>

                                            {{-- D Gr --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->putih == 'Y')
                                                    <input type="text"
                                                        class="form-control form-control-sm inputan text-end gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" name="gr{{ $no }}[]"
                                                        hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                                @else
                                                    <input type="hidden" name="gr{{ $no }}[]"
                                                        class="gr{{ $no }} gr{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr) ? '0' : $persen->gr }}">
                                                @endif
                                            </td>

                                            {{-- D Rp/gr --}}
                                            @if ($posisi_id == 1)
                                                <td
                                                    class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                    @if ($g->putih == 'Y')
                                                        <input type="text"
                                                            class="form-control form-control-sm text-end inputan harga harga{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}"
                                                            name="harga{{ $no }}[]">
                                                    @else
                                                        <input type="hidden" name="harga{{ $no }}[]"
                                                            class="harga harga{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}">
                                                    @endif
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
                                                <input type="hidden" name="harga{{ $no }}[]"
                                                    class="harga harga{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" hruf="{{ $letter }}"
                                                    value="{{ empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga }}">
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
                                            @endif

                                            {{-- Comp D --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr) && $g->putih == 'Y' ? 'bg-gr_isi' : ($g->putih != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->putih == 'Y')
                                                    {{ empty($persen->gr) ? 0 : number_format(($persen->gr / ($c->gr + $c->gr_kuning + $c->gr_beras)) * 100, 0) }}%
                                                @endif
                                            </td>

                                            {{-- VPTH Gr --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->kuning == 'Y')
                                                    <input type="text"
                                                        class="form-control form-control-sm inputan text-end gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}"
                                                        name="gr_kuning{{ $no }}[]"
                                                        hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                                @else
                                                    <input type="hidden" name="gr_kuning{{ $no }}[]"
                                                        class="gr_kuning{{ $no }} gr_kuning{{ $no }}{{ $letter }}"
                                                        count="{{ $no }}" hruf="{{ $letter }}"
                                                        value="{{ empty($persen->gr_kuning) ? '0' : $persen->gr_kuning }}">
                                                @endif
                                            </td>

                                            {{-- VPTH Rp/gr --}}
                                            @if ($posisi_id == 1)
                                                <td
                                                    class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                    @if ($g->kuning == 'Y')
                                                        <input type="text"
                                                            class="form-control form-control-sm text-end inputan harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}"
                                                            name="harga_kuning{{ $no }}[]">
                                                    @else
                                                        <input type="hidden"
                                                            name="harga_kuning{{ $no }}[]"
                                                            class="harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                            count="{{ $no }}" hruf="{{ $letter }}"
                                                            value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}">
                                                    @endif
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
                                                <input type="hidden" name="harga_kuning{{ $no }}[]"
                                                    class="harga_kuning harga_kuning{{ $no }}{{ $letter }}"
                                                    count="{{ $no }}" hruf="{{ $letter }}"
                                                    value="{{ empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning }}">
                                                @php
                                                    $gram_kuning = empty($persen->gr_kuning) ? '0' : $persen->gr_kuning;
                                                    $hgra_kuning =
                                                        empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                                                            ? '0'
                                                            : $persen->hrga_kuning;
                                                @endphp
                                                <input type="hidden"
                                                    class="ttl_hrga_kuning{{ $no }} ttl_hrga_kuning{{ $no }}{{ $letter }}"
                                                    value="{{ $gram_kuning * $hgra_kuning }}">
                                            @endif

                                            {{-- Comp VPTH --}}
                                            <td
                                                class="text-end {{ !empty($persen->gr_kuning) && $g->kuning == 'Y' ? 'bg-gr_isi' : ($g->kuning != 'Y' ? 'td-empty' : '') }}">
                                                @if ($g->kuning == 'Y')
                                                    {{ empty($persen->gr_kuning) ? 0 : number_format(($persen->gr_kuning / ($c->gr + $c->gr_kuning + $c->gr_beras)) * 100, 0) }}%
                                                @endif
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
                                                    ? $hrga_dlu_kuning->hrga_kuning ?? 0
                                                    : $persen->hrga_kuning;
                                            $gr2 += $gram + $gram_kuning;
                                            $total_rp2 += $gram * $hgra + $gram_kuning * $hgra_kuning;
                                            $sub_gr += $gram;
                                            $sub_gr_kuning += $gram_kuning;
                                            $sub_total_rp += $gram * $hgra + $gram_kuning * $hgra_kuning;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-5">
                            <table style="padding: 10px">
                                <tr>
                                    <td>
                                        <h6>Total Gram </h6>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm gradndtotal_gram{{ $no }}"
                                            readonly value="{{ $c->gr_kuning + $c->gr }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Harga Beli &nbsp;</h6>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm harga_ambil hrga_beli{{ $no }}"
                                            name="hrga_beli[]" value="{{ $c->hrga_beli }}" required>
                                    </td>
                                    <td>
                                        @if ($posisi_id == 1)
                                            @if ($c->selesai == 'Y')
                                                <button type="submit" name="selesai" value="T"
                                                    class="btn btn-warning btn-sm ms-4">Harga Unfix</button>
                                            @else
                                                <button type="submit" name="selesai" value="Y"
                                                    class="btn btn-primary btn-sm ms-4"
                                                    no_nota="{{ $no_nota }}">Harga fix</button>
                                            @endif
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
                                            value="Rp. {{ $gr > 0 ? number_format((($total_rp + $total_rp2) / ($gr + $gr2)) * ((100 - $c->persen_air) / 100), 0) : 0 }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6>Harga(100%) </h6>
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm hrga_persen{{ $no }}"
                                            value="Rp. {{ $gr > 0 ? number_format(($total_rp + $total_rp2) / ($gr + $gr2), 0) : 0 }}"
                                            readonly>
                                    </td>
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
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <a href="{{ route('congan.index') }}"
                            class="float-end btn btn-outline-primary me-2">Batal</a>
                    </div>
                </div>
            </form>

            <form action="{{ route('congan.import_congan') }}" method="post" enctype="multipart/form-data">
                @csrf
                <x-theme.modal title="Congan" idModal="import" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-12">
                            <label>File</label>
                            <input type="file" class="form-control" name="file">
                            <input type="hidden" name="no_nota" value="{{ $no_nota }}">
                        </div>
                    </div>
                </x-theme.modal>
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
                                $('.harga_unfix').show();
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
                        });
                    }
                });

                $(".harga_unfix").on("click", function(e) {
                    var no_nota = $(this).attr('no_nota');
                    $.ajax({
                        type: "get",
                        url: "{{ route('congan.harga_unfix') }}",
                        data: {
                            no_nota: no_nota
                        },
                        success: function(data) {
                            $('.harga_unfix').hide();
                            $('.harga_fix').show();
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
                    });
                });

            });
        </script>
    @endsection
</x-theme.app>
