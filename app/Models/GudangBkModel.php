<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GudangBkModel extends Model
{
    use HasFactory;
    protected $table = 'buku_campur';

    public static function getPembelianBk($nmgudang)
    {
        $result = DB::select("SELECT 
        a.id_buku_campur, a.approve,
        if(a.approve = 'T',c.tgl,d.tgl) as tgl, 
        a.no_lot,  a.gudang, a.gabung,
        if(a.approve = 'T',b.nm_grade,d.nm_grade) as nm_grade, 
        if(a.approve = 'T',c.no_campur,d.buku) as buku, 
        
        if(a.approve = 'T',a.pcs,d.pcs) as pcs, 
        if(a.approve = 'T',a.gr,d.gr) as gr, 
        if(a.approve = 'T',a.rupiah,if(d.rupiah is null , a.rupiah,d.rupiah)) as rupiah,
        if(a.approve = 'T',a.ket,d.ket) as ket,
        if(a.approve = 'T',a.ket2,d.ket2) as ket2,
        if(a.approve = 'T',a.lok_tgl,d.lok_tgl) as lok_tgl,
        if(a.approve = 'T',a.no_produksi,d.no_produksi) as no_produksi,d.pcs_diambil,d.gr_diambil
        FROM buku_campur as a
        left join grade as b on b.id_grade = a.id_grade
        left join grading as c on c.no_nota = a.no_nota
        left join buku_campur_approve as d on d.id_buku_campur = a.id_buku_campur
        left join invoice_bk as e on e.no_nota = a.no_nota
        left join tb_suplier as f on f.id_suplier = e.id_suplier
        where if(a.approve = 'T',a.gudang,d.gudang) = ? and a.gabung = 'T' and  if(a.approve = 'T',a.rupiah,if(d.rupiah is null , a.rupiah,d.rupiah))  is not null;
        ", [$nmgudang]);

        return $result;
    }
    public static function export_getPembelianBk($gudang)
    {

        $result = DB::select("SELECT 
        a.id_buku_campur, a.approve,
        if(a.approve = 'T',c.tgl,d.tgl) as tgl, 
        a.no_lot,  a.gudang, a.gabung,
        if(a.approve = 'T',b.nm_grade,d.nm_grade) as nm_grade, 
        if(a.approve = 'T',c.no_campur,d.buku) as buku, 
        if(a.approve = 'T',f.nm_suplier,d.suplier_awal) as suplier_awal, 
        if(a.approve = 'T',a.pcs,d.pcs) as pcs, 
        if(a.approve = 'T',a.gr,d.gr) as gr, 
        if(a.approve = 'T',a.rupiah, if(d.rupiah is null , a.rupiah,d.rupiah)) as rupiah,
        if(a.approve = 'T',a.ket,d.ket) as ket,
        if(a.approve = 'T',a.ket2,d.ket2) as ket2,
        if(a.approve = 'T',a.lok_tgl,d.lok_tgl) as lok_tgl,
        if(a.approve = 'T',a.no_produksi,d.no_produksi) as no_produksi,d.pcs_diambil,d.gr_diambil
        FROM buku_campur as a
        left join grade as b on b.id_grade = a.id_grade
        left join grading as c on c.no_nota = a.no_nota
        left join buku_campur_approve as d on d.id_buku_campur = a.id_buku_campur
        left join invoice_bk as e on e.no_nota = a.no_nota
        left join tb_suplier as f on f.id_suplier = e.id_suplier
        where if(a.approve = 'T',a.gudang,d.gudang) = ? and a.gabung = 'T' and if(a.approve = 'T',a.rupiah, if(d.rupiah is null , a.rupiah,d.rupiah)) is not null and if(a.approve = 'T',a.rupiah, if(d.rupiah is null , a.rupiah,d.rupiah)) != 0
        order by if(a.approve = 'T',a.ket,d.ket) ASC , a.id_buku_campur ASC
        ", [$gudang]);
        return $result;
    }
    public static function getPembelianBkExport($id_buku_campur)
    {
        $result = DB::selectOne("SELECT 
        a.id_buku_campur, a.approve,
        if(a.approve = 'T',c.tgl,d.tgl) as tgl, 
        a.no_lot,  a.gudang, a.gabung,
        if(a.approve = 'T',b.nm_grade,d.nm_grade) as nm_grade, 
        if(a.approve = 'T',c.no_campur,d.buku) as buku, 
        if(a.approve = 'T',f.nm_suplier,d.suplier_awal) as suplier_awal, 
        if(a.approve = 'T',a.pcs,d.pcs) as pcs, 
        if(a.approve = 'T',a.gr,d.gr) as gr, 
        if(a.approve = 'T',a.rupiah,d.rupiah) as rupiah,
        if(a.approve = 'T',a.ket,d.ket) as ket,
        if(a.approve = 'T',a.lok_tgl,d.lok_tgl) as lok_tgl,
        if(a.approve = 'T',a.no_produksi,d.no_produksi) as no_produksi,d.pcs_diambil,d.gr_diambil
        FROM buku_campur as a
        left join grade as b on b.id_grade = a.id_grade
        left join grading as c on c.no_nota = a.no_nota
        left join buku_campur_approve as d on d.id_buku_campur = a.id_buku_campur
        left join invoice_bk as e on e.no_nota = a.no_nota
        left join tb_suplier as f on f.id_suplier = e.id_suplier
        where a.id_buku_campur = ?;
        ", [$id_buku_campur]);

        return $result;
    }
    public static function getPembelianBkExportnota($no_nota)
    {
        $result = DB::select("SELECT 
        a.id_buku_campur, a.approve,
        if(a.approve = 'T',c.tgl,d.tgl) as tgl, 
        a.no_lot,  a.gudang, a.gabung,
        if(a.approve = 'T',b.nm_grade,d.nm_grade) as nm_grade, 
        if(a.approve = 'T',c.no_campur,d.buku) as buku, 
        if(a.approve = 'T',f.nm_suplier,d.suplier_awal) as suplier_awal, 
        if(a.approve = 'T',a.pcs,d.pcs) as pcs, 
        if(a.approve = 'T',a.gr,d.gr) as gr, 
        if(a.approve = 'T',a.rupiah,d.rupiah) as rupiah,
        if(a.approve = 'T',a.ket,d.ket) as ket,
        if(a.approve = 'T',a.lok_tgl,d.lok_tgl) as lok_tgl,
        if(a.approve = 'T',a.no_produksi,d.no_produksi) as no_produksi,d.pcs_diambil,d.gr_diambil,
        d.selesai_1
        FROM buku_campur as a
        left join grade as b on b.id_grade = a.id_grade
        left join grading as c on c.no_nota = a.no_nota
        left join buku_campur_approve as d on d.id_buku_campur = a.id_buku_campur
        left join invoice_bk as e on e.no_nota = a.no_nota
        left join tb_suplier as f on f.id_suplier = e.id_suplier
        where a.no_nota = ? 
        order by a.no_nota ASC, b.urutan ASC;
        ", [$no_nota]);

        return $result;
    }

    public static function getSummaryWip($kat)
    {
        if ($kat == 'data') {
            $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
            WHERE a.gudang = 'wip' and b.gabung = 'T' and a.selesai_2 = 'T'
            GROUP by a.ket2
            order by a.ket2 ASC
            ");
        } else {
            $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
            WHERE a.gudang = 'wip' and b.gabung = 'T' and a.selesai_2 = 'Y'
            GROUP by a.ket2
            order by a.ket2 ASC
            ");
        }



        return $result;
    }
    public static function getSummaryWipLot($ket)
    {
        $result = DB::select("SELECT a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2
        FROM buku_campur_approve as a 
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        WHERE a.gudang = 'wip' and a.ket2 = ? and b.gabung = 'T'
        GROUP by a.ket2, a.no_lot;
        ", [$ket]);

        return $result;
    }
    public static function getSummaryWipLotexport()
    {
        $result = DB::select("SELECT a.id_buku_campur, a.no_lot, a.ket, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2
        FROM buku_campur_approve as a
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        WHERE a.gudang = 'wip' and b.gabung = 'T'
        GROUP by a.ket, a.no_lot
        order by a.ket ASC
        ");
        return $result;
    }
    public static function getSummaryWip_pcs($no_lot, $ket)
    {
        $result = DB::select("SELECT a.id_buku_campur, a.no_lot, a.ket, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2
        FROM buku_campur_approve as a 
        WHERE a.gudang = 'wip' and a.no_lot = ? and a.ket = ?
        GROUP by a.no_lot, a.ket;
        ", [$no_lot, $ket]);

        return $result;
    }


    public static function getSummary($gudang, $kat)
    {
        if ($kat == 'data') {
            $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = ?
            WHERE a.gudang = ? and b.gabung = 'T' and a.selesai_2 = 'T'
            GROUP by a.ket2
            order by a.ket2 ASC
            ", [$gudang, $gudang]);
        } else {
            $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = ?
            WHERE a.gudang = ? and b.gabung = 'T' and a.selesai_2 = 'Y'
            GROUP by a.ket2
            order by a.ket2 ASC
            ", [$gudang, $gudang]);
        }

        return $result;
    }
    public static function getSummarypartai($gudang, $nm_partai)
    {
        $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.gr as gr_susut, c.selesai
        FROM buku_campur_approve as a 
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        left join table_susut as c on c.ket = a.ket2 and c.gudang = ?
        WHERE a.gudang = ? and b.gabung = 'T' and a.ket2 = ?
        GROUP by a.ket2
        order by a.ket2 ASC
        ", [$gudang, $gudang, $nm_partai]);

        return $result;
    }

    public static function getPartaicetak($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
        FROM buku_campur_approve as a 
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
        WHERE a.gudang = 'wip' and b.gabung = 'T' and a.selesai_2 = 'T' and a.ket2 = ?
        GROUP by a.ket2
        order by a.ket2 ASC", [$nm_partai]);

        return $result;
    }

    public static function getProduksiGabung()
    {
        $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
        FROM buku_campur_approve as a 
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
        WHERE a.gudang = 'produksi' and b.gabung = 'T' and a.selesai_2 = 'T'
        GROUP by a.ket
        order by a.ket ASC;
        ");

        return $result;
    }

    public static function getSumWipCetak()
    {
        $result = DB::select("SELECT a.partai_h, a.no_box , a.grade, sum(a.pcs_cabut) as pcs_cabut, sum(a.gr_cabut) as gr_cabut, sum(a.ttl_rp) as ttl_rp, sum(a.cost_cabut) as cost_cabut, b.pcs as pcs_susut, b.gr as gr_susut, a.selesai2, b.selesai as selesai_1
        FROM gudang_ctk as a 
        left join table_susut as b on b.ket = a.partai_h and b.gudang = 'wipcetak'
        where a.gudang = 'cetak' and a.selesai = 'selesai'
        GROUP by a.partai_h
        Order by a.partai_h ASC
        ");

        return $result;
    }
    public static function getSumWipCetak2($limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT a.partai_h, a.no_box , a.grade, sum(a.pcs_cabut) as pcs_cabut, sum(a.gr_cabut) as gr_cabut, sum(a.ttl_rp) as ttl_rp, sum(a.cost_cabut) as cost_cabut, a.selesai2, sum(a.pcs_timbang_ulang) as pcs_t_ulang, sum(a.gr_timbang_ulang) as gr_t_ulang
        FROM gudang_ctk as a 
        where a.gudang = 'cetak' and a.selesai = 'selesai'
        GROUP by a.no_box
        Order by a.no_box ASC 
        
        ");

        return $result;
    }
    public static function export_cetak()
    {
        $result = DB::select("SELECT a.no_box, a.tipe, a.grade, a.partai_h, a.pcs_cabut, a.gr_cabut, a.ttl_rp, a.cost_cabut,a.pcs_timbang_ulang, a.gr_timbang_ulang,

        b.pcs_awal, b.gr_awal, b.pcs_tidak_ctk, b.gr_tidak_ctk, b.pcs_awal_ctk, b.gr_awal_ctk, b.pcs_cu, b.gr_cu, b.pcs_akhir, b.gr_akhir, b.rp_ctk
        From gudang_ctk as a
        left join (
            SELECT b.no_box, sum(b.pcs_awal) as pcs_awal, sum(b.gr_awal) as gr_awal, sum(b.pcs_tidak_ctk) as pcs_tidak_ctk, sum(b.gr_tidak_ctk) as gr_tidak_ctk, sum(b.pcs_awal_ctk) as pcs_awal_ctk, sum(b.gr_awal_ctk) as gr_awal_ctk, sum(b.pcs_cu) as pcs_cu, sum(b.gr_cu) as gr_cu, sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir, sum(b.pcs_akhir * b.rp_pcs) as rp_ctk
            FROM cetak as b 
            group by b.no_box
        ) as b on b.no_box = a.no_box
        WHERE a.selesai = 'selesai' and a.gudang = 'cetak'
        ");

        return $result;
    }

    public static function g_p_kerja()
    {
        $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai, d.pcs as pcs_bk , d.gr as gr_bk, a.opname_bulan, a.tgl, c.ket as ket_susut, a.buku
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
            left join bk_timbang_ulang as d on d.nm_partai = a.ket2
            WHERE a.gudang = 'wip' and b.gabung = 'T' and a.selesai_2 = 'T' and b.buku != '10968'
            GROUP by a.ket2
            order by a.ket2 ASC;
            ");
        return $result;
    }


    public static function getSummaryWipnew()
    {

        $result = DB::select("SELECT a.nm_grade,count(a.no_lot) as no_lot1, a.id_buku_campur, a.no_lot, a.ket,a.ket2, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.rupiah * a.gr) as total_rp , a.selesai_1, a.selesai_2, a.ket2, c.pcs as pcs_susut, c.gr as gr_susut, c.selesai
            FROM buku_campur_approve as a 
            left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
            left join table_susut as c on c.ket = a.ket2 and c.gudang = 'wip'
            WHERE a.gudang = 'wip' and b.gabung = 'T'
            GROUP by a.ket2
            order by a.ket2 ASC
            ");
        return $result;
    }

    public static function getPartai($nm_partai)
    {
        $result = DB::selectOne("SELECT a.ket2, a.ket, sum(a.pcs) as pcs , sum(a.gr) as gr, a.rupiah
        FROM buku_campur_approve as a 
        where a.gudang = 'wip' and a.ket2 = ?
        group by a.ket2;", [$nm_partai]);

        return $result;
    }
}
