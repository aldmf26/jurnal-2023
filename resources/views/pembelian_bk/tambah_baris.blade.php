<tr class="baris{{ $count }}">
    <td style="vertical-align: top;">
        {{-- <button type="button" data-bs-toggle="collapse" href=".join1" class="btn rounded-pill " count="1"><i
                class="fas fa-angle-down"></i>
        </button> --}}
    </td>
    <td>
        <select name="id_produk[]" id="" class="select pilih_produk pilih_produk{{ $count }}"
            count='{{ $count }}' required>
            <option value="">Pilih Produk</option>
            @foreach ($produk as $p)
                <option value="{{ $p->id_produk }}">{{ $p->nm_produk }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control" name="ket[]">
    </td>

    <td style="vertical-align: top;">
        <input type="text" class="form-control qty qty{{ $count }}" count='{{ $count }}'
            style="vertical-align: top;width: 80px;" value="0">
        <input type="hidden" name="qty[]" class="form-control qty_biasa qty_biasa{{ $count }}"
            style="vertical-align: top;" value="0">

    </td>
    <td style="vertical-align: top;">
        <select name="id_satuan[]" id="" class="select satuan{{ $count }}">

        </select>

    </td>
    <td style="vertical-align: top;" align="right">
        <input type="text" class="form-control h_satuan h_satuan{{ $count }} text-end" value="Rp 0"
            count="{{ $count }}" style="width: 150px;">
        <input type="hidden" class="form-control h_satuan_biasa h_satuan_biasa{{ $count }}" value="0"
            name="h_satuan[]">
    </td>
    <td style="vertical-align: top;" align="right">
        <input type="text" class="form-control total_harga{{ $count }} text-end" value=""
            count="{{ $count }}" readonly style="width: 150px;">
        <input type="hidden" class="form-control total_harga_biasa total_harga_biasa{{ $count }} text-end"
            value="" readonly>
    </td>
    <td style="vertical-align: top;">
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
