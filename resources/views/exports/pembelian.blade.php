<table>
    <thead>
        <tr>
            <th width="39px">#</th>
            <th width="110px">Tanggal</th>
            <th width="102px">Nota BK</th>
            <th width="102px">Nota Lot</th>
            <th width="184px">Suplier Akhir</th>
            <th width="151px">Keterangan</th>
            <th width="125px">Gr Beli</th>
            <th width="125px">Harga rata2</th>
            <th width="156px">Total Nota Bk</th>
            <th width="151px">Gr Basah</th>
            <th width="114px">Pcs Awal</th>
            <th width="151px">Gr Kering</th>
            <th width="80px">Susut</th>
            <th width="100px">No Buku Campur</th>
            <th width="80px">TGL Grade</th>
            <th width="50px">Status</th>
            <th width="143px">KAS BESAR</th>
            <th width="143px">Bca No. Rek 0513020888 (untuk Hutang)</th>
            <th width="143px">BANK MANDIRI NO.REK 031-00-5108889-9</th>
            <th width="143px">BANK BCA NO. REK 0511780062</th>
            <th width="143px">Sisa Hutang</th>
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
                <td>{{ $p->id_invoice_bk }}</td>
                <td>{{ $p->tgl }}</td>
                {{-- <td>{{ strtoupper($p->nm_suplier) }}</td> --}}
                <td>{{ $p->no_nota }}</td>
                <td>{{ $p->no_lot }}</td>
                <td>{{ strtoupper($p->suplier_akhir) }}</td>
                <td></td>
                <td>{{ $p->gr_beli }}</td>
                <td>{{ $p->gr_beli == 0 ? 0 : $p->total_harga / $p->gr_beli }}</td>
                <td>{{ $p->total_harga }}</td>
                <td>{{ $p->gr_basah }}</td>
                <td>{{ $p->pcs_awal }}</td>
                <td>{{ $p->gr_kering }}</td>
                <td align="right">
                    {{ empty($p->gr_kering) ? '0' : number_format((1 - $p->gr_beli / $p->gr_kering) * -100, 0) }}
                    %</td>
                <td align="right">{{ $p->no_campur }}</td>
                <td>{{ $p->tgl_grading }}</td>
                @php
                    $kas2 = empty($kas->bayar) ? '0' : $kas->bayar;
                    $bca2 = empty($bca->bayar) ? '0' : $bca->bayar;
                    $mandiri2 = empty($mandiri->bayar) ? '0' : $mandiri->bayar;
                    $bca222 = empty($bca22->bayar) ? '0' : $bca22->bayar;
                @endphp
                <td>{{ $p->lunas == 'D' ? 'Draft' : ($p->total_harga - $kas2 - $bca2 - $mandiri2 <= 0 ? 'Paid' : 'Unpaid') }}
                </td>
                <td>{{ empty($kas->bayar) ? '0' : $kas->bayar }}</td>
                <td>{{ empty($bca->bayar) ? '0' : $bca->bayar }}</td>
                <td>{{ empty($mandiri->bayar) ? '0' : $mandiri->bayar }}</td>
                <td>{{ $bca222 }}</td>
                <td>{{ $p->total_harga - $kas2 - $bca2 - $mandiri2 - $bca222 }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
