<table>
    <thead>
        <tr>
            <th width="30px">#</th>
            <th width="110px">Tanggal</th>
            <th width="102px">No Nota</th>
            <th width="184px">Suplier Awal</th>
            <th width="184px">Suplier Akhir</th>
            <th width="125px">Total Gr</th>
            <th width="156px">Total Rp</th>
            <th width="50px">Status</th>
            <th width="143px">KAS BESAR</th>
            <th width="143px">Bca No. Rek 0513020888 (untuk Hutang)</th>
            <th width="143px">BANK MANDIRI NO.REK 031-00-5108889-9</th>
            <th width="143px">BANK BCA NO. REK 0511780062</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pembelian as $no => $p)
            @php
                $kas = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
        FROM bayar_bk as a
        where a.no_nota = '$p->no_nota' and a.id_akun = '4'
        group by a.no_nota;");
                $bca = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
        FROM bayar_bk as a
        where a.no_nota = '$p->no_nota' and a.id_akun = '30'
        group by a.no_nota;");
                $mandiri = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
        FROM bayar_bk as a
        where a.no_nota = '$p->no_nota' and a.id_akun = '10'
        group by a.no_nota;");
                $bca22 = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
        FROM bayar_bk as a
        where a.no_nota = '$p->no_nota' and a.id_akun = '6'
        group by a.no_nota;");
            @endphp
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $p->tgl }}</td>
                <td>{{ $p->no_nota }}</td>
                <td>{{ strtoupper($p->nm_suplier) }}</td>
                <td>{{ strtoupper($p->suplier_akhir) }}</td>
                <td>{{ $p->qty }}</td>
                <td>{{ $p->total_harga }}</td>
                @php
                    $kas2 = empty($kas->bayar) ? '0' : $kas->bayar;
                    $bca2 = empty($bca->bayar) ? '0' : $bca->bayar;
                    $mandiri2 = empty($mandiri->bayar) ? '0' : $mandiri->bayar;
                    $bca222 = empty($bca22->bayar) ? '0' : $bca22->bayar;
                @endphp
                <td>{{ $p->lunas == 'D' ? 'Draft' : (round($p->total_harga, 0) - $kas2 - $bca2 - $mandiri2 - $bca222 <= 0 ? 'Paid' : 'Unpaid') }}

                </td>
                <td>{{ empty($kas->bayar) ? '0' : $kas->bayar }}</td>
                <td>{{ empty($bca->bayar) ? '0' : $bca->bayar }}</td>
                <td>{{ empty($mandiri->bayar) ? '0' : $mandiri->bayar }}</td>
                <td>{{ $bca222 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
