<table class="table table2 table-bordered">
    <thead>
        <tr>
            <th width="150px" class="dhead">Grade</th>
            <th class="text-end dhead">Pcs</th>
            <th class="text-end dhead">Gr</th>
            {{-- <th class="text-end dhead">Rupiah</th> --}}
            <td class="dhead">Aksi</td>
        </tr>
    </thead>
    <tbody style="background-color: #F2F7FF">
        @foreach ($grade as $no => $g)
            @php
                $bk_campur = DB::selectOne("SELECT a.id_buku_campur, b.nm_grade, a.pcs, a.gr, a.rupiah
                FROM buku_campur as a
                left join grade as b on b.id_grade = a.id_grade
                where a.id_grade = '$g->id_grade' and a.no_nota = '$no_nota'
                ");
            @endphp
            <tr>
                <td>
                    {{ $g->nm_grade }}
                    <input type="hidden" name="id_grade[]" value="{{ $g->id_grade }}">
                    <input type="hidden" name="id_buku_campur[]" value="{{ $bk_campur->id_buku_campur ?? 0 }}">
                    <input type="hidden" name="rupiah[]" value="{{ $bk_campur->rupiah ?? 0 }}">
                </td>
                <td><input type="text" count="{{ $no + 1 }}" class="form-control pcsAwal text-end "
                        name="pcs[]" value="{{ empty($bk_campur->pcs) ? 0 : $bk_campur->pcs }}"
                        {{ $invoice->approve_bk_campur == 'Y' ? 'readonly' : '' }}></td>

                <td><input type="text" count="{{ $no + 1 }}" class="form-control grAwal text-end "
                        name="gr[]" value="{{ empty($bk_campur->gr) ? 0 : $bk_campur->gr }}"
                        {{ $invoice->approve_bk_campur == 'Y' ? 'readonly' : '' }}></td>
                {{-- <td>
                    <input type="text" class="form-control text-end" name="rupiah[]"
                        x-mask:dynamic="$money($input)"
                        value="{{ empty($bk_campur->rupiah) ? 0 : $bk_campur->rupiah }}">
                </td> --}}
                <td>
                    <button type="button" class="btn btn-sm btn-danger hapus_grade" no_nota="{{ $no_nota }}"
                        id_grade="{{ $g->id_grade }}"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        @endforeach

    </tbody>

</table>
