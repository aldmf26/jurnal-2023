<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .kop {
            font-size: 21px;
        }

        .awalan {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="sec">
        <h1 style="font-weight: bold; margin-top: 45px; margin-left: 10px; margin-bottom: 10px">BK-IN</h1>
        <table>
            <tr>
                <td class="kop ">No.Faktur</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ $bk1->nota_bk }}</td>
            </tr>
            <tr>
                <td class="kop ">Tanggal</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ date('d-M-Y', strtotime($bk1->tanggal)) }}</td>
            </tr>
            <tr>
                <td class="kop ">Kepada Yth</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ $bk1->suplier_akhir }}</td>
            </tr>
        </table>



        <br>

        <table border="1" style="font-size: 20px;" width="80%">
            <thead>
                <tr>
                    <th align="center" style="padding: 4px;">BK-In(Type)</th>
                    <th align="right" style="padding: 4px;">Qty/Gram</th>
                    <th align="right" style="padding: 4px;">Harga</th>
                    <th align="right" style="padding: 4px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 4px;" align="center">BK-IN-MIX</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk1->gr_beli, 0) }}</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk1->harga, 0) }}</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk1->total, 0) }}</td>
                </tr>
                <tr>
                    <td style="height: 80px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th align="center" style="padding: 4px;">Jumlah</th>
                    <th align="right" style="padding: 4px;">{{ number_format($bk1->gr_beli, 0) }}</th>
                    <th align="right" style="padding: 4px;"></th>
                    <th align="right" style="padding: 4px;">{{ number_format($bk1->total, 0) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="sec">
        <h1 style="font-weight: bold; margin-top: 45px; margin-left: 10px; margin-bottom: 10px">BK-IN</h1>
        <table>
            <tr>
                <td class="kop ">No.Faktur</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ $bk2->nota_bk }}</td>
            </tr>
            <tr>
                <td class="kop ">Tanggal</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ date('d-M-Y', strtotime($bk2->tanggal)) }}</td>
            </tr>
            <tr>
                <td class="kop ">Kepada Yth</td>
                <td class="kop">&nbsp; : &nbsp;</td>
                <td class="kop">{{ $bk2->suplier_akhir }}</td>
            </tr>
        </table>

        <br>

        <table border="1" style="font-size: 20px;" width="80%">
            <thead>
                <tr>
                    <th align="center" style="padding: 4px;">BK-In(Type)</th>
                    <th align="right" style="padding: 4px;">Qty/Gram</th>
                    <th align="right" style="padding: 4px;">Harga</th>
                    <th align="right" style="padding: 4px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 4px;" align="center">BK-IN-MIX</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk2->gr_beli, 0) }}</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk2->harga, 0) }}</td>
                    <td style="padding: 4px;" align="right">{{ number_format($bk2->total, 0) }}</td>
                </tr>
                <tr>
                    <td style="height: 80px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th align="center" style="padding: 4px;">Jumlah</th>
                    <th align="right" style="padding: 4px;">{{ number_format($bk2->gr_beli, 0) }}</th>
                    <th align="right" style="padding: 4px;"></th>
                    <th align="right" style="padding: 4px;">{{ number_format($bk2->total, 0) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

<script>
    window.print()
</script>

</html>
