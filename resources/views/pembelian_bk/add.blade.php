<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont='container-fluid'>

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-2">

            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('save_pembelian_bk') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-2 col-6">
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control tgl_nota" name="tgl" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Nota</label>
                    <input type="text" class="form-control nota_bk" name="no_nota" value="{{ $sub_po }}"
                        readonly>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Lot</label>
                    <input type="text" class="form-control nota_bk" name="no_lot" value="{{ $no_lot }}"
                        readonly>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">Supplier Awal</label>
                    <select name="suplier_awal" id="select2" class="" required>
                        <option value="">Pilih Suplier</option>
                        @foreach ($suplier as $s)
                            <option value="{{ $s->id_suplier }}">{{ $s->nm_suplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">Supplier</label>
                    <input type="text" class="form-control" name="suplier_akhir" value="" required>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">In BK</label>
                    <select name="in_bk" class="form-select" id="">
                        <option value="Y">Ya (tidak bayar BK)</option>
                        <option value="T" selected>Tidak (Bayar BK)</option>
                    </select>
                </div>

                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped ">
                            <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th width="15%">Produk</th>
                                    <th width="15%">Keterangan</th>
                                    <th width="12%">Qty</th>
                                    <th width="15%">Satuan</th>
                                    <th width="12%" style="text-align: right;white-space: nowrap;">Harga Satuan</th>
                                    <th width="12%" style="text-align: right;">Total Harga</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="baris1">
                                    <td style="vertical-align: top;">
                                        {{-- <button type="button" data-bs-toggle="collapse" href=".join1"
                                            class="btn rounded-pill " count="1"><i class="fas fa-angle-down"></i>
                                        </button> --}}
                                    </td>
                                    <td>
                                        <select name="id_produk[]" id=""
                                            class="select2_add pilih_produk pilih_produk1" count='1' required>
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
                                        <input type="text" class="form-control qty qty1 input-nanda input-nanda1"
                                            count='1' style="vertical-align: top; width: 80px;" value="0">
                                        <input type="hidden" name="qty[]" class="form-control qty_biasa qty_biasa1"
                                            count='1' style="vertical-align: top" value="0">

                                    </td>
                                    <td style="vertical-align: top;">
                                        <select name="id_satuan[]" id="" style="width: 110px;"
                                            class="select2_add satuan1">

                                        </select>

                                    </td>
                                    <td style="vertical-align: top;" align="right">
                                        <input type="text" class="form-control h_satuan h_satuan1 text-end "
                                            value="Rp 0" count="1" style="width: 150px;">
                                        <input type="hidden" class="form-control h_satuan_biasa h_satuan_biasa1"
                                            value="0" name="h_satuan[]">
                                    </td>
                                    <td style="vertical-align: top;" align="right">
                                        <input type="text" class="form-control total_harga1 text-end "
                                            value="" count="1" style="width: 150px" readonly>
                                        <input type="hidden"
                                            class="form-control total_harga_biasa total_harga_biasa1 text-end"
                                            value="" readonly>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <button type="button" class="btn rounded-pill remove_baris"
                                            count="1"><i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>

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
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-1">

                            <div class="form-check form-switch form-switch2">
                                <input class="form-check-input form-check-input2" value="Y" type="checkbox"
                                    id="Pilihan_Lainnya" />

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="Biaya lain-lain">Biaya lain-lain</label>
                        </div>
                        <div class="col-lg-12"></div>
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

                    </div>
                </div>
                <div class="col-lg-6">

                    <hr style="border: 1px solid blue">
                    <table class="" width="100%">
                        <tr>
                            <td width="20%" style="font-size: 16px">Total</td>
                            <td width="40%" class="total" style="text-align: right;font-size: 16px">Rp.0</td>
                            <input type="hidden" class="total_biasa" name="total_harga" value="0">
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
                            $('.qty' + count).attr('id_produk', +id_produk);
                            $('.h_satuan' + count).attr('id_produk', +id_produk);
                            $(".satuan" + count).html(data);
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




                    var h_satuan = $(".h_satuan_biasa" + count).val();
                    if (id_produk == '7') {
                        var total = parseFloat(h_satuan);
                    } else {
                        var total = parseFloat(input2) * parseFloat(h_satuan);
                    }

                    var totalRupiah = total.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
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
                    });
                    var tl = $(".total_biasa").val(total_all);
                    var debit = $(".total").text(totalRupiahall);


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
                    });
                    var tl = $(".total_biasa").val(total_all);
                    var debit = $(".total").text(totalRupiahall);
                });

                var count = 2;
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


                });

                $(".pilihan_l").hide();
                $(document).on("click", "#Pilihan_Lainnya", function() {
                    if ($(this).prop("checked") == true) {
                        $(".pilihan_l").show();
                        $(".inp-lain").removeAttr("disabled");
                    } else if ($(this).prop("checked") == false) {
                        $(".pilihan_l").hide();
                    }
                });

                $(document).on("change", ".tgl_nota", function() {
                    var tgl = $(this).val();
                    $.ajax({
                        type: "get",
                        url: "/nota_invoice_bk?tgl=" + tgl,
                        success: function(data) {
                            $('.nota_bk').val(data);
                        }
                    });
                });


            });
        </script>
    @endsection
</x-theme.app>
