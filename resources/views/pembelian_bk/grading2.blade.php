<div class="row">
    <div class="col-lg-12 border border-dark">
        <table style="font-size: small; white-space: nowrap;  " width="100%">
            <tr>
                <td rowspan="3" align="left" width="80%"><img src="/assets/login/img/empat.svg" width="100"
                        alt="">
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Tanggal</td>
                <td>&nbsp;</td>
                <td>:</td>
                <td>&nbsp;</td>
                <td style="text-align: center;">
                    <?= date('d-F-Y', strtotime($pembelian->tgl)) ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>No Faktur</td>
                <td>&nbsp;</td>
                <td>:</td>
                <td>&nbsp;</td>
                <td style="text-transform: uppercase;">
                    <?= $pembelian->no_nota ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Kpd Yth, Bpk/Ibu</td>
                <td>&nbsp;</td>
                <td>:</td>
                <td>&nbsp;</td>
                <td>
                    <?= strtoupper($pembelian->suplier_akhir) ?>
                </td>
            </tr>
        </table>
        <br>
        <table class="table-bordered table table-xs" width="100%" border="1">
            <thead>
                <tr>
                    <td class="text-center fw-bold">Produk</td>
                    <td class="text-end fw-bold">Qty</td>
                    <td class="fw-bold text-center">Satuan</td>
                    <td class="text-end fw-bold"> Harga</td>
                    <td class="text-end fw-bold">Total</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty_total = 0;
                @endphp
                @foreach ($produk as $no => $p)
                    @php
                        $qty_total += $p->qty;
                    @endphp
                    <tr>
                        <td class="text-center ">{{ $p->nm_produk }}</td>
                        <td align="right">{{ number_format($p->qty, 0) }}</td>
                        <td class="text-center">{{ $p->nm_satuan }}</td>
                        <td align="right">{{ number_format($p->h_satuan, 0) }}</td>
                        <td align="right">
                            {{ $p->id_produk == '7' ? $p->h_satuan : number_format($p->qty == '0' ? '0' : $p->qty * $p->h_satuan, 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="fw-bold" style="text-align: center;">Total</td>
                    <td class="fw-bold" style="text-align: right;">
                        <?= number_format($qty_total, 0) ?>
                    </td>
                    <td></td>
                    <td class="fw-bold" style="text-align: right;">
                        <?= number_format($pembelian->total_harga == '0' ? '0' : $pembelian->total_harga / $qty_total, 0) ?>
                        </th>
                    <td class="fw-bold" style="text-align: right;">
                        <?= number_format($pembelian->total_harga, 0) ?>
                        </th>
                </tr>


            </tfoot>
        </table>
    </div>

    <div class="col-lg-12">
        <hr>
    </div>
    @if (empty($grading))
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input type="date" class="form-control" name="tgl" required>
        </div>
        <div class="col-lg-3">
            <label for="">No Campur</label>
            <input type="text" class="form-control" name="no_campur">
            <input type="hidden" class="form-control nota_grading" name="no_nota" required>
            <input type="hidden" class="form-control" name="no_lot" required value="{{ $invoice->no_lot }}">
        </div>
        <div class="col-lg-2">
            <label for="">Gram Basah</label>
            <input type="text" class="form-control text-end" name="gr_basah" value="0" required>
        </div>
        <div class="col-lg-2">
            <label for="">Pcs Awal</label>
            <input type="text" class="form-control text-end" name="pcs_awal" value="0" required>
        </div>
        <div class="col-lg-2">
            <label for="">Gr Kering</label>
            <input type="text" class="form-control text-end" name="gr_kering" value="0" required>
        </div>
    @else
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input type="date" class="form-control" name="tgl" value="{{ $grading->tgl }}">
        </div>
        <div class="col-lg-3">
            <label for="">No Campur</label>
            <input type="text" class="form-control" name="no_campur" value="{{ $grading->no_campur }}"
                {{ empty($grading->no_campur) ? '' : '' }}>
            <input type="hidden" class="form-control nota_grading" name="no_nota" value="{{ $grading->no_nota }}">
            <input type="hidden" class="form-control" name="no_lot" required value="{{ $invoice->no_lot }}">
        </div>
        <div class="col-lg-2">
            <label for="">Gram Basah</label>
            <input type="text" class="form-control" name="gr_basah" value="{{ $grading->gr_basah }}"
                {{ $grading->gr_basah == '0' ? '' : '' }}>
        </div>
        <div class="col-lg-2">
            <label for="">Pcs Awal</label>
            <input type="text" class="form-control" name="pcs_awal" value="{{ $grading->pcs_awal }}"
                {{ $grading->pcs_awal == '0' ? '' : '' }}>
        </div>
        <div class="col-lg-2">
            <label for="">Gr Kering</label>
            <input type="text" class="form-control" name="gr_kering" value="{{ $grading->gr_kering }}"
                {{ $grading->gr_kering == '0' ? '' : '' }}>
        </div>
    @endif

</div>
<div class="row">
    <div class="col-lg-6">
        <hr>
        <form id="save_grade">
            <div class="row" x-data="{
                open1: false,
            }">
                <div class="col-lg-12">
                    <button type="button" class="btn btn-primary btn-sm btn-buka mb-4"
                        @click="open1 = ! open1">Tambah
                        Grade</button>
                </div>


                <div class="col-lg-8" x-show="open1">
                    <input type="text" class="form-control" name="nm_grade" placeholder="Nama Grade">
                    <input type="hidden" class="form-control" name="no_nota" value="{{ $invoice->no_nota }}">
                </div>
                <div class="col-lg-4" x-show="open1">
                    <button type="button" class="btn btn-primary btn-save">Save</button>
                </div>
                <br>
                <br>
                <br>
            </div>
        </form>
        <div id="load_grade"></div>
    </div>
</div>
