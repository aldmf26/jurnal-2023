<style>
    .dhead {
        background-color: #435EBE !important;
        color: white;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered">
            <tr>
                <th class="dhead">Akun</th>
                <th class="dhead" style="text-align: right">Rupiah</th>
            </tr>
            <tr>
                <td colspan="2" class="fw-bold">
                    <a href="#" onclick="event.preventDefault();" class="tmbhakun" jenis="1"
                        id_kategori_akun='1'>Pendapatan</a>
                </td>
            </tr>
            @php
            $total_p = 0;
            @endphp
            @foreach ($pendapatan as $p)
            @php
            $total_p += $p->kredit;
            @endphp
            <tr>
                <td style="padding-left: 20px">{{ucwords(strtolower($p->nm_akun))}}</td>
                <td align="right">Rp {{number_format($p->kredit,0)}}</td>
            </tr>
            @endforeach

            <tr>
                <td class="fw-bold">Total Pendapatan</td>
                <td class="fw-bold" align="right">Rp. {{number_format($total_p,0)}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" class="fw-bold">
                    <a href="#" onclick="event.preventDefault();" class="tmbhakun" jenis="2"
                        id_kategori_akun='2'>Biaya</a>
                </td>
            </tr>
            @php
            $total_b =0;
            @endphp
            @foreach ($biaya as $p)
            @php
            $total_b += $p->debit;
            @endphp
            <tr>
                <td style="padding-left: 20px">{{ucwords(strtolower($p->nm_akun))}}</td>
                <td align="right">Rp {{number_format($p->debit,0)}}</td>
            </tr>
            @endforeach
            <tr>
                <td class="fw-bold">Total Biaya</td>
                <td class="fw-bold" align="right">Rp. {{number_format($total_b,0)}}</td>
            </tr>
        </table>
    </div>
</div>