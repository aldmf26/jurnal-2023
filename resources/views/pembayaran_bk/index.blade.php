<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        @php
            $total_paid = 0;
            $total_unpaid = 0;
            $total_draft = 0;
        @endphp
        @foreach ($paid as $p)
            @php
                $total_paid += $p->total_harga + $p->debit;
            @endphp
        @endforeach

        @foreach ($unpaid as $u)
            @php
                $total_unpaid += $u->total_harga + $u->debit - $u->kredit;
            @endphp
        @endforeach

        @foreach ($draft as $d)
            @php
                $total_draft += $d->total_harga + $d->debit - $d->kredit;
            @endphp
        @endforeach

        <div class="row justify-content-end">
            <div class="col-lg-12 mb-2">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ empty($tipe) ? 'active' : '' }}" href="{{ route('pembayaranbk') }}"
                            type="button" role="tab" aria-controls="pills-home" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tipe == 'D' ? 'active' : '' }}"
                            href="{{ route('pembayaranbk', ['tipe' => 'D']) }}" type="button" role="tab"
                            aria-controls="pills-home" aria-selected="true">Draft <br>
                            Rp {{ number_format($total_draft, 0) }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tipe == 'Y' ? 'active' : '' }}"
                            href="{{ route('pembayaranbk', ['tipe' => 'Y']) }}" type="button" role="tab"
                            aria-controls="pills-home" aria-selected="true">Paid <br>
                            Rp {{ number_format($total_paid, 0) }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tipe == 'T' ? 'active' : '' }}"
                            href="{{ route('pembayaranbk', ['tipe' => 'T']) }}" type="button" role="tab"
                            aria-controls="pills-home" aria-selected="true">Unpaid <br>
                            Rp {{ number_format($total_unpaid, 0) }}</a>
                    </li>
                </ul>
                <hr style="border:1px solid #435EBE">
            </div>
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} : {{ tanggal($tgl1) }}
                    ~ {{ tanggal($tgl2) }}
                </h6>

            </div>
            <div class="col-lg-6">
                <x-theme.btn_filter title="Filter Pembayaran Bk" />
                @if (!empty($export))
                    <x-theme.button modal="T"
                        href="/exportBayarbk?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}" icon="fa-file-excel"
                        addClass="float-end float-end btn btn-success me-2" teks="Export" />
                @endif

                <x-theme.akses :halaman="$halaman" route="pembayaranbk" />

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <form action="{{ route('pembayaranbk.add') }}" method="get">
            <div class="row justify-content-end">

            </div>
            <section class="row">
                <div class="col-lg-8"></div>
                <div class="col-lg-4 mb-2">
                    <table class="float-end">
                        <td>Pencarian :</td>
                        <td><input type="text" id="pencarian" class="form-control float-end"></td>
                    </table>


                </div>
                {{-- <style>
                    .dhead {
                        background-color: #435EBE !important;
                        color: white;
                    }
                </style> --}}
                <table class="table table-hover" id="tablealdi" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead" width="5">#</th>
                            <th class="dhead"></th>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">No Nota</th>
                            <th class="dhead" width="10%">Akun</th>
                            <th class="dhead">Suplier</th>
                            <th class="dhead" style="text-align: right">Total Rp</th>
                            <th class="dhead" style="text-align: right">Terbayar</th>
                            <th class="dhead" style="text-align: right">Sisa Hutang</th>
                            <th class="dhead">Status</th>
                            <th class="dhead">Admin</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($pembelian as $no => $p)
                            <tr class="fw-bold induk_detail{{ $p->no_nota }}">
                                <td>{{ $i++ }}</td>
                                <td>
                                    <a href="#" onclick="event.preventDefault();"
                                        class="detail_bayar detail_bayar{{ $p->no_nota }}"
                                        no_nota="{{ $p->no_nota }}"><i class="fas fa-angle-down"></i></a>

                                    <a href="#" onclick="event.preventDefault();"
                                        class="hide_bayar hide_bayar{{ $p->no_nota }}"
                                        no_nota="{{ $p->no_nota }}"><i class="fas fa-angle-up"></i></a>
                                </td>
                                <td>{{ tanggal($p->tgl) }}</td>
                                <td>{{ $p->no_nota }}</td>
                                <td>Bkin</td>
                                <td>{{ ucwords(strtolower($p->suplier_akhir)) }}</td>
                                <td align="right">Rp. {{ number_format($p->total_harga, 0) }}</td>
                                <td align="right">Rp. {{ number_format($p->kredit, 0) }}</td>
                                <td align="right">Rp. {{ number_format($p->total_harga + $p->debit - $p->kredit, 0) }}
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $p->lunas == 'D' ? 'bg-warning' : (round($p->total_harga) + round($p->debit) - round($p->kredit) == 0 ? 'bg-success' : 'bg-danger') }}">
                                        {{ $p->lunas == 'D' ? 'Draft' : (round($p->total_harga) + round($p->debit) - round($p->kredit) == 0 ? 'Paid' : 'Unpaid') }}
                                    </span>
                                </td>
                                <td>{{ $p->admin }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <span class="btn btn-sm" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-primary"></i>
                                        </span>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            @if (!empty($edit))
                                                <li>
                                                    <a class="dropdown-item text-primary edit_akun"
                                                        href="{{ route('pembayaranbk.edit', ['nota' => $p->no_nota]) }}"><i
                                                            class="me-2 fas fa-pen"></i>Edit
                                                    </a>
                                                </li>
                                            @endif
                                            @if (!empty($bayar))
                                                <li>
                                                    @if ($p->lunas == 'D')
                                                        {{-- <a class="dropdown-item text-primary  disabled" href="#"><i
                                                    class="fas fa-money-bill-wave me-2"></i>Bayar</a> --}}
                                                    @else
                                                        @if ($p->total_harga + $p->debit - $p->kredit == 0)
                                                            {{-- <a href="#" class="dropdown-item text-primary  disabled"><i
                                                    class="fas fa-money-bill-wave me-2"></i>Bayar</a> --}}
                                                        @else
                                                            <a href="{{ route('pembayaranbk.add', ['nota' => $p->no_nota]) }}"
                                                                class="dropdown-item text-success  "><i
                                                                    class="fas fa-money-bill-wave me-2"></i>Bayar</a>
                                                        @endif
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </section>
        </form>

    </x-slot>
    @section('scripts')
        <script>
            $(document).ready(function() {
                pencarian('pencarian', 'tablealdi')
                $('.hide_bayar').hide();
                $(document).on("click", ".detail_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    var clickedElement = $(this); // Simpan elemen yang diklik dalam variabel

                    clickedElement.prop('disabled', true); // Menonaktifkan elemen yang diklik

                    $.ajax({
                        type: "get",
                        url: "/get_kreditBK?no_nota=" + no_nota,
                        success: function(data) {
                            $('.induk_detail' + no_nota).after("<tr>" + data + "</tr>");
                            $(".show_detail" + no_nota).show();
                            $(".detail_bayar" + no_nota).hide();
                            $(".hide_bayar" + no_nota).show();

                            clickedElement.prop('disabled',
                                false
                            ); // Mengaktifkan kembali elemen yang diklik setelah tampilan ditambahkan
                        },
                        error: function() {
                            clickedElement.prop('disabled',
                                false
                            ); // Jika ada kesalahan dalam permintaan AJAX, pastikan elemen yang diklik diaktifkan kembali
                        }
                    });
                });

                $(document).on("click", ".hide_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    $(".show_detail" + no_nota).remove();
                    $(".detail_bayar" + no_nota).show();
                    $(".hide_bayar" + no_nota).hide();

                });
            });
        </script>
    @endsection
</x-theme.app>
