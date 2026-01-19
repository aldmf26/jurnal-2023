<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportbayarBK;
use App\Models\User;
use SettingHal;

class PembayaranBkController extends Controller
{
    protected $tgl1, $tgl2, $tipe, $period;
    public function __construct(Request $r)
    {
        if (empty($r->period)) {
            $this->tgl1 = date('2023-01-01');
            $this->tgl2 = date('Y-12-t');
        } elseif ($r->period == 'daily') {
            $this->tgl1 = date('Y-m-d');
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'weekly') {
            $this->tgl1 = date('Y-m-d', strtotime("-6 days"));
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'mounthly') {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
            $tgl = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl));
            $this->tgl2 = date('Y-m-t', strtotime($tgl));
        } elseif ($r->period == 'costume') {
            $this->tgl1 = $r->tgl1;
            $this->tgl2 = $r->tgl2;
        } elseif ($r->period == 'years') {
            $tahun = $r->tahunfilter;
            $tgl_awal = "$tahun" . "-" . "01" . "-" . "01";
            $tgl_akhir = "$tahun" . "-" . "12" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl_awal));
            $this->tgl2 = date('Y-m-t', strtotime($tgl_akhir));
        }
    }

    public function index(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;
        $tipe = $r->tipe;

        $queryInBk = "a.in_bk = 'T' OR a.in_bk IS NULL AND";

        if ($tipe == 'D') {
            $pembelian = DB::select("SELECT a.tgl,a.admin, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.lunas = '$tipe' and a.tgl between '$tgl1' and '$tgl2'
            order by a.id_invoice_bk ASC
            ");
        } elseif (empty($tipe)) {
            $pembelian = DB::select("SELECT a.tgl,a.admin, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit, b.nm_suplier
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where  a.tgl between '$tgl1' and '$tgl2'
            order by a.id_invoice_bk ASC
            ");
        } elseif ($tipe == 'Y') {
            $pembelian = DB::select("SELECT a.tgl,a.admin, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit,b.nm_suplier
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.total_harga + c.debit - c.kredit = '0' and a.tgl between '$tgl1' and '$tgl2'
            order by a.id_invoice_bk ASC
            ");
        } elseif ($tipe == 'T') {
            $pembelian = DB::select("SELECT a.tgl,a.admin, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit,b.nm_suplier
            FROM invoice_bk as a
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where round(a.total_harga) + if(c.debit is null , 0,c.debit) - if(c.kredit is null , 0 ,c.kredit) != '0' and a.tgl between '$tgl1' and '$tgl2'
            order by a.id_invoice_bk ASC
            ");
        }


        $draft = DB::select("SELECT a.tgl, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit,b.nm_suplier
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where a.lunas = 'D'
        order by a.id_invoice_bk ASC
        ");

        $paid =  DB::select("SELECT a.tgl, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit,b.nm_suplier
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where round(a.total_harga) + round(c.debit) - round(c.kredit) = '0'
        order by a.id_invoice_bk ASC
        ");

        $unpaid = DB::select("SELECT a.tgl, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, if(c.kredit is null , 0 ,c.kredit) as kredit, if(c.debit is null , 0,c.debit) as debit,b.nm_suplier
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(if(c.debit is null , 0, c.debit)) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where round(a.total_harga) + if(c.debit is null , 0,c.debit) - if(c.kredit is null , 0 ,c.kredit) != '0'
        order by a.id_invoice_bk ASC;
        ");
        $id_user = auth()->user()->id;
        $buttonIds = [15, 16, 17];
        $userPermissions = DB::table('permission_perpage as a')
            ->join('permission_button as b', 'b.id_permission_button', '=', 'a.id_permission_button')
            ->whereIn('a.id_permission_button', $buttonIds)
            ->where('a.id_user', $id_user)
            ->get()
            ->keyBy('id_permission_button');

        $data =  [
            'title' => 'Pembayaran Bahan Baku',
            'pembelian' => $pembelian,

            'tipe' => $tipe,
            'draft' => $draft,
            'paid' => $paid,
            'unpaid' => $unpaid,
            'tgl1' =>  $tgl1,
            'tgl2' =>  $tgl2,

            'user' => User::where('posisi_id', 1)->get(),
            'halaman' => 3,
            'export' => $userPermissions->has(15),
            'edit' => $userPermissions->has(16),
            'bayar' => $userPermissions->has(17),
        ];
        return view('pembayaran_bk.index', $data);
    }

    public function add(Request $r)
    {
        $nota = $r->nota;

        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");

        $bayar = DB::select("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota'
        group by a.id_bayar_bk;");


        $data = [
            'title' => 'Pembayaran Bahan Baku',
            'p' => $p,
            'bayar' => $bayar,

        ];

        return view('pembayaran_bk.add', $data);
    }

    public function save_pembayaran(Request $r)
    {
        $cfm_pembayaran = $r->cfm_pembayaran;
        $id_akun = $r->id_akun;
        $kredit = $r->kredit;
        $debit = $r->debit;
        $tgl = $r->tgl_pembayaran;
        $keterangan = $r->ket;

        for ($x = 0; $x < count($id_akun); $x++) {
            $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();

            if (empty($max)) {
                $nota_t = '1000';
            } else {
                $nota_t = $max->nomor_nota + 1;
            }

            $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', $id_akun[$x])->first();
            $akun = DB::table('akun')->where('id_akun', $id_akun[$x])->first();
            if ($max_akun->urutan == 0) {
                $urutan = '1001';
            } else {
                $urutan = $max_akun->urutan + 1;
            }

            $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', 512)->first();
            $akun2 = DB::table('akun')->where('id_akun', $id_akun[$x])->first();
            if ($max_akun2->urutan == 0) {
                $urutan2 = '1001';
            } else {
                $urutan2 = $max_akun->urutan + 1;
            }
            DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '2']);

            $data = [
                'no_nota' => $cfm_pembayaran,
                'debit' => $debit[$x],
                'kredit' => $kredit[$x],
                'id_akun' => $id_akun[$x],
                'tgl' => $tgl[$x],
                'admin' => Auth::user()->name,
                'ket' => $keterangan[$x],
                'nota_jurnal' => 'JU-' . $nota_t,
                'bayar' => 'Y',

            ];
            DB::table('bayar_bk')->insert($data);

            if ($debit[$x] == '0') {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $kredit[$x],
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                    'no_urut' => $akun->inisial . '-' . $urutan,
                    'urutan' => $urutan,
                ];
                DB::table('jurnal')->insert($data_kredit);

                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $kredit[$x],
                    'kredit' => '0',
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                    'no_urut' => $akun2->inisial . '-' . $urutan2,
                    'urutan' => $urutan2,
                ];
                DB::table('jurnal')->insert($data_debit);
            } else {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $debit[$x],
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                    'no_urut' => $akun2->inisial . '-' . $urutan2,
                    'urutan' => $urutan2,
                ];
                DB::table('jurnal')->insert($data_kredit);


                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $debit[$x],
                    'kredit' => '0',
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                    'no_urut' => $akun->inisial . '-' . $urutan,
                    'urutan' => $urutan,
                ];
                DB::table('jurnal')->insert($data_debit);
            }
        }

        return redirect()->route('pembayaranbk')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function tambah(Request $r)
    {
        $nota = $r->no_nota;
        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");
        $data =  [
            'count' => $r->count,
            'p' => $p

        ];
        return view('pembayaran_bk.tambah', $data);
    }

    public function edit(Request $r)
    {
        $nota = $r->nota;
        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");
        $bayar = DB::select("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota' and a.bayar = 'T'
        group by a.id_bayar_bk;");

        $bayar2 = DB::select("SELECT a.tgl, a.id_akun, a.nota_jurnal, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota' and a.bayar = 'Y'
        group by a.id_bayar_bk;");


        $data = [
            'title' => 'Pembayaran Bahan Baku',
            'p' => $p,
            'bayar' => $bayar,
            'bayar2' => $bayar2

        ];

        return view('pembayaran_bk.edit', $data);
    }

    public function save_edit(Request $r)
    {
        $cfm_pembayaran = $r->cfm_pembayaran;
        $id_akun = $r->id_akun;
        $kredit = $r->kredit;
        $debit = $r->debit;
        $tgl = $r->tgl_pembayaran;
        $keterangan = $r->ket;
        $nota_jurnal = $r->nota_jurnal;

        for ($x = 0; $x < count($nota_jurnal); $x++) {
            DB::table('jurnal')->where('no_nota', $nota_jurnal[$x])->delete();
            DB::table('bayar_bk')->where('nota_jurnal', $nota_jurnal[$x])->delete();
        }

        for ($x = 0; $x < count($id_akun); $x++) {
            $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();

            if (empty($max)) {
                $nota_t = '1000';
            } else {
                $nota_t = $max->nomor_nota + 1;
            }
            DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '2']);



            $data = [
                'no_nota' => $cfm_pembayaran,
                'debit' => $debit[$x],
                'kredit' => $kredit[$x],
                'id_akun' => $id_akun[$x],
                'tgl' => $tgl[$x],
                'admin' => Auth::user()->name,
                'ket' => $keterangan[$x],
                'nota_jurnal' => 'JU-' . $nota_t,
                'bayar' => 'Y'
            ];
            DB::table('bayar_bk')->insert($data);
            if ($debit[$x] == '0') {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $kredit[$x],
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);

                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $kredit[$x],
                    'kredit' => '0',
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            } else {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $debit[$x],
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);

                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $debit[$x],
                    'kredit' => '0',
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            }
        }
        return redirect()->route('pembayaranbk')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function get_kreditBK(Request $r)
    {
        $bayar = DB::select("SELECT a.admin,a.no_nota, a.tgl, c.nm_suplier, b.suplier_akhir, a.debit, a.kredit,
        d.nm_akun
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$r->no_nota'
        group by a.id_bayar_bk;");

        $data = [
            'bayar' => $bayar
        ];

        return view('pembayaran_bk.getkredit', $data);
    }

    public function exportBayarbk(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $total = DB::selectOne("SELECT count(a.id_invoice_bk) as jumlah FROM invoice_bk as a where a.tgl between '$tgl1' and '$tgl2' ");

        $totalrow = $total->jumlah;

        return Excel::download(new ExportbayarBK($tgl1, $tgl2, $totalrow), 'pembayaran_bk.xlsx');
    }
}
