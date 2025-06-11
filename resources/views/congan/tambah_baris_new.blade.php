<div class="row baris{{ $count }}">
    <div class="col-lg-2 col-4">
        <label for="">Tanggal</label>
        <input type="date" class="form-control" name="tgl" required>
    </div>
    <div class="col-lg-2 col-4">
        <label for="">Pemilik</label>
        <input type="text" class="form-control" name="pemilik" required>
    </div>
    <div class="col-lg-2 col-4">
        <label for="">Keterangan</label>
        <input type="text" class="form-control" name="ket[]" required>
    </div>
    <div class="col-lg-2 col-4">
        <label for="">Persen Air</label>
        <input type="text" class="form-control" value="0" name="persen_air[]" required>
    </div>
</div>
<div class="row mt-4  baris{{ $count }}">
    <style>
        @media only screen and (max-width: 767px) {

            .label_hilang {
                display: none
            }
        }
    </style>
    <table class="table table-bordered">
        <thead>
            <tr>

                <th class="dhead">Grade</th>
                <th class="dhead text-end" width="15%">Putih Gr</th>
                <th class="dhead text-end" width="15%">Kuning Gr</th>
                {{-- <th class="dhead text-end" width="15%">Harga</th> --}}
                {{-- <th class="dhead text-end">Putih Comp</th>
                <th class="dhead text-end">Kuning Comp</th> --}}
            </tr>
        </thead>
        <tbody>

            @foreach ($grade as $key => $g)
                <tr>

                    <input type="hidden" name="id_grade{{ $count }}[]" value="{{ $g->id_grade_cong }}">
                    <td>{{ $g->nm_grade }}</td>
                    <td class="text-end">
                        <input type="text" class="form-control inputan gr gr{{ $count }}"
                            count="{{ $count }}" value="0" name="gr{{ $count }}[]">
                    </td>
                    <td class="text-end">
                        <input type="text" class="form-control inputan gr_kuning gr_kuning{{ $count }}"
                            count="{{ $count }}" value="0" name="gr{{ $count }}[]">
                    </td>
                    {{-- <td class="text-end">
                        <input type="text" class="form-control inputan" value="0"
                            name="harga{{ $count }}[]" readonly>
                    </td> --}}
                    {{-- <td class="text-end">
                        0
                    </td>
                    <td class="text-end">
                        0
                    </td> --}}

                </tr>
            @endforeach
        </tbody>

    </table>
</div>
<br>
<br>
<div class="row  baris{{ $count }}">
    <div class="col-lg-2">
        <table style="padding: 10px">
            <tr>
                <td>
                    <h6>Total Gram Putih &nbsp;</h6>
                </td>
                <td><input type="text" class="form-control total_gram{{ $count }}" readonly value="0">
                </td>

            </tr>
            <tr>
                <td>
                    <h6>Total Gram Kuning &nbsp;</h6>
                </td>
                <td><input type="text" class="form-control total_gram_kuning{{ $count }}" readonly
                        value="0">
                </td>

            </tr>
            <tr>
                <td>
                    <h6>Harga Beli &nbsp;</h6>
                </td>
                <td><input type="text" class="form-control hrga_beli" name="hrga_beli[]" value="0"></td>
            </tr>
            <tr>
                <td>
                    <h6>Harga(100%) &nbsp;</h6>
                </td>
                <td><input type="text" class="form-control" readonly value="0"></td>
            </tr>
            <tr>
                <td>
                    <h6>Harga(%) &nbsp;</h6>
                </td>
                <td><input type="text" class="form-control" readonly></td>
                <input type="hidden" name="count[]" value="{{ $count }}">
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center"><button type="button"
                        class="btn btn-sm btn-danger  remove_baris mb-4" count="{{ $count }}"><i
                            class="fas fa-minus"></i> Hapus Baris</button></td>
            </tr>
        </table>

    </div>
    <div class="col-lg-12">
        <hr style="border: 1px solid">
    </div>
</div>
