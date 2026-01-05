<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BkinExport;
use App\Exports\BkinExport2;
use App\Exports\BkinExportM;
use App\Models\User;
use SettingHal;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use App\Models\GudangBkModel;

class PembelianBahanBakuController extends Controller
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

        if (empty($r->page)) {
            $page = 'pembelian';
        } else {
            $page = $r->page;
        }



        if ($page == 'pembelian') {
            $where = "";
        } elseif ($page == 'belum_grading') {
            $where = "AND d.no_nota IS NULL";
        } elseif ($page == 'belum_harga') {
            $where = "AND d.no_nota IS NOT NULL and e.approve = 'T'";
        } else {
            $where = "AND d.no_nota IS NOT NULL and e.approve = 'Y'";
        }
        $pembelian = DB::select("SELECT a.id_invoice_bk, a.no_lot, a.tgl, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit, a.approve, d.no_nota as nota_grading, if(e.approve = 'Y',f.rupiah_approve,e.rupiah) as rupiah , e.no_nota as nota_bk_campur, e.approve, f.rupiah_approve, b.nm_suplier
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        left join grading as d on d.no_nota = a.no_nota
        left join (
            SELECT e.no_nota, sum(e.rupiah) as rupiah, e.approve
            FROM buku_campur as e
            group by e.no_nota
        ) as e on e.no_nota = a.no_nota
        
        left join (
            SELECT e.no_lot, sum(e.rupiah) as rupiah_approve
            FROM buku_campur_approve as e
            group by e.no_lot
        ) as f on f.no_lot = a.no_lot
        where a.tgl between '$tgl1' and '$tgl2' $where
        order by a.no_nota DESC;");

        $listBulan = DB::table('bulan')->get();
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Pembelian Bahan Baku',
            'pembelian' => $pembelian,
            'listbulan' => $listBulan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'user' => User::where('posisi_id', 1)->get(),
            'halaman' => 2,
            'create' => SettingHal::btnHal(9, $id_user),
            'export' => SettingHal::btnHal(8, $id_user),
            'approve' => SettingHal::btnHal(10, $id_user),
            'edit' => SettingHal::btnHal(11, $id_user),
            'delete' => SettingHal::btnHal(12, $id_user),
            'print' => SettingHal::btnHal(13, $id_user),
            'grading' => SettingHal::btnHal(14, $id_user),
            'page' => $page

        ];
        return view('pembelian_bk.index', $data);
    }
    function add_new()
    {
        return response()->json(['url' => route('pembelian_bk.add')]);
    }

    public function add(Request $r)
    {
        // 1. Logika Nota
        $b = date('m');
        $max = DB::table('pembelian')->whereMonth('tgl', $b)->latest('urutan_nota')->first();

        if (empty($max)) {
            $nota_t = '1';
        } else {
            $nota_t = $max->urutan_nota + 1;
        }

        // 2. Ambil Data Tahun (DIBENERIN DISINI)
        $currentYear = date("Y");
        $yearData = DB::table('tahun')->where('tahun', $currentYear)->first();

        // Cek apakah tahun ada di database?
        if (!$yearData) {
            // Opsi A: Return error ke user
            return redirect()->back()->with('error', "Data Tahun $currentYear belum disetting di database 'tahun'.");

            // Opsi B: Debugging (uncomment jika ingin lihat error langsung)
            // dd("Data tahun $currentYear tidak ditemukan!");
        }

        // 3. Ambil Data Bulan (DIBENERIN DISINI)
        $currentMonth = date('m');
        $bulanData = DB::table('bulan')->where('bulan', $currentMonth)->first();

        // Cek apakah bulan ada di database?
        if (!$bulanData) {
            return redirect()->back()->with('error', "Data Bulan $currentMonth belum disetting di database 'bulan'.");
        }

        // 4. Generate SUB PO (Aman karena sudah dicek)
        // Menggunakan $yearData dan $bulanData bukan $year/$bulan untuk menghindari konflik nama
        $sub_po = "BI" . $yearData->kode . $bulanData->kode . str_pad($nota_t, 3, '0', STR_PAD_LEFT);

        // 5. Logika No Lot
        $max_lot = DB::table('pembelian')->latest('no_lot')->first();

        if (empty($max_lot->no_lot)) {
            $max_l = '1001';
        } else {
            $max_l = $max_lot->no_lot + 1;
        }

        $data =  [
            'title' => 'Tambah Bahan Baku',
            'suplier' => DB::table('tb_suplier')->get(),
            'nota' => $nota_t,
            'produk' => DB::table('tb_produk')->get(),
            'bulan' => $bulanData, // Pakai variabel yang sudah divalidasi
            'akun' => DB::table('akun')->get(),
            'sub_po' => $sub_po,
            'no_lot' => $max_l
        ];

        return view('pembelian_bk.add', $data);
    }

    public function get_satuan_produk(Request $r)
    {
        $id_produk = $r->id_produk;
        $produk = DB::table('tb_produk')->where('id_produk', $id_produk)->first();
        $satuan = DB::table('tb_satuan')->where('id_satuan', $produk->satuan_id)->get();

        foreach ($satuan as $k) {
            echo "<option value='" . $k->id_satuan  . "'>" . $k->nm_satuan . "</option>";
        }
    }

    public function tambah_baris_bk(Request $r)
    {
        $data =  [
            'produk' => DB::table('tb_produk')->get(),
            'count' => $r->count

        ];
        return view('pembelian_bk.tambah_baris', $data);
    }

    public function save_pembelian_bk(Request $r)
    {
        $tgl = $r->tgl;
        $suplier_awal = $r->suplier_awal;
        $suplier_akhir = $r->suplier_akhir;
        $id_produk = $r->id_produk;
        $qty = $r->qty;
        $id_satuan = $r->id_satuan;
        $h_satuan = $r->h_satuan;
        $total_harga = $r->total_harga;

        $year = date("Y", strtotime($tgl));
        $b = date('m', strtotime($tgl));
        $max = DB::table('pembelian')->whereMonth('tgl', $b)->whereYear('tgl', $year)->latest('urutan_nota')->first();

        $year = DB::table('tahun')->where('tahun', $year)->first();
        if (empty($max)) {
            $nota_t = '1';
        } else {
            $nota_t = $max->urutan_nota + 1;
        }
        $bulan = DB::table('bulan')->where('bulan', $b)->first();
        $sub_po = "BI$year->kode" . "$bulan->kode" . str_pad($nota_t, 3, '0', STR_PAD_LEFT);

        $max_lot = DB::table('pembelian')->latest('no_lot')->first();

        if (empty($max_lot->no_lot)) {
            $max_l = '1001';
        } else {
            $max_l = $max_lot->no_lot + 1;
        }

        for ($x = 0; $x < count($id_produk); $x++) {
            $data = [
                'tgl' => $tgl,
                'no_nota' => $sub_po,
                'id_produk' => $id_produk[$x],
                // 'suplier_awal' => $suplier_awal,
                // 'suplier_akhir' => $suplier_akhir,
                'no_lot' => $max_l,
                'qty' => $qty[$x],
                'h_satuan' => $h_satuan[$x],
                'urutan_nota' => $nota_t,
                'admin' => Auth::user()->name,
                'ket' => $r->ket[$x]
            ];

            DB::table('pembelian')->insert($data);
        }


        $button = $r->button;
        if ($button == 'simpan') {
            $data = [
                'id_suplier' => $suplier_awal,
                'tgl' => $tgl,
                'no_nota' => $sub_po,
                'no_lot' => $max_l,
                'suplier_akhir' => $suplier_akhir,
                'total_harga' => $total_harga,
                'tgl_bayar' => '0000-00-00',
                'lunas' => 'T',
                'admin' => Auth::user()->name,
            ];
            DB::table('invoice_bk')->insert($data);
        } else {
            $data = [
                'id_suplier' => $suplier_awal,
                'tgl' => $tgl,
                'no_nota' => $sub_po,
                'no_lot' => $max_l,
                'suplier_akhir' => $suplier_akhir,
                'total_harga' => $total_harga,
                'tgl_bayar' => '0000-00-00',
                'lunas' => 'D',
                'admin' => Auth::user()->name,
            ];
            DB::table('invoice_bk')->insert($data);
        }

        if (empty($r->debit_tambahan) || $r->debit_tambahan == '0') {
            # code...
        } else {
            $data_tambahan = [
                'no_nota' => $sub_po,
                'debit' => $r->debit_tambahan,
                'kredit' => 0,
                'id_akun' => $r->id_akun_lainnya,
                'tgl' => $tgl,
                'admin' => Auth::user()->name,
                'ket' => $r->ket_lainnya
            ];
            DB::table('bayar_bk')->insert($data_tambahan);
        }
        $tgl1 = date('Y-m-01', strtotime($r->tgl));
        $tgl2 = date('Y-m-t', strtotime($r->tgl));
        return redirect()->route('pembelian_bk', ['period' => 'costume', 'tgl1' => $tgl1, 'tgl2' => $tgl2])->with('sukses', 'Data berhasil ditambahkan');
    }




    public function print(Request $r)
    {
        $pembelian = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$r->no_nota'
        ");

        $produk = DB::select("SELECT * FROM pembelian as a 
        left join tb_produk as b on b.id_produk = a.id_produk 
        left join tb_satuan as c on c.id_satuan = b.satuan_id
        WHERE a.no_nota ='$r->no_nota'");

        $bayar = DB::selectOne("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$r->no_nota' and a.id_akun = '35'
        group by a.id_bayar_bk;");

        $data =  [
            'title' => 'Print Bahan Baku',
            'pembelian' => $pembelian,
            'produk' => $produk,
            'bayar' => $bayar

        ];
        return view('pembelian_bk.print', $data);
    }

    public function delete_bk(Request $r)
    {
        $nota = $r->no_nota;
        DB::table('invoice_bk')->where('no_nota', $nota)->delete();
        DB::table('bayar_bk')->where('no_nota', $nota)->delete();
        DB::table('pembelian')->where('no_nota', $nota)->delete();
        DB::table('grading')->where('no_nota', $nota)->delete();


        return redirect()->back()->with('sukses', 'Data berhasil ditambahkan');
    }

    public function edit_pembelian_bk(Request $r)
    {
        $nota = $r->nota;

        $invoice = DB::table('invoice_bk')->where('no_nota', $nota)->first();
        $invoice2 = DB::table('bayar_bk')->where(['no_nota' => $nota, 'bayar' => 'T'])->first();
        $gram = DB::table('pembelian')->where('no_nota', $nota)->get();
        $data =  [
            'title' => 'Edit Bahan Baku',
            'suplier' => DB::table('tb_suplier')->get(),
            'produk' => DB::table('tb_produk')->get(),
            'nota' => $nota,
            'invoice' => $invoice,
            'invoice2' => $invoice2,
            'gram' => $gram,
            'akun' => DB::table('akun')->get()

        ];
        return view('pembelian_bk.edit', $data);
    }

    public function edit_save(Request $r)
    {
        $tgl = $r->tgl;
        $suplier_awal = $r->suplier_awal;
        $suplier_akhir = $r->suplier_akhir;
        $id_produk = $r->id_produk;
        $qty = $r->qty;
        $id_satuan = $r->id_satuan;
        $h_satuan = $r->h_satuan;
        $total_harga = $r->total_harga;

        $nota = $r->no_nota;
        $urutan_nota = $r->urutan_nota;

        DB::table('pembelian')->where('no_nota', $nota)->delete();
        // DB::table('invoice_bk')->where('no_nota', $nota)->delete();
        DB::table('bayar_bk')->where(['no_nota' => $nota, 'bayar' => 'T'])->delete();

        for ($x = 0; $x < count($id_produk); $x++) {
            $data = [
                'tgl' => $tgl,
                'no_nota' => $nota,
                'id_produk' => $id_produk[$x],
                'no_lot' => $r->no_lot,
                // 'suplier_awal' => $suplier_awal,
                // 'suplier_akhir' => $suplier_akhir,
                'qty' => $qty[$x],
                'h_satuan' => $h_satuan[$x],
                'urutan_nota' => $urutan_nota,
                'admin' => Auth::user()->name,
            ];

            DB::table('pembelian')->insert($data);
        }


        $button = $r->button;
        if ($button == 'simpan') {
            $data = [
                'id_suplier' => $suplier_awal,
                'tgl' => $tgl,
                'no_nota' => $nota,
                'no_lot' => $r->no_lot,
                'suplier_akhir' => $suplier_akhir,
                'total_harga' => $total_harga,
                'tgl_bayar' => '0000-00-00',
                'lunas' => 'T',
                'approve_bk_campur' => $r->approve_bk_campur,
                'admin' => Auth::user()->name,
            ];
            DB::table('invoice_bk')->where('no_nota', $nota)->update($data);
        } else {
            $data = [
                'id_suplier' => $suplier_awal,
                'tgl' => $tgl,
                'no_nota' => $nota,
                'no_lot' => $r->no_lot,
                'suplier_akhir' => $suplier_akhir,
                'total_harga' => $total_harga,
                'tgl_bayar' => '0000-00-00',
                'lunas' => 'D',
                'approve_bk_campur' => $r->approve_bk_campur,
                'admin' => Auth::user()->name,
            ];
            DB::table('invoice_bk')->where('no_nota', $nota)->update($data);
        }
        if (empty($r->debit_tambahan) || $r->debit_tambahan == '0') {
            # code...
        } else {
            $data_tambahan = [
                'no_nota' => $nota,
                'debit' => $r->debit_tambahan,
                'kredit' => 0,
                'id_akun' => $r->id_akun_lainnya,
                'tgl' => $tgl,
                'admin' => Auth::user()->name,
                'ket' => $r->ket_lainnya
            ];
            DB::table('bayar_bk')->insert($data_tambahan);
        }

        $tgl1 = date('Y-m-01', strtotime($r->tgl));
        $tgl2 = date('Y-m-t', strtotime($r->tgl));
        return redirect()->route('pembelian_bk', ['period' => 'costume', 'tgl1' => $tgl1, 'tgl2' => $tgl2])->with('sukses', 'Data berhasil ditambahkan');
    }

    public function grading(Request $r)
    {
        DB::table('grading')->where('no_nota', $r->no_nota)->delete();
        DB::table('buku_campur')->where('no_nota', $r->no_nota)->delete();
        $data = [
            'tgl' => $r->tgl,
            'no_nota' => $r->no_nota,
            'no_campur' => $r->no_campur,
            'gr_basah' => $r->gr_basah,
            'pcs_awal' => $r->pcs_awal,
            'gr_kering' => $r->gr_kering
        ];
        DB::table('grading')->insert($data);

        for ($x = 0; $x < count($r->id_grade); $x++) {
            if ($r->pcs[$x] + $r->gr[$x] == 0) {
                # code...
            } else {
                $data = [
                    'id_buku_campur' => $r->id_buku_campur[$x],
                    'id_grade' => $r->id_grade[$x],
                    'no_nota' => $r->no_nota,
                    'no_lot' => $r->no_lot,
                    'pcs' => $r->pcs[$x],
                    'gr' => $r->gr[$x],
                    'admin' => Auth::user()->name,
                    'rupiah' => $r->rupiah[$x]
                ];
                DB::table('buku_campur')->insert($data);
            }
        }

        $tgl1 = date('Y-m-01', strtotime($r->tgl_nota));
        $tgl2 = date('Y-m-t', strtotime($r->tgl_nota));
        return redirect()->route('pembelian_bk', ['period' => 'costume', 'tgl1' => $tgl1, 'tgl2' => $tgl2])->with('sukses', 'Data berhasil ditambahkan');
    }


    public function approve_invoice_bk(Request $r)
    {

        if ($r->submit == 'export') {
            $style_atas = array(
                'font' => [
                    'bold' => true, // Mengatur teks menjadi tebal
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ]
                ],
            );

            $style = [
                'borders' => [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ],
                ],
            ];
            $spreadsheet = new Spreadsheet();

            $spreadsheet->setActiveSheetIndex(0);
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Invoice BK');
            $sheet1->getStyle('A1:V1')->applyFromArray($style_atas);

            $akun_kas = DB::table('akun')->where('id_akun', '4')->first();
            $akun_bca = DB::table('akun')->where('id_akun', '30')->first();
            $akun_mandiri = DB::table('akun')->where('id_akun', '10')->first();
            $akun_bca22 = DB::table('akun')->where('id_akun', '6')->first();

            $sheet1->setCellValue('A1', '#');
            $sheet1->setCellValue('B1', 'Tanggal');
            $sheet1->setCellValue('C1', 'Nota BK');
            $sheet1->setCellValue('D1', 'Nota Lot');
            $sheet1->setCellValue('E1', 'Suplier Awal');
            $sheet1->setCellValue('F1', 'Suplier Akhir');
            $sheet1->setCellValue('G1', 'Keterangan');
            $sheet1->setCellValue('H1', 'Gr Beli');
            $sheet1->setCellValue('I1', 'Rp/gr');
            $sheet1->setCellValue('J1', 'Total Nota Bk');
            $sheet1->setCellValue('K1', 'Gr Basah');
            $sheet1->setCellValue('L1', 'Pcs Awal');
            $sheet1->setCellValue('M1', 'Gr Kering');
            $sheet1->setCellValue('N1', 'Susut');
            $sheet1->setCellValue('O1', 'No Buku Campur');
            $sheet1->setCellValue('P1', 'TGL Grade');
            $sheet1->setCellValue('Q1', 'Status');
            $sheet1->setCellValue('R1', $akun_kas->nm_akun);
            $sheet1->setCellValue('S1', $akun_bca->nm_akun);
            $sheet1->setCellValue('T1', $akun_mandiri->nm_akun);
            $sheet1->setCellValue('U1', $akun_bca22->nm_akun);
            $sheet1->setCellValue('V1', 'Sisa Hutang');

            $kolom = 2;

            $tes = $r->ceknota_excel;
            uasort($tes, function ($a, $b) {
                return strcmp($a, $b);
            });
            foreach ($tes as $nota) {
                $pembelian = DB::selectOne("SELECT a.id_invoice_bk, a.tgl, a.no_nota, a.no_lot, a.suplier_akhir, c.gr_basah, c.pcs_awal, c.gr_kering, a.total_harga, f.total_harga as ttl_hrg, a.approve_bk_campur, g.gr_basah as gr_basah_apr, g.pcs_awal as pcs_awal_apr, g.gr_kering as gr_kering_apr, e.gr_beli, c.tgl as tgl_grading, c.no_campur, a.lunas, b.nm_suplier
                FROM invoice_bk as a 
                left join tb_suplier as  b on b.id_suplier = a.id_suplier
                left join grading as c on c.no_nota = a.no_nota
                Left join(
                SELECT e.no_nota, sum(e.qty) as gr_beli
                    FROM pembelian as e
                    group by e.no_nota
                ) as e on e.no_nota = a.no_nota
                left join invoice_bk_approve as f on f.no_nota = a.no_nota
                left join grading_approve as g on g.no_nota = a.no_nota
                where a.no_nota = '$nota'
                order by a.no_nota ASC
                ");
                $kas = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
                FROM bayar_bk as a
                where a.no_nota = '$nota' and a.id_akun = '4'
                group by a.no_nota;");
                $bca = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
                FROM bayar_bk as a
                where a.no_nota = '$nota' and a.id_akun = '30'
                group by a.no_nota;");
                $mandiri = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
                FROM bayar_bk as a
                where a.no_nota = '$nota' and a.id_akun = '10'
                group by a.no_nota;");

                $bca22 = DB::selectOne("SELECT a.no_nota, a.id_akun, sum(a.kredit) as bayar
                FROM bayar_bk as a
                where a.no_nota = '$nota' and a.id_akun = '6'
                group by a.no_nota;");

                $sheet1->setCellValue('A' . $kolom, $pembelian->id_invoice_bk);
                $sheet1->setCellValue('B' . $kolom, $pembelian->tgl);
                $sheet1->setCellValue('C' . $kolom, $pembelian->no_nota);
                $sheet1->setCellValue('D' . $kolom, $pembelian->no_lot);
                $sheet1->setCellValue('E' . $kolom, $pembelian->nm_suplier);
                $sheet1->setCellValue('F' . $kolom, $pembelian->suplier_akhir);
                $sheet1->setCellValue('G' . $kolom, '');
                $sheet1->setCellValue('H' . $kolom, $pembelian->gr_beli);
                if ($pembelian->gr_beli == 0) {
                    $rp_gr = 0;
                } else {
                    $rp_gr = $pembelian->approve_bk_campur == 'Y' ? $pembelian->ttl_hrg / $pembelian->gr_beli : $pembelian->total_harga / $pembelian->gr_beli;
                }
                $sheet1->setCellValue('I' . $kolom, $rp_gr);
                $sheet1->setCellValue('J' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->ttl_hrg : $pembelian->total_harga);
                $sheet1->setCellValue('K' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->gr_basah_apr : $pembelian->gr_basah);
                $sheet1->setCellValue('L' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->pcs_awal_apr : $pembelian->pcs_awal);
                $sheet1->setCellValue('M' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->gr_kering_apr :  $pembelian->gr_kering);
                if ($pembelian->gr_beli == 0) {
                    $sheet1->setCellValue('N' . $kolom,  '0%');
                } else {
                    $sheet1->setCellValue('N' . $kolom, $pembelian->approve_bk_campur == 'Y' ? round((1 - $pembelian->gr_beli / $pembelian->gr_kering_apr) * 100) : round((1 - $pembelian->gr_beli /  $pembelian->gr_kering) * -100) . '%');
                }

                $sheet1->setCellValue('O' . $kolom, $pembelian->no_campur);
                $sheet1->setCellValue('P' . $kolom, $pembelian->tgl_grading);
                $kas2 = empty($kas->bayar) ? '0' : $kas->bayar;
                $bca2 = empty($bca->bayar) ? '0' : $bca->bayar;
                $bca222 = empty($bca22->bayar) ? '0' : $bca22->bayar;
                $mandiri2 = empty($mandiri->bayar) ? '0' : $mandiri->bayar;
                $sheet1->setCellValue('Q' . $kolom, $pembelian->lunas == 'D' ? 'Draft' : ($pembelian->total_harga - $kas2 - $bca2 - $mandiri2 - $bca222 <= 0 ? 'Paid' : 'Unpaid'));
                $sheet1->setCellValue('R' . $kolom,  empty($kas->bayar) ? '0' : $kas->bayar);
                $sheet1->setCellValue('S' . $kolom,  empty($bca->bayar) ? '0' : $bca->bayar);
                $sheet1->setCellValue('T' . $kolom,  empty($mandiri->bayar) ? '0' : $mandiri->bayar);
                $sheet1->setCellValue('U' . $kolom,  $bca222);
                $sheet1->setCellValue('V' . $kolom,  $pembelian->total_harga - $kas2 - $bca2 - $mandiri2 - $bca222);

                $kolom++;
            }
            $sheet1->getStyle('A2:V' . $kolom - 1)->applyFromArray($style);

            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2 = $spreadsheet->getActiveSheet(1);
            $sheet2->setTitle('Buku Campur');
            $sheet2->getStyle('A1:L1')->applyFromArray($style_atas);

            $sheet2->setCellValue('A1', 'ID BK Campur');
            $sheet2->setCellValue('B1', 'no buku');
            $sheet2->setCellValue('C1', 'Suplier Awal');
            $sheet2->setCellValue('D1', 'Suplier Akhir');
            $sheet2->setCellValue('E1', 'DATE');
            $sheet2->setCellValue('F1', 'GRD');
            $sheet2->setCellValue('G1', 'pcs-gdg');
            $sheet2->setCellValue('H1', 'gr-gdg');
            $sheet2->setCellValue('I1', 'rp/gr-gdg');
            $sheet2->setCellValue('J1', 'lot no');
            $sheet2->setCellValue('K1', 'Keterangan');
            $sheet2->setCellValue('L1', 'ttlrp-gdg');

            $kolom2 = 2;
            foreach ($tes as $nota) {
                $buku_campur = DB::select("SELECT 
                a.id_buku_campur, a.approve,
                if(a.approve = 'T',c.tgl,d.tgl) as tgl, 
                a.no_lot,  a.gudang, a.gabung,
                if(a.approve = 'T',b.nm_grade,d.nm_grade) as nm_grade, 
                if(a.approve = 'T',c.no_campur,d.buku) as buku, 
                e.suplier_akhir as suplier, 
                f.nm_suplier as nm_suplier,
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
                where a.no_nota = '$nota'
                order by a.no_nota ASC, b.urutan ASC;");
                // $buku_campur = GudangBkModel::getPembelianBkExportnota($nota);


                foreach ($buku_campur as $b) {
                    $sheet2->setCellValue('A' . $kolom2, $b->id_buku_campur);
                    $sheet2->setCellValue('B' . $kolom2, $b->buku);
                    $sheet2->setCellValue('C' . $kolom2, $b->nm_suplier);
                    $sheet2->setCellValue('D' . $kolom2, $b->suplier);
                    $sheet2->setCellValue('E' . $kolom2, $b->tgl);
                    $sheet2->setCellValue('F' . $kolom2, $b->nm_grade);
                    $sheet2->setCellValue('G' . $kolom2, $b->pcs);
                    $sheet2->setCellValue('H' . $kolom2, $b->gr);
                    $sheet2->setCellValue('I' . $kolom2, $b->rupiah);
                    $sheet2->setCellValue('J' . $kolom2, $b->no_lot);
                    $sheet2->setCellValue('K' . $kolom2, $b->ket);
                    $sheet2->setCellValue('L' . $kolom2, $b->rupiah * $b->gr);
                    $kolom2++;
                }
            }
            $sheet2->getStyle('A2:L' . $kolom2 - 1)->applyFromArray($style);




            $namafile = "Buku Campur.xlsx";

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $namafile);
            header('Cache-Control: max-age=0');


            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit();
        } else {
            for ($x = 0; $x < count($r->ceknota); $x++) {
                $data = [
                    'approve' => "Y"
                ];
                DB::table('invoice_bk')->where('no_nota', $r->ceknota[$x])->update($data);

                DB::table('invoice_bk')->where('no_nota', $r->ceknota[$x])->update(['approve_bk_campur' => 'Y']);
                DB::table('grading')->where('no_nota', $r->ceknota[$x])->update(['approve' => 'Y']);

                $invoice = DB::table('invoice_bk')->where('no_nota', $r->ceknota[$x])->first();
                $grading = DB::table('grading')->where('no_nota', $r->ceknota[$x])->first();

                $data = [
                    'no_nota' => $r->ceknota[$x],
                    'no_lot' => $invoice->no_lot,
                    'total_harga' => $invoice->total_harga
                ];
                DB::table('invoice_bk_approve')->insert($data);

                $data = [
                    'no_nota' => $r->ceknota[$x],
                    'tgl' => $grading->tgl,
                    'no_campur' => $grading->no_campur,
                    'gr_basah' => $grading->gr_basah,
                    'pcs_awal' => $grading->pcs_awal,
                    'gr_kering' => $grading->gr_kering,
                ];
                DB::table('grading_approve')->insert($data);
            }
            return redirect()->route('pembelian_bk')->with('sukses', 'Data berhasil diapprove');
        }
    }

    public function get_grading(Request $r)
    {
        $data = [
            'grading' => DB::selectOne("SELECT sum(a.qty) as qty, b.tgl, b.no_nota, b.no_campur, b.gr_basah, b.pcs_awal, b.gr_kering
            FROM pembelian as a  
            left join grading as b on b.no_nota = a.no_nota
            where a.no_nota = '$r->no_nota'
            group by a.no_nota;"),
            'invoice' => DB::table('invoice_bk')->where('no_nota', $r->no_nota)->first(),
            'buku_campur' => DB::select("SELECT b.nm_grade, if(a.approve = 'Y', c.pcs, a.pcs) as pcs, if(a.approve = 'Y',c.gr,a.gr) as gr , if(a.approve = 'Y',c.rupiah, a.rupiah) as rupiah
            FROM buku_campur as a
            left join grade as b on b.id_grade = a.id_grade
            left join buku_campur_approve as c on c.id_buku_campur = a.id_buku_campur
            where a.no_nota = '$r->no_nota'
             ")
        ];

        return view('pembelian_bk.grading', $data);
    }
    public function get_grading2(Request $r)
    {
        $pembelian = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$r->no_nota'
        ");

        $produk = DB::select("SELECT * FROM pembelian as a 
        left join tb_produk as b on b.id_produk = a.id_produk 
        left join tb_satuan as c on c.id_satuan = b.satuan_id
        WHERE a.no_nota ='$r->no_nota'");
        $data = [
            'grading' => DB::table('grading')->where('no_nota', $r->no_nota)->first(),
            'invoice' => DB::table('invoice_bk')->where('no_nota', $r->no_nota)->first(),
            'grade' => DB::table('grade')->get(),
            'pembelian' => $pembelian,
            'produk' => $produk
        ];

        return view('pembelian_bk.grading2', $data);
    }

    public function nota_invoice_bk(Request $r)
    {
        $b = date('m', strtotime($r->tgl));
        $year = date("Y", strtotime($r->tgl));
        $max = DB::table('pembelian')->whereMonth('tgl', $b)->whereYear('tgl', $year)->latest('urutan_nota')->first();


        $year = DB::table('tahun')->where('tahun', $year)->first();
        if (empty($max)) {
            $nota_t = '1';
        } else {
            $nota_t = $max->urutan_nota + 1;
        }
        $bulan = DB::table('bulan')->where('bulan', $b)->first();
        $sub_po = "BI$year->kode" . "$bulan->kode" . str_pad($nota_t, 3, '0', STR_PAD_LEFT);

        echo "$sub_po";
    }
    public function export_bk(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $total = DB::selectOne("SELECT count(a.id_invoice_bk) as jumlah FROM invoice_bk as a where a.tgl between '$tgl1' and '$tgl2' ");

        $totalrow = $total->jumlah;

        return Excel::download(new BkinExport($tgl1, $tgl2, $totalrow), 'pembelian_bk.xlsx');
    }
    public function export_bk_m(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $total = DB::selectOne("SELECT count(a.id_invoice_bk) as jumlah FROM invoice_bk as a where a.tgl between '$tgl1' and '$tgl2' ");
        $totalrow = $total->jumlah;
        $pembelian = DB::select("SELECT a.id_invoice_bk, a.no_lot, a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit, a.approve, d.no_nota as nota_grading,e.gr_beli, d.gr_basah, d.gr_kering, d.pcs_awal, d.tgl as tgl_grading, d.no_campur
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            left join grading as d on d.no_nota = a.no_nota
            left join(
            SELECT e.no_nota, sum(e.qty) as gr_beli
                FROM pembelian as e
                group by e.no_nota
            ) as e on e.no_nota = a.no_nota
            where a.tgl between '$tgl1' and '$tgl2'
            order by a.no_nota ASC");

        return Excel::download(new BkinExportM($tgl1, $tgl2, $totalrow, $pembelian), 'pembelian_bk.xlsx');
    }

    function delete_tipe_grade(Request $r)
    {
        DB::table('grade')->where('id_grade', $r->id_grade)->update(['aktif' => 'Y']);
    }

    function load_grade(Request $r)
    {
        $data = [
            'grade' => DB::table('grade')->where('aktif', 'T')->orderBy('urutan', 'ASC')->get(),
            'no_nota' => $r->no_nota,
            'invoice' => DB::table('invoice_bk')->where('no_nota', $r->no_nota)->first()
        ];
        return view('pembelian_bk.load_grade', $data);
    }

    function save_grade(Request $r)
    {
        $data = [
            'nm_grade' => $r->nm_grade
        ];
        DB::table('grade')->insert($data);
    }

    public function get_print(Request $r)
    {
        $pembelian = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$r->no_nota'
        ");

        $produk = DB::select("SELECT * FROM pembelian as a 
        left join tb_produk as b on b.id_produk = a.id_produk 
        left join tb_satuan as c on c.id_satuan = b.satuan_id
        WHERE a.no_nota ='$r->no_nota'");

        $bayar = DB::selectOne("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$r->no_nota' and a.id_akun = '35'
        group by a.id_bayar_bk;");

        $data = [
            'pembelian' => $pembelian,
            'produk' => $produk,
            'bayar' => $bayar
        ];
        return view('pembelian_bk.get_print', $data);
    }
}
