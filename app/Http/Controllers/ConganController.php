<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ConganController extends Controller
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
    public function index()
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;

        // Ambil grade data
        $grade = DB::table('grade_congan')
            ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
            ->where('aktif', 'Y')
            ->where('grade_congan.kategori_id', '!=', '14')
            ->orderBy('kategori.id', 'ASC')
            ->orderBy('grade_congan.urutan', 'ASC')
            ->get();
        $grade2 = DB::table('grade_congan')
            ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
            ->where('aktif', 'Y')
            ->where('grade_congan.kategori_id', '14')
            ->orderBy('kategori.id', 'ASC')
            ->orderBy('grade_congan.urutan', 'ASC')
            ->get();

        // Ambil congan data
        $congan = DB::select("SELECT a.*, b.ttl
        FROM invoice_congan as a
        LEFT JOIN (
            SELECT b.no_nota, b.ket, 
                   SUM((COALESCE(b.gr,0) * COALESCE(b.hrga,0)) + (COALESCE(b.gr_kuning,0) * COALESCE(b.hrga_kuning,0))) as ttl
            FROM tb_cong as b 
            GROUP BY b.no_nota, b.ket
        ) as b ON b.no_nota = a.no_nota AND b.ket = a.ket
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.no_nota, a.ket
        ORDER BY a.no_nota DESC
    ");

        // ⭐ PERBAIKAN: Ambil semua data grade dalam 1 query
        $gradeData = [];
        if (!empty($congan)) {
            // Buat array untuk IN clause
            $notaKets = [];
            foreach ($congan as $c) {
                $notaKets[] = "'{$c->no_nota}_{$c->ket}'";
            }
            $notaKetString = implode(',', $notaKets);

            // Query sekali untuk semua data
            $gradeResults = DB::select("
            SELECT 
                CONCAT(a.no_nota, '_', a.ket) as nota_ket_key,
                a.id_grade,
                (COALESCE(a.gr,0) + COALESCE(a.gr_kuning,0)) as gr
            FROM tb_cong as a 
            WHERE CONCAT(a.no_nota, '_', a.ket) IN ($notaKetString)
        ");

            // Reorganisasi data menjadi nested array
            foreach ($gradeResults as $result) {
                $gradeData[$result->nota_ket_key][$result->id_grade] = $result->gr;
            }
        }
        $data = [
            'title' => 'Cong-congan',
            'grade' => $grade,
            'grade2' => $grade2,
            'congan' => $congan,
            'gradeData' => $gradeData, // ⭐ Tambahkan data grade
            'tgl1' => $tgl1,
            'tgl2' => $tgl2
        ];
        return view('congan.indexnew', $data);
    }

    public function load_row(Request $r)
    {
        $data = [
            'grade' => DB::table('grade_congan')->where('aktif', 'Y')->orderBy('urutan', 'ASC')->get(),
            'count' => $r->count
        ];
        return view('congan.tambah_baris_new', $data);
    }

    public function detail_nota(Request $r)
    {
        $data = [
            'title' => 'Detail Nota',
            'no_nota' => $r->no_nota,
            'grade' => DB::table('grade_congan')
                ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
                ->where('aktif', 'Y')->orderBy('kategori.kelompok', 'ASC')
                ->where('grade_congan.kategori_id', '!=', '14')
                ->orderBy('kategori.urutan', 'ASC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')->get(),
            'grade2' => DB::table('grade_congan')
                ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
                ->where('aktif', 'Y')
                ->where('grade_congan.kategori_id', '14')
                ->orderBy('kategori.kelompok', 'ASC')->orderBy('kategori.urutan', 'ASC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')->get(),
            'congan' => DB::select("SELECT a.*
            FROM invoice_congan as a
            where a.no_nota = '$r->no_nota'
            group by a.no_nota, a.ket
            order by a.no_nota DESC
            "),
        ];
        return view('congan.detail_new', $data);
    }

    public function add_congan(Request $r)
    {
        $urutan = DB::selectOne("SELECT max(a.urutan) as urutan FROM tb_cong as a ");
        if (empty($urutan->urutan)) {
            $urutan = '1001';
        } else {
            $urutan = $urutan->urutan + 1;
        }

        for ($y = 0; $y < count($r->ket); $y++) {
            $count = $r->count;
            $ttl_gr = 0;
            $ttl_gr_kuning = 0;
            // $ttl_gr_beras = 0;

            $id_grade = $r->{"id_grade" . $count[$y]};
            $gr = $r->{"gr" . $count[$y]};
            // $gr_beras = $r->{"gr_beras" . $count[$y]};
            $gr_kuning = $r->{"gr_kuning" . $count[$y]};
            $harga = $r->{"harga" . $count[$y]};

            for ($x = 0; $x < count($id_grade); $x++) {
                $ttl_gr += $gr[$x];
                $ttl_gr_kuning += $gr_kuning[$x];
                // $ttl_gr_beras += $gr_beras[$x];
            }

            $data = [
                'tgl' => $r->tgl,
                'pemilik' => $r->pemilik,
                'ket' => $r->ket[$y],
                'persen_air' => $r->persen_air[$y],
                'hrga_beli' => $r->hrga_beli[$y],
                'no_nota' => $urutan,
                'gr' => $ttl_gr,
                // 'gr_beras' => $ttl_gr_beras,
                'gr_kuning' => $ttl_gr_kuning

            ];
            $idInvoiceCongan = DB::table('invoice_congan')->insertGetId($data);

            for ($x = 0; $x < count($id_grade); $x++) {
                $hrga_dlu = DB::table('tb_cong')
                    ->where('id_grade', $id_grade[$x])
                    ->where('no_nota', '!=', $urutan)
                    ->where('hrga', '!=', 0)
                    ->orderBy('no_nota', 'desc')
                    ->first();
                $hrga_dlu_kuning = DB::table('tb_cong')
                    ->where('id_grade', $id_grade[$x])
                    ->where('no_nota', '!=', $urutan)
                    ->where('hrga_kuning', '!=', 0)
                    ->orderBy('no_nota', 'desc')
                    ->first();
                if (!empty($gr[$x]) || !empty($gr_kuning[$x])) {
                    $data  = [
                        'tgl' => $r->tgl,
                        'id_grade' => $id_grade[$x],
                        'gr' => $gr[$x],
                        'gr_beras' => 0,
                        'gr_kuning' =>  $gr_kuning[$x],
                        'hrga' => 0,
                        'hrga_kuning' =>  0,
                        'hrga_beras' =>  0,
                        'urutan' => $urutan,
                        'no_nota' => $urutan,
                        'ket' => $r->ket[$y],
                        'id_invoice_congan' => $idInvoiceCongan
                    ];
                    DB::table('tb_cong')->insert($data);
                }
            }
        }

        return redirect()->route('congan.index')->with('sukses', 'Data berhasil disimpan');
    }

    public function edit_congan(Request $r)
    {


        try {
            DB::beginTransaction();

            $urutan = $r->no_nota;

            // Simpan data lama sebelum dihapus


            // Hapus data lama
            DB::table('tb_cong')->where('no_nota', $urutan)->delete();

            for ($y = 0; $y < count($r->pemilik); $y++) {
                $count = $r->count;
                $ttl_gr = 0;
                $ttl_gr_kuning = 0;
                // $ttl_gr_beras = 0;

                $id_grade = $r->{"id_grade" . $count[$y]};
                $gr = $r->{"gr" . $count[$y]};
                // $gr_beras = $r->{"gr_beras" . $count[$y]};
                $gr_kuning = $r->{"gr_kuning" . $count[$y]};


                $harga = $r->{"harga" . $count[$y]};
                // $harga_beras = $r->{"harga_beras" . $count[$y]};
                $harga_kuning = $r->{"harga_kuning" . $count[$y]};
                $nm_grade = $r->{"nm_grade" . $count[$y]};
                for ($x = 0; $x < count($id_grade); $x++) {
                    $ttl_gr += $gr[$x];
                    $ttl_gr_kuning += $gr_kuning[$x];
                    // $ttl_gr_beras += $gr_beras[$x];
                }
                $congan_selesai = DB::table('invoice_congan')->where('id_invoice_congan', $r->id_invoice_congan[$y])->first();

                $selesai = empty($r->selesai) ? $congan_selesai->selesai : $r->selesai;



                $data = [
                    'tgl' => $r->tgl[$y],
                    'pemilik' => $r->pemilik[$y],
                    'ket' => $r->ket[$y],
                    'persen_air' => $r->persen_air[$y],
                    'hrga_beli' => $r->hrga_beli[$y],
                    'no_nota' => $urutan,
                    'gr' => $ttl_gr,
                    'gr_beras' => 0,
                    'gr_kuning' => $ttl_gr_kuning,
                    'selesai' => $selesai,

                ];
                DB::table('invoice_congan')->where('id_invoice_congan', $r->id_invoice_congan[$y])->update($data);



                for ($x = 0; $x < count($id_grade); $x++) {

                    $data = [
                        'nm_grade' => $nm_grade[$x],
                    ];
                    DB::table('grade_congan')->where('id_grade_cong', $id_grade[$x])->update($data);


                    $data  = [
                        'tgl' => $r->tgl[$y],
                        'id_grade' => $id_grade[$x],
                        'gr' => $gr[$x],
                        'hrga' => $selesai == 'Y' ? $harga[$x] : 0,
                        'gr_beras' => 0,
                        'hrga_beras' =>  0,
                        'gr_kuning' => $gr_kuning[$x],
                        'hrga_kuning' => $selesai == 'Y' ? $harga_kuning[$x] : 0,
                        'urutan' => $urutan,
                        'no_nota' => $urutan,
                        'ket' => $r->ket[$y],
                        'id_invoice_congan' => $r->id_invoice_congan[$y]
                    ];
                    DB::table('tb_cong')->insert($data);
                }
            }

            DB::commit();
            return redirect()->route('congan.index')->with('sukses', 'Data berhasil disimpan');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('congan.index')->with('error', 'Gagal menyimpan data. Data lama telah dikembalikan.');
        }
    }

    public function buat_nota(Request $r)
    {
        $b = date('m');
        $max = DB::table('pembelian')->whereMonth('tgl', $b)->latest('urutan_nota')->first();

        $year = date("Y");
        $year = DB::table('tahun')->where('tahun', $year)->first();
        if (empty($max)) {
            $nota_t = '1';
        } else {
            $nota_t = $max->urutan_nota + 1;
        }
        $date = date('m');
        $bulan = DB::table('bulan')->where('bulan', $date)->first();
        $sub_po = "BI$year->kode" . "$bulan->kode" . str_pad($nota_t, 3, '0', STR_PAD_LEFT);

        $max_lot = DB::table('pembelian')->latest('no_lot')->first();

        if (empty($max_lot->no_lot)) {
            $max_l = '1001';
        } else {
            $max_l = $max_lot->no_lot + 1;
        }

        $nota_cong = $r->no_nota;

        $data =  [
            'title' => 'Tambah Bahan Baku',
            'suplier' => DB::table('tb_suplier')->get(),
            'nota' => $nota_t,
            'produk' => DB::table('tb_produk')->get(),
            'bulan' => $bulan,
            'akun' => DB::table('akun')->get(),
            'sub_po' => $sub_po,
            'no_lot' => $max_l,
            'nota_cong' => $nota_cong,
            'congan' => DB::select("SELECT a.*
            FROM invoice_congan as a
            where a.no_nota = '$nota_cong'
            group by a.no_nota, a.ket
            order by a.no_nota DESC
            "),
            'invoice' => DB::selectOne("SELECT *
             FROM invoice_congan as a
             where a.no_nota = '$nota_cong'
             group by a.no_nota
             ")

        ];
        return view('congan.add_nota', $data);
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

        $data = [
            'no_invoice_bk' => $sub_po
        ];
        DB::table('invoice_congan')->where('no_nota', $r->not_cong)->update($data);

        return redirect()->route('print_bk', ['no_nota' => $sub_po])->with('sukses', 'Data berhasil ditambahkan');
    }

    function export(Request $r)
    {

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
        $sheet1->setTitle('Cong-congan');


        $grade = DB::table('grade_congan')->where('aktif', 'Y')->orderBy('urutan', 'ASC')->get();


        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'No nota');
        $sheet1->setCellValue('C1', 'Tanggal');
        $sheet1->setCellValue('D1', 'Nama');
        $sheet1->setCellValue('E1', 'Harga Beli');
        $sheet1->setCellValue('F1', 'Harga (100%)');
        $sheet1->setCellValue('G1', 'Harga Basah');
        $sheet1->setCellValue('H1', 'GR');
        $column = 'I';
        foreach ($grade as $g) {
            $header = $column . '1';
            $sheet1->setCellValue($header, $g->nm_grade . ' %');
            $column++;
        }
        $airColumn = $column . '1';
        $sheet1->setCellValue($airColumn, 'Air(%)');

        $sheet1->getStyle("A1:$airColumn")->applyFromArray($style_atas);

        $congan = DB::select("SELECT a.*, b.ttl
        FROM invoice_congan as a
        left JOIN (
        SELECT b.no_nota, b.ket, sum(b.gr * b.hrga) as ttl
            FROM tb_cong as b 
            GROUP by b.no_nota, b.ket
        ) as b on b.no_nota = a.no_nota and b.ket = a.ket
        where a.tgl between '$r->tgl1' and '$r->tgl2'
        group by a.no_nota, a.ket
        order by a.no_nota DESC;
        ");

        $kolom = 2;
        foreach ($congan as $c) {
            $sheet1->setCellValue('A' . $kolom, $c->id_invoice_congan);
            $sheet1->setCellValue('B' . $kolom, $c->no_nota . '-' . $c->ket);
            $sheet1->setCellValue('C' . $kolom, $c->tgl);
            $sheet1->setCellValue('D' . $kolom, $c->pemilik);
            $sheet1->setCellValue('E' . $kolom, $c->hrga_beli);
            $sheet1->setCellValue('F' . $kolom, ($c->ttl / $c->gr) * ((100 - $c->persen_air) / 100));
            $sheet1->setCellValue('G' . $kolom, $c->ttl / $c->gr);
            $sheet1->setCellValue('H' . $kolom, $c->gr);
            $column_bawah = 'I';
            foreach ($grade as $g) {
                $header = $column_bawah . $kolom;
                $persen = DB::selectOne("SELECT a.gr  FROM tb_cong as a where a.no_nota = '$c->no_nota' and a.id_grade = '$g->id_grade_cong' and a.ket = '$c->ket'");

                $sheet1->setCellValue($header, empty($persen->gr) ? '0' : round(($persen->gr / $c->gr) * 100, 2));
                $column_bawah++;
            }
            $airColumn_bawah = $column_bawah . $kolom;
            $sheet1->setCellValue($airColumn_bawah, 100 - $c->persen_air);


            $kolom++;
        }

        $sheet1->getStyle("A2:$airColumn_bawah")->applyFromArray($style);


        $namafile = "Cong-congan.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function delete_nota(Request $r)
    {
        DB::table('invoice_congan')->where('id_invoice_congan', $r->id_invoice_congan)->delete();
        DB::table('tb_cong')->where('id_invoice_congan', $r->id_invoice_congan)->delete();

        return redirect()->route('congan.index')->with('sukses', 'Data berhasil dihapus');
    }

    public function harga_fix(Request $r)
    {
        $data = [
            'selesai' => 'Y',
        ];
        DB::table('invoice_congan')->where('no_nota', $r->no_nota)->update($data);
    }
    public function harga_unfix(Request $r)
    {
        $data = [
            'selesai' => 'T',
        ];
        DB::table('invoice_congan')->where('no_nota', $r->no_nota)->update($data);

        DB::table('tb_cong')->where('no_nota', $r->no_nota)->update(['hrga' => 0, 'hrga_kuning' => 0]);
    }

    public function export_congan(Request $r)
    {
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
        $sheet1->setTitle('Congan');

        $invoice = DB::table('invoice_congan')->where('no_nota', $r->no_nota)->first();

        $sheet1->getStyle("A1:E2")->applyFromArray($style_atas);
        $sheet1->getStyle("A4:I4")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'No Nota');
        $sheet1->setCellValue('A2', $r->no_nota);

        $sheet1->setCellValue('B1', 'Tanggal');
        $sheet1->setCellValue('B2', $invoice->tgl);

        $sheet1->setCellValue('C1', 'Pemilik');
        $sheet1->setCellValue('C2', $invoice->pemilik);


        $sheet1->setCellValue('D1', 'Keterangan');
        $sheet1->setCellValue('D2', $invoice->ket);

        $sheet1->setCellValue('E1', 'Persen Air');
        $sheet1->setCellValue('E2', $invoice->persen_air);


        $sheet1->setCellValue('A4', 'Kategori');
        $sheet1->setCellValue('B4', 'ID Grade');
        $sheet1->setCellValue('C4', 'Grade');
        $sheet1->setCellValue('D4', 'Putih/Beras  Gr');
        $sheet1->setCellValue('E4', 'Putih/Beras  Rp/gr');
        $sheet1->setCellValue('F4', 'Comp');
        $sheet1->setCellValue('G4', 'Kuning Gr');
        $sheet1->setCellValue('H4', 'Kuning Rp/gr');
        $sheet1->setCellValue('I4', 'Comp');



        $grade = DB::table('grade_congan')
            ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
            ->where('aktif', 'Y')
            ->where('grade_congan.kategori_id', '!=', '14')
            ->orderBy('kategori.kelompok', 'ASC')->orderBy('kategori.urutan', 'ASC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')->get();
        $grade2 = DB::table('grade_congan')
            ->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')
            ->where('aktif', 'Y')
            ->where('grade_congan.kategori_id',  '14')
            ->orderBy('kategori.kelompok', 'ASC')->orderBy('kategori.urutan', 'ASC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')->get();

        $kolom = 5;
        $prevKategori = null;
        $ttl_gr = 0;
        $total_rp = 0;
        foreach ($grade as $c) {

            $persen = DB::selectOne(
                "SELECT a.gr, a.hrga, a.gr_kuning, a.hrga_kuning, a.gr_beras, a.hrga_beras  FROM tb_cong as a where a.no_nota = '$r->no_nota' and a.id_grade = '$c->id_grade_cong' ",
            );

            $hrga_dlu = DB::table('tb_cong')
                ->where('id_grade', $c->id_grade_cong)
                ->where('no_nota', '!=', $r->no_nota)
                ->where('hrga', '!=', 0)
                ->orderBy('no_nota', 'desc')
                ->first();
            // $hrga_dlu_beras = DB::table('tb_cong')
            //     ->where('id_grade', $c->id_grade_cong)
            //     ->where('no_nota', '!=', $r->no_nota)
            //     ->where('hrga_beras', '!=', 0)
            //     ->orderBy('no_nota', 'desc')
            //     ->first();
            $hrga_dlu_kuning = DB::table('tb_cong')
                ->where('id_grade', $c->id_grade_cong)
                ->where('no_nota', '!=', $r->no_nota)
                ->where('hrga_kuning', '!=', 0)
                ->orderBy('no_nota', 'desc')
                ->first();
            $ttl_gr += ($persen->gr ?? 0) + ($persen->gr_kuning ?? 0);
            $hgra =
                empty($persen->hrga) || $persen->hrga == 0
                ? $hrga_dlu->hrga ?? 0
                : $persen->hrga;
            $hgra_beras =
                empty($persen->hrga_beras) || $persen->hrga_beras == 0
                ? $hrga_dlu->hrga_beras ?? 0
                : $persen->hrga_beras;
            $hgra_kuning =
                empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                ? $hrga_dlu->hrga_kuning ?? 0
                : $persen->hrga_kuning;
            $total_rp += ($persen->gr ?? 0) * $hgra + ($persen->gr_kuning ?? 0) * $hgra_kuning;
            $sheet1->setCellValue('A' . $kolom, $c->nm_kategori);
            $sheet1->setCellValue('B' . $kolom, $c->id_grade_cong);
            $sheet1->setCellValue('C' . $kolom, $c->nm_grade);
            $sheet1->setCellValue('D' . $kolom, $persen->gr ?? 0);
            $sheet1->setCellValue('E' . $kolom, empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga);
            $sheet1->setCellValue('F' . $kolom, empty($persen->gr) ? 0 : ($persen->gr / ($invoice->gr + $invoice->gr_kuning)) * 100);
            // $sheet1->setCellValue('G' . $kolom, $persen->gr_beras ?? 0);
            // $sheet1->setCellValue('H' . $kolom, empty($persen->hrga_beras) || $persen->hrga_beras == 0 ? $hrga_dlu_beras->hrga_beras ?? 0 : $persen->hrga_beras);
            // $sheet1->setCellValue('I' . $kolom, empty($persen->gr_beras) ? 0 : ($persen->gr_beras / ($invoice->gr + $invoice->gr_beras)) * 100);
            $sheet1->setCellValue('G' . $kolom, $persen->gr_kuning ?? 0);
            $sheet1->setCellValue('H' . $kolom, empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning);
            $sheet1->setCellValue('I' . $kolom, empty($persen->gr_kuning) ? 0 : ($persen->gr_kuning / ($invoice->gr + $invoice->gr_kuning)) * 100);

            $kolom++;
        }
        $sheet1->getStyle('A5:I' . $kolom - 1)->applyFromArray($style);
        $sheet1->getStyle('A' . $kolom + 1 . ":" . "I" . $kolom + 1)->applyFromArray($style_atas);

        $sheet1->setCellValue('A' . $kolom + 1, 'Kategori');
        $sheet1->setCellValue('B' . $kolom + 1, 'ID Grade');
        $sheet1->setCellValue('C' . $kolom + 1, 'Grade');
        $sheet1->setCellValue('D' . $kolom + 1, 'D  Gr');
        $sheet1->setCellValue('E' . $kolom + 1, 'D  Rp/gr');
        $sheet1->setCellValue('F' . $kolom + 1, 'Comp');
        $sheet1->setCellValue('G' . $kolom + 1, 'VPTH Gr');
        $sheet1->setCellValue('H' . $kolom + 1, 'VPTH Rp/gr');
        $sheet1->setCellValue('I' . $kolom + 1, 'Comp');

        $lastkolom = $kolom + 1;
        $kolom = $lastkolom + 1;
        $prevKategori = null;
        $ttl_gr = 0;
        $total_rp = 0;
        foreach ($grade2 as $c) {

            $persen = DB::selectOne(
                "SELECT a.gr, a.hrga, a.gr_kuning, a.hrga_kuning, a.gr_beras, a.hrga_beras  FROM tb_cong as a where a.no_nota = '$r->no_nota' and a.id_grade = '$c->id_grade_cong' ",
            );

            $hrga_dlu = DB::table('tb_cong')
                ->where('id_grade', $c->id_grade_cong)
                ->where('no_nota', '!=', $r->no_nota)
                ->where('hrga', '!=', 0)
                ->orderBy('no_nota', 'desc')
                ->first();
            // $hrga_dlu_beras = DB::table('tb_cong')
            //     ->where('id_grade', $c->id_grade_cong)
            //     ->where('no_nota', '!=', $r->no_nota)
            //     ->where('hrga_beras', '!=', 0)
            //     ->orderBy('no_nota', 'desc')
            //     ->first();
            $hrga_dlu_kuning = DB::table('tb_cong')
                ->where('id_grade', $c->id_grade_cong)
                ->where('no_nota', '!=', $r->no_nota)
                ->where('hrga_kuning', '!=', 0)
                ->orderBy('no_nota', 'desc')
                ->first();
            $ttl_gr += ($persen->gr ?? 0) + ($persen->gr_kuning ?? 0);
            $hgra =
                empty($persen->hrga) || $persen->hrga == 0
                ? $hrga_dlu->hrga ?? 0
                : $persen->hrga;
            $hgra_beras =
                empty($persen->hrga_beras) || $persen->hrga_beras == 0
                ? $hrga_dlu->hrga_beras ?? 0
                : $persen->hrga_beras;
            $hgra_kuning =
                empty($persen->hrga_kuning) || $persen->hrga_kuning == 0
                ? $hrga_dlu->hrga_kuning ?? 0
                : $persen->hrga_kuning;
            $total_rp += ($persen->gr ?? 0) * $hgra + ($persen->gr_kuning ?? 0) * $hgra_kuning;

            $sheet1->setCellValue('A' . $kolom, $c->nm_kategori);
            $sheet1->setCellValue('B' . $kolom, $c->id_grade_cong);
            $sheet1->setCellValue('C' . $kolom, $c->nm_grade);
            $sheet1->setCellValue('D' . $kolom, $persen->gr ?? 0);
            $sheet1->setCellValue('E' . $kolom, empty($persen->hrga) || $persen->hrga == 0 ? $hrga_dlu->hrga ?? 0 : $persen->hrga);
            $sheet1->setCellValue('F' . $kolom, empty($persen->gr) ? 0 : ($persen->gr / ($invoice->gr + $invoice->gr_kuning)) * 100);
            // $sheet1->setCellValue('G' . $kolom, $persen->gr_beras ?? 0);
            // $sheet1->setCellValue('H' . $kolom, empty($persen->hrga_beras) || $persen->hrga_beras == 0 ? $hrga_dlu_beras->hrga_beras ?? 0 : $persen->hrga_beras);
            // $sheet1->setCellValue('I' . $kolom, empty($persen->gr_beras) ? 0 : ($persen->gr_beras / ($invoice->gr + $invoice->gr_beras)) * 100);
            $sheet1->setCellValue('G' . $kolom, $persen->gr_kuning ?? 0);
            $sheet1->setCellValue('H' . $kolom, empty($persen->hrga_kuning) || $persen->hrga_kuning == 0 ? $hrga_dlu_kuning->hrga_kuning ?? 0 : $persen->hrga_kuning);
            $sheet1->setCellValue('I' . $kolom, empty($persen->gr_kuning) ? 0 : ($persen->gr_kuning / ($invoice->gr + $invoice->gr_kuning)) * 100);

            $kolom++;
        }
        $sheet1->getStyle('A' . $lastkolom . ':I' . $kolom - 1)->applyFromArray($style);




        $sheet1->setCellValue('K4', 'Total Gram');
        $sheet1->setCellValue('K5', 'Harga Beli');
        $sheet1->setCellValue('K6', 'Harga' . 100 - $invoice->persen_air . '%');
        $sheet1->setCellValue('K7', 'Harga' . 100 . '%');
        $sheet1->setCellValue('K8', 'Harga FIx');

        $sheet1->setCellValue('L4', $ttl_gr);
        $sheet1->setCellValue('L5', $invoice->hrga_beli);
        $sheet1->setCellValue('L6', $ttl_gr > 0 ? round(($total_rp / $ttl_gr) * ((100 - $invoice->persen_air) / 100), 0) : 0);
        $sheet1->setCellValue('L7', $ttl_gr > 0 ? round($total_rp / $ttl_gr, 0) : 0);
        $sheet1->setCellValue('L8', $invoice->selesai);


        $namafile = "Data Congan.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function import_congan(Request $r)
    {
        $hasError = false;

        if ($r->hasFile('file')) {
            $file = $r->file('file');
            $filePath = $file->storeAs('temp', 'imported_file.xlsx');

            $spreadsheet = IOFactory::load(storage_path("app/{$filePath}"));
            $sheetNames = $spreadsheet->getSheetNames();

            foreach ($sheetNames as $sheetName) {
                $currentSheet = $spreadsheet->getSheetByName($sheetName);
                $title = $currentSheet->getTitle();

                if ($title !== 'Congan') {
                    $hasError = true;
                    continue; // skip sheet yang tidak cocok
                }

                // ── Baca header info ──────────────────────────────────────
                $no_nota    = $currentSheet->getCell('A2')->getValue();
                $tgl        = $currentSheet->getCell('B2')->getValue();
                $pemilik    = $currentSheet->getCell('C2')->getValue();
                $ket        = $currentSheet->getCell('D2')->getValue();
                $persen_air = $currentSheet->getCell('E2')->getValue();
                $harga_beli = $currentSheet->getCell('L5')->getValue();
                $hrga_fix   = $currentSheet->getCell('L8')->getValue(); // "Y" atau null

                // ── Hapus data lama ───────────────────────────────────────
                DB::table('tb_cong')->where('no_nota', $no_nota)->delete();

                // ── Update selesai dulu agar bisa dicek saat insert ───────
                DB::table('invoice_congan')
                    ->where('no_nota', $no_nota)
                    ->update(['selesai' => $hrga_fix]);

                $congan = DB::table('invoice_congan')
                    ->where('no_nota', $no_nota)
                    ->first();

                $isSelesai = $congan->selesai === 'Y';

                // ── Cari baris pemisah (baris kosong antara 2 tabel) ─────
                // Grade 1: mulai baris 5 (index 5), sampai ketemu baris kosong
                // Grade 2: setelah baris header kedua

                $maxRow = $currentSheet->getHighestRow();

                $gr        = 0;
                $gr_kuning = 0;

                $mode          = 'grade1'; // 'grade1' | 'skip' | 'grade2'
                $grade2_header = false;

                for ($rowIndex = 5; $rowIndex <= $maxRow; $rowIndex++) {
                    $idGrade = $currentSheet->getCell('B' . $rowIndex)->getValue();

                    // Deteksi baris kosong → beralih ke mode skip
                    if (empty($idGrade) && $mode === 'grade1') {
                        $mode = 'skip';
                        continue;
                    }

                    // Deteksi baris header grade2 (ID Grade berisi teks, bukan angka)
                    if ($mode === 'skip' && !is_numeric($idGrade)) {
                        $mode = 'grade2';
                        continue; // lewati baris header
                    }

                    // Lewati jika ID Grade bukan angka (header nyasar)
                    if (!is_numeric($idGrade)) {
                        continue;
                    }

                    if ($mode === 'grade1') {
                        // Kolom: A=Kategori, B=ID Grade, C=Grade,
                        //        D=Putih/Beras Gr, E=Putih/Beras Rp/gr, F=Comp,
                        //        G=Kuning Gr, H=Kuning Rp/gr, I=Comp
                        $grPutih      = $currentSheet->getCell('D' . $rowIndex)->getValue() ?? 0;
                        $hrgaPutih    = $currentSheet->getCell('E' . $rowIndex)->getValue() ?? 0;
                        $grKuning     = $currentSheet->getCell('G' . $rowIndex)->getValue() ?? 0;
                        $hrgaKuning   = $currentSheet->getCell('H' . $rowIndex)->getValue() ?? 0;

                        DB::table('tb_cong')->insert([
                            'tgl'               => $tgl,
                            'id_grade'          => $idGrade,
                            'gr'                => $grPutih,
                            'hrga'              => $isSelesai ? $hrgaPutih : 0,
                            'gr_beras'          => 0,
                            'hrga_beras'        => 0,
                            'gr_kuning'         => $grKuning,
                            'hrga_kuning'       => $isSelesai ? $hrgaKuning : 0,
                            'urutan'            => $no_nota,
                            'no_nota'           => $no_nota,
                            'ket'               => $ket,
                            'id_invoice_congan' => $congan->id_invoice_congan,
                        ]);

                        $gr        += $grPutih;
                        $gr_kuning += $grKuning;
                    } elseif ($mode === 'grade2') {
                        // Kolom: A=Kategori, B=ID Grade, C=Grade,
                        //        D=D Gr, E=D Rp/gr, F=Comp,
                        //        G=VPTH Gr, H=VPTH Rp/gr, I=Comp
                        $grD        = $currentSheet->getCell('D' . $rowIndex)->getValue() ?? 0;
                        $hrgaD      = $currentSheet->getCell('E' . $rowIndex)->getValue() ?? 0;
                        $grVPTH     = $currentSheet->getCell('G' . $rowIndex)->getValue() ?? 0;
                        $hrgaVPTH   = $currentSheet->getCell('H' . $rowIndex)->getValue() ?? 0;

                        DB::table('tb_cong')->insert([
                            'tgl'               => $tgl,
                            'id_grade'          => $idGrade,
                            'gr'                => $grD,
                            'hrga'              => $isSelesai ? $hrgaD : 0,
                            'gr_beras'          => 0,
                            'hrga_beras'        => 0,
                            'gr_kuning'         => $grVPTH,
                            'hrga_kuning'       => $isSelesai ? $hrgaVPTH : 0,
                            'urutan'            => $no_nota,
                            'no_nota'           => $no_nota,
                            'ket'               => $ket,
                            'id_invoice_congan' => $congan->id_invoice_congan,
                        ]);

                        $gr        += $grD;
                        $gr_kuning += $grVPTH;
                    }
                }

                // ── Update invoice_congan dengan total gram ───────────────
                DB::table('invoice_congan')
                    ->where('no_nota', $no_nota)
                    ->update([
                        'tgl'        => $tgl,
                        'pemilik'    => $pemilik,
                        'ket'        => $ket,
                        'persen_air' => $persen_air,
                        'hrga_beli'  => $harga_beli,
                        'no_nota'    => $no_nota,
                        'gr'         => $gr,
                        'gr_beras'   => 0,
                        'gr_kuning'  => $gr_kuning,
                        'selesai'    => $hrga_fix,
                    ]);
            }

            unlink(storage_path("app/{$filePath}"));

            if ($hasError) {
                return redirect()->back()->with('error', 'Nama sheet yang diimport tidak sesuai');
            }
        }

        return redirect()
            ->route('congan.detail_nota', ['no_nota' => $r->no_nota])
            ->with('sukses', 'Data berhasil diimport');
    }
}
