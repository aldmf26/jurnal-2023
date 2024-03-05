@php
    if ($nm_gudang == 'wipcetak') {
        $url = 'gudangBk.export_wip_cetak';
    } else {
        $url = 'gudangBk.export_buku_campur_bk';
    }

@endphp

<form action="{{ route($url) }}" method="post">
    @csrf
    <div class="row">
        <div class="col-lg-3">
            <button class="btn btn-warning kembali">Kembali</button>
        </div>
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            @if ($nm_gudang == 'wipcetak')
                <x-theme.button modal="Y" idModal="importcetak" icon="fas fa-upload" addClass="float-end"
                    teks="Import" />
            @else
                <x-theme.button modal="Y" idModal="import" icon="fas fa-upload" addClass="float-end"
                    teks="Import" />
            @endif

        </div>

        {{-- <div class="col-lg-3">
            <table class="float-end">
                <td>Search :</td>
                <td><input type="text" id="pencarian" class="form-control float-end"></td>
            </table>
        </div> --}}
        <div class="col-lg-12 mt-2">

            <div class="table-container">
                <table class="table table-hover table-bordered" id="tableSearch" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Partai H</th>
                            <th class="dhead">No Box</th>
                            <th class="dhead">Tipe</th>
                            <th class="dhead">Grade</th>
                            <th class="dhead">Pcs sdh cabut</th>
                            <th class="dhead">Gr sdh cabut</th>
                            <th class="dhead">Ttl Rp</th>
                            <th class="dhead">Cost Cabut</th>
                            <th class="dhead">Pcs timbang ulang</th>
                            <th class="dhead">Gr timbang ulang</th>
                            <th class="dhead">Selesai</th>
                            <th class="dhead">
                                <button type="submit" name="submit" value="export" class="badge bg-success"><i
                                        class="fas fa-file-excel"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNumber = 1;
                        @endphp
                        @foreach ($cabut as $no => $c)
                            @php
                                $bk = \App\Models\GudangBkModel::getPartaicetak($c->nm_partai);
                                $gdng_ctk = DB::table('gudang_ctk')
                                    ->where('no_box', $c->no_box)
                                    ->where('selesai', 'selesai')
                                    ->first();

                                if (empty($gdng_ctk->selesai)) {
                                } else {
                                    continue;
                                }
                            @endphp
                            <tr>
                                <td>{{ $rowNumber }}</td>
                                <td>{{ $c->nm_partai }}</td>
                                <td>{{ $c->no_box }}</td>
                                <td>{{ $c->tipe }}</td>
                                <td>{{ $bk->nm_grade }}</td>
                                <td align="right">{{ $c->pcs_akhir }}</td>
                                <td align="right">{{ $c->gr_akhir }}</td>
                                <td align="right">{{ number_format(($bk->total_rp / $bk->gr) * $c->gr_akhir, 0) }}</td>
                                <td align="right">{{ number_format($c->ttl_rp) }}</td>
                                <td align="right">{{ number_format($gdng_ctk->pcs_timbang_ulang ?? 0) }}</td>
                                <td align="right">{{ number_format($gdng_ctk->gr_timbang_ulang ?? 0) }}</td>
                                <td align="right">proses</td>
                                <td></td>
                            </tr>
                            @php
                                $rowNumber++;
                            @endphp
                        @endforeach
                        @foreach ($wip_cetak as $c)
                            <tr>
                                <td>{{ $rowNumber }}</td>
                                <td>{{ $c->partai_h }}</td>
                                <td>{{ $c->no_box }}</td>
                                <td>{{ $c->tipe }}</td>
                                <td>{{ $c->grade }}</td>
                                <td align="right">{{ $c->pcs_cabut }}</td>
                                <td align="right">{{ $c->gr_cabut }}</td>
                                <td align="right">{{ number_format($c->ttl_rp, 0) }}
                                </td>
                                <td align="right">{{ number_format($c->cost_cabut) }}</td>
                                <td align="right">{{ $c->pcs_timbang_ulang }}</td>
                                <td align="right">{{ $c->gr_timbang_ulang }}</td>
                                <td align="right">{{ $c->selesai }}</td>
                                <td></td>
                            </tr>
                            @php
                                $rowNumber++;
                            @endphp
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>