<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\Jurnal;
use App\Models\Stok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SettingHal;

class JurnalPenyesuaianController extends Controller
{
    protected $tgl1, $tgl2, $period;
    public function __construct(Request $r)
    {
        if (empty($r->period)) {
            $this->tgl1 = date('Y-m-01');
            $this->tgl2 = date('Y-m-t');
        } elseif ($r->period == 'daily') {
            $this->tgl1 = date('Y-m-d');
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'weekly') {
            $this->tgl1 = date('Y-m-d', strtotime("-6 days"));
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'mounthly') {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
            $tglawal = "$tahun" . "-" . "$bulan" . "-" . "01";
            $tglakhir = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tglawal));
            $this->tgl2 = date('Y-m-t', strtotime($tglakhir));
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
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '4')->first();
        $max_tgl = DB::selectOne("SELECT max(a.tgl) as tgl FROM depresiasi_aktiva as a");
        $max_tgl_akt = DB::selectOne("SELECT min(a.tgl) as tgl FROM aktiva as a");
        if (empty($max_tgl->tgl)) {
            $tgl = date('Y-m-t', strtotime($max_tgl_akt->tgl));
            $tgl1 = date('Y-m-01', strtotime($tgl));
        } else {
            $tgl_asli = date('Y-m-01', strtotime($max_tgl->tgl));
            $tgl = date('Y-m-t', strtotime("next month", strtotime($tgl_asli)));
            $tgl1 = date('Y-m-01', strtotime($tgl));
        }


        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->nomor_nota + 1;
        }
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Jurnal Penyesuaian',
            'nota' => $nota_t,
            'akun' => DB::table('akun')->get(),
            'aktiva' => DB::select("SELECT a.*, c.beban FROM aktiva as a 
            left join kelompok_aktiva as b on b.id_kelompok = a.id_kelompok
            left join(
            SELECT sum(c.b_penyusutan) as beban , c.id_aktiva
                FROM depresiasi_aktiva as c
                group by c.id_aktiva
            ) as c on c.id_aktiva = a.id_aktiva
            where a.tgl between '2017-01-01' and '$tgl' 
            order by a.tgl ASC
            "),
            'tgl' => $tgl,
            'user' => User::where('posisi_id', 1)->get(),
            'halaman' => 11,
            'create' => SettingHal::btnHal(46, $id_user),
            'export' => SettingHal::btnHal(47, $id_user),
            'detail' => SettingHal::btnHal(48, $id_user),
        ];
        return view('jurnal_penyesuaian.index', $data);
    }

    public function jurnal()
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;



        $jurnal =  DB::select("SELECT a.id_jurnal,a.no_urut,a.admin, a.id_akun, a.tgl, a.debit, a.kredit, a.ket,a.no_nota, b.nm_akun, c.nm_post, d.nm_proyek FROM jurnal as a 
            left join akun as b on b.id_akun = a.id_akun
            left join tb_post_center as c on c.id_post_center = a.id_post_center
            left join proyek as d on d.id_proyek = a.id_proyek
            where a.id_buku = '4' and a.tgl between '$tgl1' and '$tgl2' order by a.id_jurnal DESC");
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Jurnal Penyesuaian',
            'jurnal' => $jurnal,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'user' => User::where('posisi_id', 1)->get(),
            'halaman' => 11,
            'create' => SettingHal::btnHal(46, $id_user),
            'export' => SettingHal::btnHal(47, $id_user),
            'detail' => SettingHal::btnHal(48, $id_user),


        ];
        return view('jurnal_penyesuaian.jurnal', $data);
    }

    public function save_penyesuaian_aktiva(Request $r)
    {
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '4')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->nomor_nota + 1;
        }
        DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '4']);
        $data_kredit = [
            'tgl' => $r->tgl,
            'no_nota' => 'JPA-' . $nota_t,
            'id_akun' => 511,
            'id_buku' => '4',
            'ket' => 'Penyesuaian Aktiva',
            'kredit' => $r->debit_kredit,
            'debit' => '0',
            'admin' => Auth::user()->name,
            'kode_penyesuaian' => 'JPA'
        ];
        Jurnal::create($data_kredit);
        $data_debit = [
            'tgl' => $r->tgl,
            'no_nota' => 'JPA-' . $nota_t,
            'id_akun' => 510,
            'id_buku' => '4',
            'ket' => 'Penyesuaian Aktiva',
            'debit' => $r->debit_kredit,
            'kredit' => '0',
            'admin' => Auth::user()->name,
        ];
        Jurnal::create($data_debit);

        for ($x = 0; $x < count($r->id_aktiva); $x++) {
            $data = [
                'id_aktiva' => $r->id_aktiva[$x],
                'tgl' => $r->tgl,
                'b_penyusutan' => $r->b_penyusutan[$x],
                'admin' => 'import'
            ];
            DB::table('depresiasi_aktiva')->insert($data);
        }

        return redirect()->route('penyesuaian.aktiva')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function atk(Request $r, $gudang_id = null)
    {

        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();
        $max_tgl = DB::selectOne("SELECT max(a.tgl) as tgl FROM jurnal as a")->tgl;

        if (empty($max_tgl)) {
            $tgl = date('Y-m-t', strtotime(date('Y-m-d')));
        } else {
            $tgl1 = date('Y-m-01', strtotime($max_tgl));
            $tgl2 = date('Y-m-t', strtotime($max_tgl));
            $tgl = Carbon::parse($tgl1)->addMonth()->toDateString();
        }


        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->nomor_nota + 1;
        }

        $data =  [
            'title' => 'Jurnal Penyesuaian ATK',
            'nota' => $nota_t,
            'gudang' => Gudang::where('kategori_id', 1)->get(),
            'akunAtk' => DB::table('akun')->where('id_akun', 516)->first(),
            'akunBiaya' => DB::table('akun')->where('id_akun', 37)->first(),
            'atk' => DB::select("SELECT 
                        a.id_produk, 
                                a.kd_produk, 
                                a.gudang_id, 
                                a.nm_produk, 
                                a.admin,
                                f.debit,
                                (f.rp_satuan / f.debit) as rp_satuan,
                                f.kredit,
                                f.tgl as tgl1 
                        FROM tb_produk as a
                        LEFT join (
                                SELECT 
                                    max(b.tgl) as tgl, 
                                    b.id_produk, 
                                    b.urutan, 
                                    SUM(b.debit) as debit, 
                                    SUM(b.rp_satuan) as rp_satuan, 
                                    sum(b.kredit) as kredit 
                                FROM 
                                    tb_stok_produk as b 
                                where 
                                    b.jenis = 'selesai'
                                group by 
                                    b.id_produk
                                ) as f on f.id_produk = a.id_produk 
                        WHERE a.kategori_id = 1 AND f.debit != 0 AND f.tgl BETWEEN '2017-01-01' AND '$tgl';
            "),
            'tgl' => $tgl
        ];
        return view('jurnal_penyesuaian.atk.index', $data);
    }

    public function save_atk(Request $r)
    {
        DB::table('notas')->insert([
            'id_buku' => 2,
            'nomor_nota' => $r->urutan
        ]);
        $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', $r->id_akun_debit)->first();
        $akun = DB::table('akun')->where('id_akun', $r->id_akun_debit)->first();

        $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);

        $dataK = [
            'tgl' => $r->tgl,
            'no_nota' => $r->no_nota,
            'id_akun' => $r->id_akun_debit,
            'id_buku' => '2',
            'ket' => 'Penyesuaian Atk',
            'debit' => $r->debit_kredit,
            'no_urut' => $akun->inisial . '-' . $urutan,
            'urutan' => $urutan,
            'kredit' => 0,
            'admin' => auth()->user()->name,
            'kode_penyesuaian' => 'JPATK'
        ];
        Jurnal::create($dataK);

        $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', $r->id_akun_kredit)->first();
        $akun2 = DB::table('akun')->where('id_akun', $r->id_akun_kredit)->first();

        $urutan = empty($max_akun2) ? '1001' : ($max_akun2->urutan == 0 ? '1001' : $max_akun2->urutan + 1);
        $dataK = [
            'tgl' => $r->tgl,
            'no_nota' => $r->no_nota,
            'id_akun' => $r->id_akun_kredit,
            'id_buku' => '2',
            'ket' => 'Penyesuaian Atk',
            'kredit' => $r->debit_kredit,
            'debit' => 0,
            'no_urut' => $akun2->inisial . '-' . $urutan,
            'urutan' => $urutan,
            'admin' => auth()->user()->name,
            'kode_penyesuaian' => 'JPATK'
        ];
        Jurnal::create($dataK);

        $no_nota = buatNota('tb_stok_produk', 'urutan');
        for ($i = 0; $i < count($r->id_produk); $i++) {
            $total = $r->sisa[$i] - $r->fisik[$i];

            $debit = $total < 0 ? $total * -1 : 0;
            $kredit = $total < 0 ? 0 : $total;

            $data = [
                'id_produk' => $r->id_produk[$i],
                'tgl' => date('Y-m-d'),
                'urutan' => $no_nota,
                'no_nota' => 'OPN-' . $no_nota,
                'departemen_id' => '1',
                'kategori_id' => 1,
                'status' => 'opname',
                'jenis' => 'selesai',
                'gudang_id' => $r->gudang_id[$i],
                'jml_sebelumnya' => $r->sisa[$i],
                'jml_sesudahnya' => $r->fisik[$i],
                'selisih' => $r->fisik[$i] - $r->sisa[$i],
                'debit' => $debit,
                'kredit' => $kredit,
                'ket' => 'Opname Penyesuaian Atk',
                'rp_satuan' => '0',
                'admin' => auth()->user()->name,
            ];

            Stok::create($data);
        }
        return redirect()->route('opname.index')->with('sukses', 'Berhasil Penyesuaian Opname');
    }

    public function peralatan()
    {
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '4')->first();
        $max_tgl = DB::selectOne("SELECT max(a.tgl) as tgl FROM depresiasi_peralatan as a");
        $max_tgl_akt = DB::selectOne("SELECT min(a.tgl) as tgl FROM peralatan as a");
        if (empty($max_tgl->tgl)) {
            $tgl = date('Y-m-t', strtotime($max_tgl_akt->tgl));
            $tgl1 = date('Y-m-01', strtotime($tgl));
        } else {
            $tgl_asli = date('Y-m-01', strtotime($max_tgl->tgl));
            $tgl = date('Y-m-t', strtotime("next month", strtotime($tgl_asli)));
            $tgl1 = date('Y-m-01', strtotime($tgl));
        }

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->nomor_nota + 1;
        }
        $data =  [
            'title' => 'Jurnal Penyesuaian',
            'akunAtk' => DB::table('akun')->where('id_akun', 518)->first(),
            'akunBiaya' => DB::table('akun')->where('id_akun', 517)->first(),
            'nota' => $nota_t,
            'akun' => DB::table('akun')->get(),
            'aktiva' => DB::select("SELECT a.*, c.beban FROM peralatan as a 
            left join kelompok_peralatan as b on b.id_kelompok = a.id_kelompok
            left join(
            SELECT sum(c.b_penyusutan) as beban , c.id_aktiva
                FROM depresiasi_peralatan as c
                group by c.id_aktiva
            ) as c on c.id_aktiva = a.id_aktiva
            where a.tgl between '2017-01-01' and '$tgl' 
            order by a.tgl ASC
            "),
            'tgl' => $tgl
        ];

        return view('jurnal_penyesuaian.peralatan.index', $data);
    }

    public function save_peralatan(Request $r)
    {
        $admin = auth()->user()->name;
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '4')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->nomor_nota + 1;
        }
        DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '4']);

        $data_kredit = [
            'tgl' => $r->tgl,
            'no_nota' => 'JPP-' . $nota_t,
            'id_akun' => $r->id_akun_kredit,
            'id_buku' => '4',
            'ket' => 'Penyesuaian Peralatan',
            'kredit' => $r->debit_kredit,
            'debit' => '0',
            'admin' => $admin,
            'kode_penyesuaian' => 'JPP'
        ];
        Jurnal::create($data_kredit);

        $data_debit = [
            'tgl' => $r->tgl,
            'no_nota' => 'JPP-' . $nota_t,
            'id_akun' => $r->id_akun_debit,
            'id_buku' => '4',
            'ket' => 'Penyesuaian Peralatan',
            'debit' => $r->debit_kredit,
            'kredit' => '0',
            'admin' => $admin,
        ];
        Jurnal::create($data_debit);

        for ($x = 0; $x < count($r->id_aktiva); $x++) {
            $data = [
                'id_aktiva' => $r->id_aktiva[$x],
                'tgl' => $r->tgl,
                'b_penyusutan' => $r->b_penyusutan[$x],
                'admin' => $admin
            ];
            DB::table('depresiasi_peralatan')->insert($data);
        }

        return redirect()->route('penyesuaian.peralatan')->with('sukses', 'Data berhasil ditambahkan');
    }
}
