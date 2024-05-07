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
        {{-- <form action="{{ route('gudangBk.export_buku_campur_bk') }}" method="post"> --}}
        @csrf
        <section class="row">

            {{-- <div class="col-lg-9">
                @include('laporan_produksi.navlaporan')
            </div> --}}
            {{-- <div class="col-lg-3">
                    <table class="float-end">
                        <td>Search :</td>
                        <td><input type="text" id="pencarian" class="form-control float-end"></td>
                    </table>
                </div> --}}
            <div class="col-lg-12 mt-2">
                <div class="table-container table-responsive">


                    <table class="table table-hover table-bordered" id="tableSearch" width="100%">
                        <thead>
                            <tr>
                                <th class="dhead" rowspan="2">#</th>
                                <th class="dhead" rowspan="2">Ket / nama partai</th>
                                <th class="dhead" rowspan="2">Grade</th>
                                <th class="dhead text-center tdhide" colspan="3">BK</th>
                                <th class="dhead text-center tdhide" colspan="3">BK TIMBANG ULANG</th>
                                <th class="dhead text-center tdhide" colspan="3">Susut BK</th>

                                <th class="text-white text-center bg-danger tdhide" colspan="3">BK Sisa</th>

                                {{-- <th class="dhead text-center" rowspan="2">Selesai Bk</th> --}}

                                <th class="dhead text-center" colspan="9">Cabut</th>
                                <th class="bg-danger text-white text-center" colspan="2">Bk Sisa Pgws</th>

                                {{-- <th class="dhead" rowspan="2">Selesai</th> --}}
                                <th class="dhead_second text-center" colspan="11">Cetak</th>
                                <th class="dhead_second text-end" rowspan="2">Cost Bk</th>
                                <th class="dhead_second text-end" rowspan="2">Cost Cbt</th>
                                <th class="dhead_second text-end" rowspan="2">Cost Ctk</th>
                            </tr>
                            <tr>

                                <th class="dhead text-center tdhide">Pcs</th>
                                <th class="dhead text-center tdhide">Gr</th>
                                <th class="dhead text-center tdhide">Ttl Rp</th>
                                <th class="dhead text-center tdhide">Pcs</th>
                                <th class="dhead text-center tdhide">Gr</th>
                                <th class="dhead text-center tdhide">Ttl Rp</th>

                                <th class="dhead text-center tdhide">Pcs</th>
                                <th class="dhead text-center tdhide">Gr</th>
                                <th class="dhead text-center tdhide">sst(%)</th>

                                <th class="text-white text-center bg-danger tdhide">Pcs</th>
                                <th class="text-white text-center bg-danger tdhide">Gr</th>
                                <th class="text-white text-center bg-danger tdhide">Ttl Rp</th>



                                <th class="dhead text-center">Pcs Awal</th>
                                <th class="dhead text-center">Gr Awal</th>
                                <th class="dhead text-center">Pcs Akhir</th>
                                <th class="dhead text-center">Gr Akhir</th>
                                <th class="dhead text-center">Susut</th>
                                <th class="dhead text-center">Eot</th>
                                <th class="dhead text-center">Flx</th>
                                <th class="dhead text-center">Cost bk</th>
                                <th class="dhead text-center">Cost cabut</th>

                                <th class="text-white bg-danger text-center">Pcs</th>
                                <th class="text-white bg-danger text-center">Gr</th>

                                <th class="dhead_second text-center">Pcs Awal</th>
                                <th class="dhead_second text-center">Gr Awal</th>
                                <th class="dhead_second text-center">Pcs Tdk ctk</th>
                                <th class="dhead_second text-center">Gr Tdk ctk</th>
                                <th class="dhead_second text-center">Pcs awal ctk</th>
                                <th class="dhead_second text-center">Gr awal ctk</th>
                                <th class="dhead_second text-center">Pcs cu</th>
                                <th class="dhead_second text-center">Gr cu</th>
                                <th class="dhead_second text-center">Pcs akhir</th>
                                <th class="dhead_second text-center">Gr akhir</th>
                                <th class="dhead_second text-center">Susut</th>


                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlwippcs = 0;
                                $ttlwipgr = 0;
                                $ttlwiprupiah = 0;
                                $ttlbtupcs = 0;
                                $ttlbtugr = 0;
                                $ttlbtuttlrp = 0;
                                $ttlsstbk = 0;
                                $ttlsstbkgr = 0;
                                $ttlbksisapcs = 0;
                                $ttlbksisagr = 0;
                                $ttlbksisattlrp = 0;
                                $ttlcbtawalpcs = 0;
                                $ttlcbtawalgr = 0;
                                $ttlcbtakhirpcs = 0;
                                $ttlcbtakhirgr = 0;
                                $ttlcbteot = 0;
                                $ttlcbtflx = 0;
                                $ttlbksisapgwspcs = 0;
                                $ttlbksisapgwsgr = 0;
                                $ttlrpcost = 0;
                                $ttlrpbk = 0;
                            @endphp
                            @php
                                $ttl_pcs_ambil_ctk = 0;
                                $ttl_gr_ambil_ctk = 0;
                                $ttl_pcs_tdk_ctk = 0;
                                $ttl_gr_tdk_ctk = 0;
                                $ttl_pcs_awal_ctk = 0;
                                $ttl_gr_awal_ctk = 0;
                                $ttl_pcs_cu = 0;
                                $ttl_gr_cu = 0;
                                $ttl_pcs_akhir_ctk = 0;
                                $ttl_gr_akhir_ctk = 0;
                                $ttl_bk_cost_ctk = 0;
                                $ttl_cost_ctk = 0;
                            @endphp
                            @foreach ($gudang as $no => $g)
                                @php
                                    $ket = $g->ket2;
                                    $resSum = Cache::remember(
                                        'datacabutsum5_' . $ket,
                                        now()->addMinutes(5),
                                        function () use ($ket, $linkApi) {
                                            return Http::get("$linkApi/datacabutsum2", ['nm_partai' => $ket])->object();
                                        },
                                    );
                                    $c = $resSum;
                                    $g->relatedModel = $c;
                                    $resSum2 = Cache::remember(
                                        'datacetaksum5_' . $ket,
                                        now()->addMinutes(5),
                                        function () use ($ket, $linkApi) {
                                            return Http::get("$linkApi/cetak_laporan_all", [
                                                'nm_partai' => $ket,
                                            ])->object();
                                        },
                                    );
                                    $d = $resSum2;
                                    $g->relatedModel = $d;

                                    $wipPcs = $g->pcs ?? 0;
                                    $wipGr = $g->gr ?? 0;
                                    $wipTllrp = $g->total_rp ?? 0;
                                    $bkPcs = $c->pcs_bk ?? 0;
                                    $bkGr = $c->gr_awal_bk ?? 0;

                                    $gr_susut = $g->gr_susut ?? 0;
                                    $pcs_susut = $g->pcs_susut ?? 0;
                                    $WipSisaPcs = $wipPcs - $bkPcs - $pcs_susut;
                                    $WipSisaGr = $wipGr - $bkGr - $gr_susut;
                                    $selesai_bk = $c->selesai ?? 'T';
                                    $gr_akhir_cbt = $c->gr_akhir ?? 0;
                                    $gr_akhir_eo = $c->gr_eo_akhir ?? 0;

                                    $hrga_modal_satuan = $wipTllrp / ($wipGr - $gr_susut);
                                    $pcs_awal_bk = $c->pcs_bk ?? 0;
                                    $gr_awal_bk = $c->gr_awal_bk ?? 0;

                                    $pcs_awal_cbt = $c->pcs_awal ?? 0;
                                    $gr_awal_cbt = $c->gr_awal ?? 0;
                                    $gr_awal_eo = $c->gr_awal_eo ?? 0;
                                    $ttl_rp_cbt = $c->ttl_rp ?? 0;
                                    $ttl_rp_eo = $c->ttl_rp_eo ?? 0;
                                @endphp
                                @php
                                    $ttlwippcs += $wipPcs;
                                    $ttlwipgr += $wipGr;
                                    $ttlwiprupiah += $wipTllrp;
                                    $ttlbtupcs += $bkPcs;
                                    $ttlbtugr += $bkGr;
                                    $ttlbtuttlrp += $g->selesai == 'Y' ? $bkGr * $hrga_modal_satuan : 0;
                                    $ttlsstbk += $g->pcs_susut ?? 0;
                                    $ttlsstbkgr += $g->gr_susut ?? 0;
                                    $ttlbksisapcs += $WipSisaPcs;
                                    $ttlbksisagr += $WipSisaGr;
                                    $ttlbksisattlrp += $hrga_modal_satuan * $WipSisaGr;
                                    $ttlcbtawalpcs += $c->pcs_awal ?? 0;
                                    $ttlcbtawalgr += $gr_awal_cbt + $gr_awal_eo;
                                    $ttlcbtakhirpcs += $c->pcs_akhir ?? 0;
                                    $ttlcbtakhirgr += $gr_akhir_cbt + $gr_akhir_eo;
                                    $ttlcbteot += $c->eot ?? 0;
                                    $ttlcbtflx += $c->gr_flx ?? 0;
                                    $ttlbksisapgwspcs += $pcs_awal_bk - $pcs_awal_cbt;
                                    $ttlbksisapgwsgr += $gr_awal_bk - ($gr_awal_cbt + $gr_awal_eo);
                                    $ttlrpcost += $ttl_rp_cbt + $ttl_rp_eo;
                                    $ttlrpbk += empty($gr_awal_cbt)
                                        ? 0
                                        : (($gr_awal_cbt + $gr_awal_eo) / $bkGr) * ($bkGr * $hrga_modal_satuan);
                                @endphp

                                @php
                                    $ttl_pcs_ambil_ctk += $d->pcs_awal_ambil_ctk ?? 0;
                                    $ttl_gr_ambil_ctk += $d->gr_awal_ambil_ctk ?? 0;
                                    $ttl_pcs_tdk_ctk += $d->pcs_tdk_ctk ?? 0;
                                    $ttl_gr_tdk_ctk += $d->gr_tdk_ctk ?? 0;
                                    $ttl_pcs_awal_ctk += $d->pcs_ctk ?? 0;
                                    $ttl_gr_awal_ctk += $d->gr_ctk ?? 0;

                                    $ttl_pcs_cu += $d->pcs_cu ?? 0;
                                    $ttl_gr_cu += $d->gr_cu ?? 0;

                                    $ttl_pcs_akhir_ctk += $d->pcs_akhir_ctk ?? 0;
                                    $ttl_gr_akhir_ctk += $d->gr_akhir_ctk ?? 0;

                                    $ttl_bk_cost_ctk += $d->cost_bk_ctk ?? 0;
                                    $ttl_cost_ctk += $d->cost_ctk ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $no + 1 }} </td>
                                    <td>
                                        {{ $g->ket2 }}
                                    </td>
                                    <td class="text-center ">
                                        {{ $g->nm_grade }}
                                    </td>


                                    <td class="text-end  tdhide">{{ number_format($wipPcs, 0) }}
                                    </td>
                                    <td class="text-end  tdhide">{{ number_format($wipGr, 0) }}</td>
                                    <td class="text-end  tdhide">{{ number_format($wipTllrp, 0) }}</td>
                                    <td class="text-end  tdhide">{{ number_format($bkPcs, 0) }} </td>
                                    <td class="text-end  tdhide">{{ number_format($bkGr, 0) }}</td>
                                    <td class="text-end  tdhide">
                                        {{-- {{ $g->selesai == 'Y' ? number_format($bkGr * $hrga_modal_satuan, 0) : '0' }} --}}
                                        {{ number_format($bkGr * $hrga_modal_satuan, 0) }}
                                    </td>

                                    <td class="text-end  tdhide">{{ number_format($g->pcs_susut ?? 0, 0) }}
                                    </td>
                                    <td class="text-end  tdhide">{{ number_format($g->gr_susut ?? 0, 0) }}
                                    </td>
                                    <td class="text-end  tdhide">
                                        {{ number_format((1 - $bkGr / $wipGr) * 100, 1) }}%
                                    </td>

                                    <td class="text-end  text-danger tdhide">
                                        {{ number_format($WipSisaPcs, 0) }}
                                    </td>
                                    <td class="text-end  text-danger tdhide">
                                        {{ number_format($WipSisaGr, 0) }}
                                    </td>
                                    <td class="text-end  text-danger tdhide">
                                        {{ number_format($hrga_modal_satuan * $WipSisaGr, 0) }}
                                    </td>

                                    {{-- <td class="text-center ">
                                        proses
                                    </td> --}}
                                    <td class="text-end ">{{ number_format($c->pcs_awal ?? 0, 0) }} </td>

                                    <td class="text-end ">{{ number_format($gr_awal_cbt + $gr_awal_eo, 0) }}
                                    </td>
                                    <td class="text-end ">{{ number_format($c->pcs_akhir ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($gr_akhir_cbt + $gr_akhir_eo, 0) }}
                                    </td>
                                    <td class="text-end ">
                                        <a href="#" data-bs-toggle="modal" nm_partai="{{ $g->ket2 }}"
                                            data-bs-target="#load_bk_cabut" class="show_box fw-bold">
                                            <u>
                                                {{ $gr_awal_cbt + $gr_awal_eo == 0 ? 0 : number_format((1 - ($gr_akhir_cbt + $gr_akhir_eo) / ($gr_awal_cbt + $gr_awal_eo)) * 100, 1) }}%
                                            </u>
                                        </a>
                                    </td>
                                    <td class="text-end ">{{ number_format($c->eot ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($c->gr_flx ?? 0, 0) }}</td>

                                    <td class="text-end ">
                                        {{ number_format((($gr_awal_cbt + $gr_awal_eo) / $bkGr) * ($bkGr * $hrga_modal_satuan), 0) }}
                                    </td>
                                    <td class="text-end ">{{ number_format($ttl_rp_cbt + $ttl_rp_eo, 0) }}</td>

                                    <td class="text-end text-danger ">
                                        {{ number_format($pcs_awal_bk - $pcs_awal_cbt, 0) }}</td>
                                    <td class="text-end text-danger ">
                                        {{ number_format($gr_awal_bk - ($gr_awal_cbt + $gr_awal_eo), 0) }}
                                    </td>
                                    <td class="text-end ">{{ number_format($d->pcs_awal_ambil_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->gr_awal_ambil_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->pcs_tdk_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->gr_tdk_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->pcs_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->gr_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->pcs_cu ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->gr_cu ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->pcs_akhir_ctk ?? 0, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->gr_akhir_ctk ?? 0, 0) }}</td>
                                    @php
                                        $gr_awal_ctk = $d->gr_ctk ?? 0;
                                        $gr_cu = $d->gr_cu ?? 0;
                                        $gr_akhir_ctk = $d->gr_akhir_ctk ?? 0;

                                        $susut =
                                            $gr_akhir_ctk == 0
                                                ? 0
                                                : (1 - ($gr_akhir_ctk + $gr_cu) / $gr_awal_ctk) * 100;

                                        $cost_bk_bts_cbt =
                                            (($gr_awal_cbt + $gr_awal_eo) / $bkGr) * ($bkGr * $hrga_modal_satuan);
                                    @endphp

                                    <td class="text-end ">{{ number_format($susut, 0) }}%</td>
                                    <td class="text-end ">
                                        {{-- {{ number_format(($gr_awal_ctk / ($gr_akhir_cbt + $gr_akhir_eo)) * $cost_bk_bts_cbt, 0) }}
                                        / --}}
                                        {{ number_format($d->cost_bk_ctk ?? 0, 0) }}
                                    </td>
                                    <td class="text-end ">{{ number_format($ttl_rp_cbt + $ttl_rp_eo, 0) }}</td>
                                    <td class="text-end ">{{ number_format($d->cost_ctk ?? 0, 0) }}</td>
                                    {{-- <td class="text-center ">
                                        @if ($g->selesai_1 == 'Y')
                                            <a href="#" class="btn btn-primary btn-sm finish"
                                                data-bs-toggle="modal" data-bs-target="#load_bk_finish"
                                                lokasi="{{ $lokasi }}" nm_partai="{{ $g->ket2 }}"
                                                gudang="{{ $nm_gudang }}">Selesai</a>
                                        @else
                                        @endif
                                        proses

                                    </td> --}}

                                </tr>
                            @endforeach



                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="dhead" colspan="3">Total</td>

                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlwippcs, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlwipgr, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlwiprupiah, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlbtupcs, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlbtugr, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlbtuttlrp, 0) }}</td>

                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlsstbk, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">{{ number_format($ttlsstbkgr, 0) }}</td>
                                <td class="text-end dhead fw-bold tdhide">-</td>

                                <td class="text-end bg-danger text-white fw-bold  tdhide">
                                    {{ number_format($ttlbksisapcs, 0) }}
                                </td>
                                <td class="text-end bg-danger text-white fw-bold  tdhide">
                                    {{ number_format($ttlbksisagr, 0) }}
                                </td>
                                <td class="text-end fw-bold  bg-danger text-white tdhide">
                                    {{ number_format($ttlbksisattlrp, 0) }}
                                </td>

                                {{-- <td class="text-center dhead fw-bold">-</td> --}}
                                <td class="text-end dhead fw-bold">{{ number_format($ttlcbtawalpcs, 0) }}</td>
                                <td class="text-end dhead fw-bold">{{ number_format($ttlcbtawalgr, 0) }}</td>
                                <td class="text-end dhead fw-bold">{{ number_format($ttlcbtakhirpcs, 0) }}</td>
                                <td class="text-end dhead fw-bold">{{ number_format($ttlcbtakhirgr, 0) }}</td>
                                <td class="text-end fw-bold dhead">-</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttlcbteot, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttlcbtflx, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttlrpbk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttlrpcost, 0) }}</td>
                                <td class="text-end bg-danger text-white fw-bold">
                                    {{ number_format($ttlbksisapgwspcs, 0) }}
                                </td>
                                <td class="text-end bg-danger text-white fw-bold">
                                    {{ number_format($ttlbksisapgwsgr, 0) }}
                                </td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_pcs_ambil_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_gr_ambil_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_pcs_tdk_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_gr_tdk_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_pcs_awal_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_gr_awal_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_pcs_cu, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_gr_cu, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_pcs_akhir_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_gr_akhir_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">-</td>

                                <td class="text-end fw-bold dhead">{{ number_format($ttl_bk_cost_ctk, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttlrpcost, 0) }}</td>
                                <td class="text-end fw-bold dhead">{{ number_format($ttl_cost_ctk, 0) }}</td>
                                {{-- <td class="text-center fw-bold dhead"></td> --}}

                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

        </section>
        {{-- </form> --}}
        <x-theme.modal title="Data Bk Cabut" idModal="load_bk_cabut" btnSave="T" size="modal-lg-max">
            <button class="btn btn-primary btn-loading" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
            </button>
            <div class="load_box"></div>
        </x-theme.modal>
    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                pencarian('pencarian', 'tableSearch')
                $(document).on('click', '#checkAll', function() {
                    // Setel properti checked dari kotak centang individu sesuai dengan status "cek semua"
                    $('.checkbox-item').prop('checked', $(this).prop('checked'));
                });

                var currentNoLot = '';
                var currentNmPartai = '';

                $(document).on('click', '.show_box', function(e) {
                    e.preventDefault();
                    $('.btn-loading').removeClass('d-none');

                    var no_lot = $(this).attr('no_lot');
                    var nm_partai = $(this).attr('nm_partai');
                    currentNoLot = no_lot;
                    currentNmPartai = nm_partai;
                    $('.no_lot_input').val(no_lot);
                    $('.nm_partai_input').val(nm_partai);
                    console.log(no_lot);
                    console.log(nm_partai);
                    $('.load_box').html('');
                    loadBoxData(no_lot, nm_partai, 5); // Default limit 5
                });
                $(document).on('change', '.load-data', function() {
                    $('.btn-loading').removeClass('d-none');
                    var val = $(this).val();
                    $('.load_box').html('');
                    loadBoxData(currentNoLot, currentNmPartai, val);
                    $(this).val(val);
                });

                function loadBoxData(no_lot, nm_partai, limit) {
                    $.ajax({
                        type: "get",
                        url: "{{ route('gudangnew.get_no_box') }}",
                        data: {
                            no_lot: no_lot,
                            nm_partai: nm_partai,
                            limit: limit // Menambahkan parameter limit ke dalam data yang dikirimkan
                        },
                        success: function(response) {
                            $('.btn-loading').addClass('d-none');
                            $('.load_box').html(response);
                            pencarian('pencarianBox', 'tblAldi2')

                        }
                    });
                }


            });
        </script>
    @endsection
</x-theme.app>
