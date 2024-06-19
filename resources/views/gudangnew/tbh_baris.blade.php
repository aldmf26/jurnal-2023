<div class="row baris{{ $count }}">
    <div class="col-lg-12">
        <hr>
    </div>
    {{-- <div class="col-lg-2">
        <label for="">Suplier Awal</label>
        <input type="text" class="form-control" name="suplier_awal[]">
    </div> --}}
    <div class="col-lg-2">
        <label for="">Date</label>
        <input type="date" class="form-control" name="tgl[]">
    </div>
    <div class="col-lg-1">
        <label for="">Grade</label>
        <input type="text" class="form-control" name="grade[]">
    </div>
    <div class="col-lg-2">
        <label for="">Pcs</label>
        <input type="text" class="form-control" name="pcs[]">
    </div>
    <div class="col-lg-2">
        <label for="">Gr</label>
        <input type="text" class="form-control" name="gr[]">
    </div>
    <div class="col-lg-2">
        <label for="">Rp/Gr</label>
        <input type="text" class="form-control" name="rp_gram[]">
    </div>
    <div class="col-lg-1">
        <label for="">Lot</label>
        <input type="text" class="form-control" name="lot[]">
    </div>
    <div class="col-lg-2 mt-2">
        <label for="">Nama Partai Herry
        </label>
        <input type="text" class="form-control" name="ket1[]">
    </div>
    <div class="col-lg-2 mt-2">
        <label for="">Nama Partai Sinta</label>
        <input type="text" class="form-control" name="ket2[]">
    </div>
    <div class="col-lg-1 mt-2">
        <label for="">Aksi</label> <br>
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </div>

</div>
