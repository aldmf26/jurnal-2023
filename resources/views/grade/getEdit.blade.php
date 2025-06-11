<div class="row">
    <div class="col-lg-4 ">
        <label for="">Kategori</label>
    </div>
    <div class="col-lg-4">
        <label for="">Grade</label>
    </div>
    <div class="col-lg-3">
        <label for="">Urutan</label>
    </div>
    <div class="col-lg-4 col-4 mb-2">
        <input type="hidden" name="id" value="{{ $id }}">
        <select name="kategori" class="form-control" id="">
            <option value="">-Pilih Katgeori-</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id }}" @selected($grade->kategori_id == $k->id)>{{ $k->nm_kategori }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4 col-4 mb-2">

        <input type="text" class="form-control" name="nm_grade" value="{{ $grade->nm_grade }}">
    </div>
    <div class="col-lg-3 col-3 mb-2">

        <input type="text" class="form-control" name="urutan" value="{{ $grade->urutan }}">
    </div>


</div>
