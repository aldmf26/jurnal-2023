<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>

<body class="py-2 px-3">
    <style>
        .bdr {
            border-radius: 16px;
            overflow: hidden;
        }

        .rounded-tfoot th:first-child {
            border-bottom-left-radius: 16px;
        }



        .rounded-tfoot th:last-child {
            border-bottom-right-radius: 16px;
        }

        .dotted-line {
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .dotted-line::after {
            content: "----------------------------------------------------------------------------------------------------------";
            letter-spacing: 6px;
            font-size: 30px;
            color: #9cbfdb;
            display: inline-block;
            vertical-align: 3px;
            padding-left: 10px;
        }

        @media (max-width:1600px) {
            .logoHp {
                display: none
            }


        }

        @media (max-width:480px) {
            .logoHp {
                display: block
            }

            .logoDesktop {
                display: none
            }
        }
    </style>
    <style>
        @media print {
            .print_hilang {
                display: none;
            }
        }
    </style>
    <div class="row print_hilang">
        <div class="col-lg-6">
            <a href="#" class="print btn btn-success float-start me-2">Print</a>
            <a href="{{ route('edit_pembelian_bk', ['nota' => $pembelian->no_nota]) }}"
                class=" btn btn-warning float-start">Back</a>
        </div>
        <div class="col-lg-12">
            <hr style="border: 1px solid black">
        </div>
    </div>
    @for ($i = 0; $i < 2; $i++)
        <div class="container text-center">

            <div class="logoHp row text-center float-center align-items-center">
                <div class="col-12 float-center justify-content-center">
                    <table class="text-center float-center">
                        <tr>
                            <td><img src="/assets/login/img/empat.svg" width="100" alt=""></td>
                            <td>
                                <h5 class=" text-end" style="color:#0071C1">PT Agrika Gatya Arum</h5>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="logoDesktop row align-items-center">
                <div class="col"></div>
                <div class="col float-end">
                    <table class="text-end">
                        <tr>
                            <td><img src="/assets/login/img/empat.svg" width="100" alt=""></td>
                            <td>
                                <h5 class=" text-end" style="color:#0071C1">PT Agrika Gatya Arum</h5>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <hr class="text-black" style="border: 1px solid black">
            <div class="row">
                <div class="col-4 col-lg-6 text-start">

                    <p style="margin-bottom: 1px; font-size: 12px">Kpd Yth, Bpk/Ibu</p>
                    <h6 style="font-size: 12px">{{ strtoupper($pembelian->suplier_akhir) }}</h6>
                </div>
                <div class="col-8 col-lg-6 text-end">
                    <table class="float-end" style="font-size: 13px;">
                        <tr>
                            <td align="left">

                                No Nota
                            </td>
                            <td width="10">:</td>
                            <th width="130" class="text-end">#{{ $pembelian->no_nota }}</th>
                        </tr>
                        <tr>
                            <td align="left">

                                Tanggal
                            </td>
                            <td width="10">:</td>
                            <th width="130" class="text-end">{{ tanggal($pembelian->tgl) }}</th>
                        </tr>
                    </table>

                </div>
            </div>

            <div class="row mt-4">
                <div class="col">
                    <div class="tbl-container bdr">
                        <table class="table table-sm">
                            <thead class=" text-white" style="background-color: #309fee">
                                <tr>
                                    <th class="text-start">Grade</th>
                                    <th>ket</th>
                                    <th class="text-end" style="padding-right: 18px">Gr</th>
                                    <th class="text-end" style="padding-right: 18px">Rp</th>
                                    <th class="text-end">Total Rp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $qty_total = 0;
                                @endphp
                                @foreach ($produk as $no => $p)
                                    @php
                                        $qty_total += $p->qty;
                                        $ket = $p->ket;
                                        $ket_with_br = strlen($ket) > 50 ? wordwrap($ket, 50, "<br>\n") : $ket;
                                    @endphp
                                    <tr class="align-middle">
                                        <td align="left">{{ $p->nm_produk }}</td>
                                        <td>{{ $p->ket }}</td>
                                        <td align="right">{{ number_format($p->qty, 0) }}</td>
                                        <td align="right">{{ number_format($p->h_satuan, 0) }}</td>
                                        <td align="right">
                                            {{ $p->id_produk == '7' ? number_format($p->h_satuan, 0) : number_format($p->qty == '0' ? '0' : $p->qty * $p->h_satuan, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="rounded-tfoot" style="background-color: #D3D3D3;border-top: 2px solid white;">
                                <tr>
                                    <th style="text-align: start;">Total</th>
                                    <th></th>
                                    <th style="text-align: right;">
                                        <?= number_format($qty_total, 0) ?>
                                    </th>
                                    <th style="text-align: right;">
                                        <?= number_format($pembelian->total_harga == '0' ? '0' : $pembelian->total_harga / $qty_total, 0) ?>
                                    </th>
                                    <th style="text-align: right;">
                                        <?= number_format($pembelian->total_harga, 0) ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p class="float-start" style="margin-left: 30px; color:#0071C1"><em>T e r i m a k a s i h !</em></p>
                    </p>
                </div>
            </div>

            @if ($i == 0)
                <h6 class="dotted-line"></h6>
            @endif
        </div>
    @endfor




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.print').click(function(e) {
            e.preventDefault();
            window.print()
        });
    </script>
</body>

</html>
