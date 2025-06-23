<div class="row">
    <div class="col-lg-4 col-12">
        <label for="">Kategori</label>
        <input type="hidden" name="id" value="{{ $id }}">
        <select name="kategori" class="form-control" id="">
            <option value="">-Pilih Katgeori-</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id }}" @selected($grade->kategori_id == $k->id)>{{ $k->nm_kategori }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4 col-12">
        <label for="">Grade</label>

        <textarea name="nm_grade" id="" cols="8" rows="2" class="form-control">
            {{ $grade->nm_grade }}
                            </textarea>
    </div>
    <div class="col-lg-4 col-12">
        <label for="">Urutan</label>
        <input type="text" class="form-control" name="urutan" value="{{ $grade->urutan }}">
    </div>
    <div class="col-lg-4 col-12 mt-2">
        <label for="">Aktif</label>
        <select name="aktif" class="form-control" id="">
            <option value="Y" @selected($grade->aktif == 'Y')>Aktif</option>
            <option value="T" @selected($grade->aktif == 'T')>Tidak Aktif</option>
        </select>
    </div>

    <div class="col-lg-4 col-12 mt-2">
        <label for="">Putih beras</label>
        <select name="putih" class="form-control" id="">
            <option value="Y" @selected($grade->putih == 'Y')>Aktif</option>
            <option value="T" @selected($grade->putih == 'T')>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-lg-4 col-12 mt-2">
        <label for="">Kuning</label>
        <select name="kuning" class="form-control" id="">
            <option value="Y" @selected($grade->kuning == 'Y')>Aktif</option>
            <option value="T" @selected($grade->kuning == 'T')>Tidak Aktif</option>
        </select>
    </div>



</div>
