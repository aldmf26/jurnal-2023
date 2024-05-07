<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <style>
            /* width */
            ::-webkit-scrollbar {
                width: 10px;
            }

            /* Track */
            ::-webkit-scrollbar-track {
                box-shadow: inset 0 0 5px grey;
                border-radius: 10px;
            }

            /* Handle */
            ::-webkit-scrollbar-thumb {
                background: #257687;
                border-radius: 2px;
            }
        </style>
        <div class="d-flex justify-content-between">
            <h6 class="float-start mt-1">{{ $title }} </h6>
            <div class="ms-auto">
                <x-theme.button modal="Y" idModal="suntikan" icon="fa-plus" teks="Suntikan" />
                <a href="{{ route('gudang_grading.export') }}" class="btn btn-sm btn-success me-2"><i
                        class="fas fa-file-excel"></i> Export</a>
            </div>
        </div>
        @include('gudang_grading.nav')
    </x-slot>
    <x-slot name="cardBody">

        <section class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Siap Grading Awal</h6>
                </div>
                <input type="text" class="mt-3 form-control form-control-sm mb-2" placeholder="pencarian"
                    id="pencarian">
                <div style="overflow-x: hidden; height: 400px">
                    <table id="tableSearch" class="table table-hover table-bordered">
                        
                        <thead>
                            <tr>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Ttl Rp</th>
                                <th class="dhead text-end">Cost Cbt</th>
                                <th class="dhead text-end">Cost Ctk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlPcs = 0;
                                $ttlGr = 0;
                                $ttlRp = 0;
                                $ttlCostCbt = 0;
                                $ttlCostCtk = 0;
                            @endphp
                            @foreach ($datas->cetak as $i => $d)
                            @php
                                $ttlPcs += $d->pcs_akhir;
                                $ttlGr += $d->gr_akhir;
                                $ttlRp += $d->total_rp;
                                $ttlCostCbt += $d->cost_cabut;
                                $ttlCostCtk += $d->cost_cetak;
                            @endphp
                                <tr>
                                    <td>{{ $d->tipe }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ number_format($d->pcs_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->total_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cabut, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cetak, 0) }}</td>
                                </tr>
                            @endforeach
                            @foreach ($datas->cabut_selesai as $i => $d)
                            @php
                                $ttlPcs += $d->pcs_akhir;
                                $ttlGr += $d->gr_akhir;
                                $ttlRp += $d->total_rp;
                                $ttlCostCbt += $d->cost_cabut;
                                $ttlCostCtk += $d->cost_cetak;
                            @endphp
                                <tr>
                                    <td>{{ $d->tipe }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ number_format($d->pcs_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->total_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cabut, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cetak, 0) }}</td>
                                </tr>
                            @endforeach
                            @foreach ($datas->suntikan as $i => $d)
                            @php
                                $pcs_akhir = $d->pcs_akhir ?? 0;
                                $gr_akhir = $d->gr_akhir ?? 0;
                                $total_rp = $d->total_rp ?? 0;
                                $cost_cabut = $d->cost_cabut ?? 0;
                                $cost_cetak = $d->cost_cetak ?? 0;

                                $ttlPcs += $pcs_akhir;
                                $ttlGr += $gr_akhir;
                                $ttlRp += $total_rp;
                                $ttlCostCbt += $cost_cabut;
                                $ttlCostCtk += $cost_cetak;
                            @endphp
                                <tr>
                                    <td>{{ $d->tipe }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ number_format($pcs_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($total_rp, 0) }}</td>
                                    <td align="right">{{ number_format($cost_cabut, 0) }}</td>
                                    <td align="right">{{ number_format($cost_cetak, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="dhead"></th>
                                <th class="dhead text-end">{{ number_format($ttlPcs,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlGr,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlRp,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlCostCbt,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlCostCtk,0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Selesai Grading Awal</h6>
                </div>
                <input type="text" class="mt-3 form-control form-control-sm mb-2" placeholder="pencarian"
                    id="pencarian2">
                <div style="overflow-x: hidden; height: 400px">
                    <table id="tableSearch2" class="table table-hover table-bordered">
                        <thead>

                            <tr>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Ttl Rp</th>
                                <th class="dhead text-end">Cost Cbt</th>
                                <th class="dhead text-end">Cost Ctk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlPcs = 0;
                                $ttlGr = 0;
                                $ttlRp = 0;
                                $ttlCostCbt = 0;
                                $ttlCostCtk = 0;
                            @endphp
                            @foreach ($datas->grading_selesai as $i => $d)
                            @php
                                $ttlPcs += $d->pcs_awal;
                                $ttlGr += $d->gr_awal;
                                $ttlRp += $d->ttl_rp;
                                $ttlCostCbt += $d->cost_cabut;
                                $ttlCostCtk += $d->cost_cetak;
                            @endphp
                                <tr>
                                    <td>{{ $d->tipe }}</td>
                                    <td>{{ $d->no_box }}</td>
                                    <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cabut, 0) }}</td>
                                    <td align="right">{{ number_format($d->cost_cetak, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="dhead"></th>
                                <th class="dhead text-end">{{ number_format($ttlPcs,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlGr,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlRp,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlCostCbt,0) }}</th>
                                <th class="dhead text-end">{{ number_format($ttlCostCtk,0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <form action="{{ route('gudang_grading.create_suntikan') }}" method="post">
            @csrf
        <x-theme.modal size="modal-lg-max" idModal="suntikan" title="Tambah Suntikan">
            <x-theme.multiple-input>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="">Tipe</label>
                        <input type="text" name="tipe[]" class="form-control">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="">No Box</label>
                        <input type="text" name="no_box[]" class="form-control">
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="">Pcs</label>
                        <input type="text" name="pcs[]" class="form-control text-end">
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="">Gr</label>
                        <input type="text" name="gr[]" class="form-control text-end">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="">Ttl rp</label>
                        <input type="text" name="ttl_rp[]" class="form-control text-end">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="">Cost Cbt</label>
                        <input type="text" name="cost_cbt[]" class="form-control text-end">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="">Cost Ctk</label>
                        <input type="text" name="cost_ctk[]" class="form-control text-end">
                    </div>
                </div>
            </x-theme.multiple-input>
        </x-theme.modal>
    </form>
    </x-slot>

    @section('scripts')
        <script>
            $(document).ready(function() {
                pencarian('pencarian', 'tableSearch')
                pencarian('pencarian2', 'tableSearch2')
            });
        </script>
    @endsection
</x-theme.app>
