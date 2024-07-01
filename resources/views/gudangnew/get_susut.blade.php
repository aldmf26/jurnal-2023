<div class="row">
    <input type="hidden" name="rp_satuan" value="{{ $rp_satuan }}">

    @if (empty($get_partai->ket))
        <div class="col-lg-4">
            <label for="">Partai</label>
            <input type="text" class="form-control partai" name="partai_kosong" readonly value="{{ $partai }}">
        </div>
        <div class="col-lg-4">
            <label for="">Pcs Susut</label>
            <input type="text" class="form-control pcs_susut" name="pcs_susut" value="0">
        </div>
        <div class="col-lg-4">
            <label for="">Gr Susut</label>
            <input type="text" class="form-control gr_susut" name="gr_susut" value="0">
        </div>
    @else
        <div class="col-lg-4">
            <label for="">Partai</label>
            <input type="text" class="form-control partai" name="partai" readonly value="{{ $get_partai->ket }}">
        </div>
        <div class="col-lg-4">
            <label for="">Pcs Susut</label>
            <input type="text" class="form-control pcs_susut" name="pcs_susut" value="{{ $get_partai->pcs }}">
        </div>
        <div class="col-lg-4">
            <label for="">Gr Susut</label>
            <input type="text" class="form-control gr_susut" name="gr_susut" value="{{ $get_partai->gr }}">
        </div>
    @endif

</div>
