<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-2">

            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('edit_pembelian_bk') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-2 col-6">
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control" name="tgl"
                        value="{{ date('Y-m-d', strtotime($invoice->tgl)) }}">
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Nota</label>
                    <input type="text" class="form-control" name="no_nota" value="{{ $nota }}" readonly>

                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Lot</label>
                    <input type="text" class="form-control nota_bk" name="no_lot" value="{{ $invoice->no_lot }}">
                    <input type="hidden" class="form-control nota_bk" name="approve_bk_campur"
                        value="{{ $invoice->approve_bk_campur }}">
                </div>
                {{-- <div class="col-lg-2 col-6">
                    <label for="">Suplier Awal</label>
                    <select name="suplier_awal" id="select2" class="">
                        <option value="">Pilih Suplier</option>
                        @foreach ($suplier as $s)
                            <option value="{{ $s->id_suplier }}"
                                {{ $invoice->id_suplier == $s->id_suplier ? 'Selected' : '' }}>{{ $s->nm_suplier }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-lg-2 col-6">
                    <label for="">Suplier Akhir</label>
                    <input type="text" class="form-control" name="suplier_akhir"
                        value="{{ $invoice->suplier_akhir }}">
                </div>

                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="15%">Produk</th>
                                <th width="15%">Keterangan</th>
                                <th width="7%">Qty</th>
                                <th width="10%">Satuan</th>
                                <th width="12%" style="text-align: right;white-space: nowrap;">Harga Satuan</th>
                                <th width="12%" style="text-align: right;white-space: nowrap;">Total Harga</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_all = 0;
                            @endphp
                            @foreach ($gram as $no => $g)
                                @php
                                    $total_all += $g->id_produk == '7' ? $g->h_satuan : $g->qty * $g->h_satuan;
                                @endphp
                                <input type="hidden" class="form-control" name="urutan_nota"
                                    value="{{ $g->urutan_nota }}" readonly>
                                <tr class="baris{{ $no + 1 }}">
                                    <td style="vertical-align: top;">
                                        {{-- <button type="button" data-bs-toggle="collapse" href=".join1"
                                        class="btn rounded-pill " count="1"><i class="fas fa-angle-down"></i>
                                    </button> --}}
                                    </td>
                                    <td>
                                        <select name="id_produk[]" id=""
                                            class="select2_add pilih_produk pilih_produk{{ $no + 1 }}"
                                            count='{{ $no + 1 }}'>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produk as $p)
                                                <option value="{{ $p->id_produk }}"
                                                    {{ $g->id_produk == $p->id_produk ? 'Selected' : '' }}>
                                                    {{ $p->nm_produk }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="ket[]"
                                            value="{{ $g->ket }}">
                                    </td>

                                    <td style="vertical-align: top;">
                                        <input type="text" class="form-control qty qty{{ $no + 1 }}"
                                            id_produk="{{ $g->id_produk }}" count='{{ $no + 1 }}'
                                            style="vertical-align: top"
                                            value="{{ number_format($g->qty, 0, ',', '.') }}">
                                        <input type="hidden" name="qty[]"
                                            class="form-control qty_biasa qty_biasa{{ $no + 1 }}" count='1'
                                            style="vertical-align: top" value="{{ $g->qty }}">

                                    </td>
                                    <td style="vertical-align: top;">
                                        @php
                                            $produk2 = DB::table('tb_produk')
                                                ->where('id_produk', $g->id_produk)
                                                ->first();
                                            $satuan = DB::table('tb_satuan')
                                                ->where('id_satuan', $produk2->satuan_id)
                                                ->get();
                                        @endphp
                                        <select name="id_satuan[]" id=""
                                            class="select2_add satuan{{ $no + 1 }}" style="width: 120px">
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id_satuan }}">{{ $s->nm_satuan }}</option>
                                            @endforeach
                                        </select>

                                    </td>
                                    <td style="vertical-align: top;">
                                        <input type="text"
                                            class="form-control h_satuan h_satuan{{ $no + 1 }} text-end"
                                            id_produk="{{ $g->id_produk }}"
                                            value="Rp {{ number_format($g->h_satuan, 0, ',', '.') }}"
                                            count="{{ $no + 1 }}">
                                        <input type="hidden"
                                            class="form-control h_satuan_biasa h_satuan_biasa{{ $no + 1 }}"
                                            value="{{ $g->h_satuan }}" name="h_satuan[]">
                                    </td>
                                    <td style="vertical-align: top;">
                                        <input type="text"
                                            class="form-control total_harga{{ $no + 1 }} text-end"
                                            value="Rp. {{ $g->id_produk == '7' ? number_format($g->h_satuan, 2, ',', '.') : number_format($g->qty * $g->h_satuan, 2, ',', '.') }}"
                                            count="{{ $no + 1 }}" readonly>
                                        <input type="hidden"
                                            class="form-control total_harga_biasa total_harga_biasa{{ $no + 1 }} text-end"
                                            value="{{ $g->id_produk == '7' ? $g->h_satuan : $g->qty * $g->h_satuan }}"
                                            readonly>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <button type="button" class="btn rounded-pill remove_baris"
                                            count="{{ $no + 1 }}"><i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                        <tbody id="tb_baris">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris"
                                        style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Baris Baru

                                    </button>
                                </th>
                            </tr>
                        </tfoot>


                    </table>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-1">

                            <div class="form-check form-switch form-switch2">
                                <input class="form-check-input form-check-input2"
                                    {{ empty($invoice2) ? '' : 'checked' }} value="Y" type="checkbox"
                                    id="Pilihan_Lainnya" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="Biaya lain-lain">Biaya lain-lain</label>
                        </div>
                        <div class="col-lg-12"></div>
                        @if (empty($invoice2))
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Akun</label>
                                <select name="id_akun_lainnya" id="" class="select2_add inp-lain">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($akun as $a)
                                        <option value="{{ $a->id_akun }}">{{ $a->nm_akun }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Keterangan</label>
                                <input type="text" class="form-control inp-lain" name="ket_lainnya">
                            </div>
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Rupiah</label>
                                <input type="text" class="form-control inp-lain debit-lain">
                                <input type="hidden" class="form-control inp-lain debit-lain_biasa"
                                    name="debit_tambahan">
                            </div>
                        @else
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Akun</label>
                                <select name="id_akun_lainnya" id="" class="select2_add inp-lain">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($akun as $a)
                                        <option value="{{ $a->id_akun }}"
                                            {{ $invoice2->id_akun == $a->id_akun ? 'Selected' : '' }}>
                                            {{ $a->nm_akun }}</option>
                                    @endforeach

                                </select>
                                <input type="hidden" class="id_cek" value="{{ empty($invoice2) ? '1' : '2' }}">
                            </div>
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Keterangan</label>
                                <input type="text" class="form-control inp-lain" name="ket_lainnya"
                                    value="{{ $invoice2->ket }}">
                            </div>
                            <div class="col-lg-4 pilihan_l">
                                <label for="">Rupiah</label>
                                <input type="text" class="form-control inp-lain debit-lain"
                                    value="Rp {{ number_format($invoice2->debit, 0, ',', '.') }}">
                                <input type="hidden" class="form-control inp-lain debit-lain_biasa"
                                    name="debit_tambahan" value="{{ $invoice2->debit }}">
                            </div>
                        @endif


                    </div>
                </div>
                <div class="col-lg-6">

                    <hr style="border: 1px solid blue">
                    <table class="" width="100%">
                        <tr>
                            <td width="20%" style="font-size: 16px">Total</td>
                            <td width="40%" class="total" style="text-align: right;font-size: 16px">Rp.
                                {{ number_format($total_all, 0, ',', '.') }}</td>
                            <input type="hidden" class="total_biasa" name="total_harga"
                                value="{{ $total_all }}">
                        </tr>
                    </table>
                    {{--
                    <hr style="border: 1px solid blue">
                    <table class="" width="100%">
                        <tr>
                            <td width="50%">Pembayaran</td>
                            <td width="50%" style="text-align: right;">
                                <select name="" id="" class="select2_add">
                                    <option value="">Cash</option>
                                    <option value="">Hutang</option>
                                </select>
                            </td>
                        </tr>
                    </table> --}}

                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <div class="btn-group dropup me-1 mb-1 float-end">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Simpan
            </button>
            <div class="dropdown-menu">
                <button type="submit" name="button" value="simpan" class="dropdown-item"
                    href="#">Simpan</button>
                <button type="submit" name="button" value="draft" class="dropdown-item"
                    href="#">Draft</button>
            </div>
        </div>
        {{-- <button type="submit" class="float-end btn btn-primary button-save">Simpan</button> --}}
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('jurnal') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
        <script>
            $(document).ready(function() {
                $(document).on("change", ".pilih_produk", function() {
                    var count = $(this).attr("count");
                    var id_produk = $(".pilih_produk" + count).val();
                    $.ajax({
                        url: "/get_satuan_produk?id_produk=" + id_produk,
                        type: "Get",

                        success: function(data) {
                            $(".satuan" + count).html(data);
                            $('.qty' + count).attr('id_produk', +id_produk);
                            $('.h_satuan' + count).attr('id_produk', +id_produk);
                        },
                    });
                });
                $(document).on("keyup", ".qty", function() {
                    var count = $(this).attr("count");
                    var id_produk = $(this).attr('id_produk');
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.qty_biasa' + count).val(0)
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.qty_biasa' + count).val(input2)

                    }




                    var h_satuan = $(".h_satuan_biasa" + count).val()
                    if (id_produk == '7') {
                        var total = parseFloat(h_satuan);
                    } else {
                        var total = parseFloat(input2) * parseFloat(h_satuan);
                    }
                    var totalRupiah = total.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });

                    $('.total_harga_biasa' + count).val(total);
                    var debit = $(".total_harga" + count).val(totalRupiah);

                    var total_all = 0;
                    $(".total_harga_biasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });

                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    var tl = $(".total_biasa").val(total_all);
                    var debit = $(".total").text(totalRupiahall);


                });
                $(document).on("keyup", ".h_satuan", function() {
                    var count = $(this).attr("count");

                    var id_produk = $(this).attr('id_produk');
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.h_satuan_biasa' + count).val(0)
                    } else {
                        $(this).val("Rp " + input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.h_satuan_biasa' + count).val(input2)

                    }
                    var qty_biasa = $(".qty_biasa" + count).val()
                    if (id_produk == '7') {
                        var total = parseFloat(input2);
                    } else {
                        var total = parseFloat(input2) * parseFloat(qty_biasa);
                    }

                    var totalRupiah = total.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    $('.total_harga_biasa' + count).val(total);
                    var debit = $(".total_harga" + count).val(totalRupiah);

                    var total_all = 0;
                    $(".total_harga_biasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });

                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    var tl = $(".total_biasa").val(total_all);
                    var debit = $(".total").text(totalRupiahall);
                });


                var count = 99;
                $(document).on("click", ".tbh_baris", function() {
                    count = count + 1;
                    $.ajax({
                        url: "/tambah_baris_bk?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $("#tb_baris").append(data);
                            $(".select").select2();
                        },
                    });
                });

                $(document).on("click", ".remove_baris", function() {
                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();
                    var total_all = 0;
                    $(".total_harga_biasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });

                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    });
                    var tl = $(".total_biasa").val(total_all);
                    var debit = $(".total").text(totalRupiahall);


                });

                var id_cek = $('.id_cek').val();
                if (id_cek === '2') {
                    $(".pilihan_l").show();
                } else {
                    $(".pilihan_l").hide();
                }


                $(document).on("click", "#Pilihan_Lainnya", function() {
                    if ($(this).prop("checked") == true) {
                        $(".pilihan_l").show();
                        $(".inp-lain").removeAttr("disabled");
                    } else if ($(this).prop("checked") == false) {
                        $(".pilihan_l").hide();
                    }
                });
                $(document).on("keyup", ".debit-lain", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.debit-lain_biasa').val(0)
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.debit-lain_biasa').val(input2)

                    }


                });


            });
        </script>
    @endsection
</x-theme.app>
