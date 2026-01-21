<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-2">

            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                /* font-size: 12px; */
                width: 170px;

            }
        </style>
        <form action="{{ route('pembayaranbk.save_pembayaran') }}" method="post" class="save_jurnal">
            @csrf

            <section class="row justify-content-center">

                <div class="col-lg-3 col-6">
                    <label for="">No Nota</label>
                    <input type="text" class="form-control" name="cfm_pembayaran" value="{{ $p->no_nota }}"
                        readonly>
                </div>
                <div class="col-lg-9"></div>
                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="10%">Tanggal</th>
                                <th width="10%">Akun</th>
                                <th width="20%">Keterangan</th>
                                <th width="10%">Suplier Awal</th>
                                <th width="10%">Suplier Akhir</th>
                                <th width="15%" style="text-align: right">Debit</th>
                                <th width="15%" style="text-align: right">Kredit</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ date('d-m-Y', strtotime($p->tgl)) }}</td>
                                <td>Bkin</td>
                                <td>Pembayaran BKIN {{ $p->no_nota }}</td>
                                <td>{{ $p->nm_suplier }}</td>
                                <td>{{ $p->suplier_akhir }}</td>
                                <td align="right">Rp. {{ number_format($p->total_harga, 0) }}</td>
                                <td align="right">0</td>
                                <td></td>
                            </tr>
                            @php
                                $total = 0;
                                $total_debit = 0;
                                $tgl = 0;
                            @endphp
                            @foreach ($bayar as $b)
                                @php
                                    $total += $b->kredit;
                                    $total_debit = $b->debit;
                                    $tgl = $b->tgl;

                                @endphp
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($b->tgl)) }}</td>
                                    <td>{{ ucwords(strtolower($b->nm_akun)) }}</td>
                                    <td>{{ $b->ket }}</td>
                                    <td>{{ $b->nm_suplier }}</td>
                                    <td>{{ $b->suplier_akhir }}</td>
                                    <td align="right">Rp. {{ number_format($b->debit, 0) }}</td>
                                    <td align="right">
                                        Rp. {{ number_format($b->kredit, 0) }}
                                        <input type="hidden" class="form-control bayarbiasa "
                                            value="{{ $b->kredit }}">
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <input type="hidden" class="debit" value="{{ $p->total_harga + $total_debit }}">
                            <tr class="baris1">
                                <td><input type="date" class="form-control" name="tgl_pembayaran[]"
                                        value="{{ $tgl }}"></td>
                                <td>
                                    <select name="id_akun[]" id="" class="select2_add" required>
                                        <option value="">Pilih Akun Pembayaran</option>
                                        <option value="4">KAS BESAR</option>
                                        <option value="10">BANK MANDIRI NO.REK 031-00-5108889-9</option>
                                        <option value="30">Bca No. Rek 0513020888 (untuk Hutang)</option>
                                        <option value="6">BANK BCA NO. REK 0511780062</option>
                                        <option value="45">PENDAPATAN  DILUAR USAHA</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="ket[]"
                                        value="Pembayaran BKIN {{ $p->no_nota }}">
                                </td>
                                <td>{{ $p->nm_suplier }}</td>
                                <td>{{ $p->suplier_akhir }}</td>
                                <td align="right">
                                    <input type="text" class="form-control bayardebit bayardebit1 text-end"
                                        count="1">
                                    <input type="hidden" name="debit[]"
                                        class="form-control bayardebitbiasa bayardebitbiasa1" value="0">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control bayar bayar1 text-end" count="1">
                                    <input type="hidden" name="kredit[]" class="form-control bayarbiasa bayarbiasa1"
                                        value="0">
                                </td>
                                <td>
                                    <button type="button" class="btn rounded-pill remove_baris" count="1"><i
                                            class="fas fa-trash text-danger"></i>
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
                                        nota="{{ $p->no_nota }}"
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


                    </div>
                </div>
                <div class="col-lg-6">

                    <hr style="border: 1px solid blue">
                    <table class="" width="100%">
                        {{-- <input type="text" class="totaldebitbiasa">
                        <input type="text" class="totalkreditbiasa"> --}}
                        <tr>

                            <td width="20%">Total</td>
                            <td width="40%" class="total" style="text-align: right;">
                                Rp.{{ number_format($p->total_harga + $total_debit - $p->jumlah_pembayaran, 2) }}
                            </td>
                            <td width="40%" class="total_kredit" style="text-align: right;">
                                Rp. {{ number_format($total, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="cselisih" colspan="2">Sisa Hutang</td>
                            <td style="text-align: right;" class="selisih cselisih">Rp.
                                {{ number_format($p->total_harga + $total_debit - $total, 2) }}</td>
                        </tr>
                    </table>

                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
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
                var count = 3;
                $(document).on("click", ".tbh_baris", function() {
                    count = count + 1;
                    var nota = $(this).attr('nota');
                    $.ajax({
                        url: "/pembayaranbk.tambah?count=" + count + "&no_nota=" + nota,
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
                    var total_kredit = 0;
                    $(".bayarbiasa").each(function() {
                        total_kredit += parseFloat($(this).val());
                    });
                    var total_debit = 0;
                    $(".bayardebitbiasa").each(function() {
                        total_debit += parseFloat($(this).val());
                    });
                    var debit = $('.debit').val();
                    var total_debitall = parseFloat(debit) + total_debit;


                    var selisih = total_debitall - total_kredit;
                    var selisih_total = selisih.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    var totalRupiah = total_kredit.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $(".total_kredit").text(totalRupiah);

                    if (selisih === 0) {
                        $(".cselisih").css("color", "green");

                    } else {
                        $(".cselisih").css("color", "red");
                    }
                    $(".selisih").text(selisih_total);

                });

                $(document).on("keyup", ".bayar", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.bayarbiasa' + count).val(0)
                    } else {
                        $(this).val("Rp " + input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.bayarbiasa' + count).val(input2)

                    }
                    var total_kredit = 0;
                    $(".bayarbiasa").each(function() {
                        total_kredit += parseFloat($(this).val());
                    });


                    var total_debit = 0;
                    $(".bayardebitbiasa").each(function() {
                        total_debit += parseFloat($(this).val());
                    });
                    var debit = $('.debit').val();
                    var total_debitall = parseFloat(debit) + total_debit;


                    var selisih = total_debitall - total_kredit;
                    var selisih_total = selisih.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    var totalRupiah = total_kredit.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $(".total_kredit").text(totalRupiah);

                    if (selisih === 0) {
                        $(".cselisih").css("color", "green");

                    } else {
                        $(".cselisih").css("color", "red");

                    }
                    $(".selisih").text(selisih_total);
                });

                $(document).on("keyup", ".bayardebit", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.bayardebitbiasa' + count).val(0)
                    } else {
                        $(this).val("Rp " + input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.bayardebitbiasa' + count).val(input2)

                    }
                    var total_kredit = 0;
                    $(".bayarbiasa").each(function() {
                        total_kredit += parseFloat($(this).val());
                    });
                    var total_debit = 0;
                    $(".bayardebitbiasa").each(function() {
                        total_debit += parseFloat($(this).val());
                    });

                    var debit = $('.debit').val();
                    var total_debitall = parseFloat(debit) + total_debit;


                    var selisih = total_debitall - total_kredit;
                    var selisih_total = selisih.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    var totalRupiahDebit = total_debitall.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $(".total").text(totalRupiahDebit);

                    if (selisih === 0) {
                        $(".cselisih").css("color", "green");

                    } else {
                        $(".cselisih").css("color", "red");

                    }
                    $(".selisih").text(selisih_total);
                });


            });
        </script>
    @endsection
</x-theme.app>
