<div class="row baris{{ $count }}">
    <div class="col-lg-4 col-4 mt-2">

        <select name="kategori[]" class="form-control" id="">
            <option value="">-Pilih Katgeori-</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id }}">{{ $k->nm_kategori }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4 col-4 mt-2">

        <input type="text" class="form-control" name="nm_grade[]">
    </div>
    <div class="col-lg-3 col-3 mt-2">

        <input type="text" class="form-control" name="urutan[]">
    </div>
    <div class="col-lg-1 mt-2">

        <button type="button" class="btn btn-sm btn-danger  remove_baris mb-4" count="{{ $count }}"><i
                class="fas fa-minus"></i> </button>
    </div>

</div>
