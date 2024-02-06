<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="table-container table-responsive">
            <table class="table table-hover table-bordered" id="tableSearch" width="100%">
                <thead>
                    <tr>
                        <th class="dhead" rowspan="2">#</th>
                        <th class="dhead" rowspan="2">Ket / nama partai</th>
                        <th class="dhead" rowspan="2">Grade</th>

                        @if ($nm_gudang == 'summary')
                            <th class="dhead text-center " colspan="3">Wip</th>
                            <th class="dhead text-center " colspan="3">BK</th>
                        @else
                            <th class="dhead text-center " colspan="2">Wip</th>
                            <th class="dhead text-center " colspan="2">BK</th>
                        @endif



                        <th class="dhead text-center " colspan="3">Susut Wip - bk</th>
                        @if ($nm_gudang == 'summary')
                            <th class="text-white text-center bg-danger " colspan="3">Wip Sisa</th>
                        @else
                            <th class="text-white text-center bg-danger " colspan="2">Wip Sisa</th>
                        @endif
                        <th class="dhead text-center" rowspan="2">Selesai Bk</th>

                        <th class="dhead text-center" colspan="7">Cabut</th>
                        <th class="bg-danger text-white text-center" colspan="2">Bk Sisa Pgws</th>
                        <th class="dhead" rowspan="2">Ttl Rp Cost</th>
                        <th class="dhead" rowspan="2">Ttl Rp Bk</th>
                    </tr>
                    <tr>
                        @if ($nm_gudang == 'summary')
                            <th class="dhead text-center ">Pcs</th>
                            <th class="dhead text-center ">Gr</th>
                            <th class="dhead text-center ">Ttl Rp</th>
                            <th class="dhead text-center ">Pcs</th>
                            <th class="dhead text-center ">Gr</th>
                            <th class="dhead text-center ">Ttl Rp</th>
                        @else
                            <th class="dhead text-center ">Pcs</th>
                            <th class="dhead text-center ">Gr</th>
                            <th class="dhead text-center ">Pcs</th>
                            <th class="dhead text-center ">Gr</th>
                        @endif
                        <th class="dhead text-center ">Pcs</th>
                        <th class="dhead text-center ">Gr</th>
                        <th class="dhead text-center ">sst(%)</th>
                        @if ($nm_gudang == 'summary')
                            <th class="text-white text-center bg-danger ">Pcs</th>
                            <th class="text-white text-center bg-danger ">Gr</th>
                            <th class="text-white text-center bg-danger ">Ttl Rp</th>
                        @else
                            <th class="text-white text-center bg-danger ">Pcs</th>
                            <th class="text-white text-center bg-danger ">Gr</th>
                        @endif


                        <th class="dhead text-center">Pcs Awal</th>
                        <th class="dhead text-center">Gr Awal</th>
                        <th class="dhead text-center">Pcs Akhir</th>
                        <th class="dhead text-center">Gr Akhir</th>
                        <th class="dhead text-center">Susut</th>
                        <th class="dhead text-center">Eot</th>
                        <th class="dhead text-center">Flx</th>

                        <th class="text-white bg-danger text-center">Pcs</th>
                        <th class="text-white bg-danger text-center">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gudang as $no => $g)
                        @php
                            $ket = $g->ket2;
                            $resSum = Cache::remember('datacabutsum2_' . $ket, now()->addHours(8), function () use ($ket, $linkApi) {
                                return Http::get("$linkApi/datacabutsum2", ['nm_partai' => $ket])->object();
                            });
                            $c = $resSum;
                            $g->relatedModel = $c;

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
                        @endphp

                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" nm_partai="{{ $g->ket2 }}"
                                    data-bs-target="#load_bk_cabut" class="show_box">{{ $g->ket2 }}</a>
                            </td>
                            <td class="text-center fw-bold">
                                {{ $g->nm_grade }}
                            </td>
                            @php
                                $hrga_modal_satuan = $wipTllrp / ($wipGr - $gr_susut);
                            @endphp
                            @if ($nm_gudang == 'summary')
                                <td class="text-end fw-bold ">{{ number_format($wipPcs, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($wipGr, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($wipTllrp, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($bkPcs, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($bkGr, 0) }}</td>
                                <td class="text-end fw-bold ">
                                    {{ $g->selesai == 'Y' ? number_format($bkGr * $hrga_modal_satuan, 0) : '0' }}
                                </td>
                            @else
                                <td class="text-end fw-bold ">{{ number_format($wipPcs, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($wipGr, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($bkPcs, 0) }}</td>
                                <td class="text-end fw-bold ">{{ number_format($bkGr, 0) }}</td>
                            @endif
                            <td class="text-end fw-bold ">{{ number_format($g->pcs_susut ?? 0, 0) }}
                            </td>
                            <td class="text-end fw-bold ">{{ number_format($g->gr_susut ?? 0, 0) }}
                            </td>
                            <td class="text-end fw-bold ">
                                {{ number_format((1 - $bkGr / $wipGr) * 100, 1) }}%
                            </td>
                            @if ($nm_gudang == 'summary')
                                <td class="text-end fw-bold text-danger ">
                                    {{ number_format($WipSisaPcs, 0) }}
                                </td>
                                <td class="text-end fw-bold text-danger ">
                                    {{ number_format($WipSisaGr, 0) }}
                                </td>
                                <td class="text-end fw-bold text-danger ">
                                    {{ number_format($hrga_modal_satuan * $WipSisaGr, 0) }}
                                </td>
                            @else
                                <td class="text-end fw-bold text-danger ">
                                    {{ number_format($WipSisaPcs, 0) }}
                                </td>
                                <td class="text-end fw-bold text-danger ">
                                    {{ number_format($WipSisaGr, 0) }}
                                </td>
                            @endif
                            <td class="text-center fw-bold">
                                @if ($g->selesai_1 == 'Y')
                                    <i class="fas fa-check text-success fa-lg"></i>
                                @else
                                    @if ($g->selesai == 'Y')
                                        <a href="#" class="btn btn-sm btn-primary selesai_box"
                                            data-bs-toggle="modal" data-bs-target="#load_bk_selesai"
                                            lokasi="{{ $lokasi }}" nm_partai="{{ $g->ket2 }}"
                                            gudang="{{ $nm_gudang }}">Selesai</a>
                                    @else
                                        <a href="#"><i class="fas  fa-hourglass-half text-danger"></i></a>
                                    @endif
                                @endif

                            </td>
                            <td class="text-end fw-bold">{{ number_format($c->pcs_awal ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->gr_awal ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->pcs_akhir ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->gr_akhir ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->susut ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->eot ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($c->gr_flx ?? 0, 0) }}</td>
                            @php
                                $pcs_awal_bk = $c->pcs_bk ?? 0;
                                $gr_awal_bk = $c->gr_awal_bk ?? 0;

                                $pcs_awal_cbt = $c->pcs_awal ?? 0;
                                $gr_awal_cbt = $c->gr_awal ?? 0;
                            @endphp
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($pcs_awal_bk - $pcs_awal_cbt, 0) }}</td>
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($gr_awal_bk - $gr_awal_cbt, 0) }}
                            </td>

                            <td class="text-end fw-bold">{{ number_format($c->ttl_rp ?? 0, 0) }}</td>
                            <td class="text-end fw-bold">
                                @if ($g->selesai_1 == 'Y')
                                    {{ number_format($hrga_modal_satuan * $gr_akhir_cbt, 0) }}
                                @else
                                @endif

                            </td>

                        </tr>
                    @endforeach



                </tbody>
            </table>
        </div>
    </div>
</div>