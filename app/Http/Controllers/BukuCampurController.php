<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SettingHal;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class BukuCampurController extends Controller
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
        $pembelian = DB::select("SELECT a.id_invoice_bk, a.approve_bk_campur, a.no_lot, a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit, a.approve, d.no_nota as nota_grading, e.rupiah, f.total_harga as tl_harga
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            left join grading as d on d.no_nota = a.no_nota
            left join (
                SELECT e.no_nota, sum(e.rupiah) as rupiah
                FROM buku_campur as e
                group by e.no_nota
            ) as e on e.no_nota = a.no_nota
            left join invoice_bk_approve as f on f.no_nota = a.no_nota
            where a.tgl between '$tgl1' and '$tgl2' and e.no_nota is not null 
            order by e.rupiah ASC,a.no_nota ASC");

        $listBulan = DB::table('bulan')->get();
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Buku Campur',
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
        ];
        return view('buku_campur.index', $data);
    }

    function export_buku_campur(Request $r)
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
            $sheet1->getStyle('A1:I1')->applyFromArray($style_atas);

            $sheet1->setCellValue('A1', 'Tanggal');
            $sheet1->setCellValue('B1', 'No Nota');
            $sheet1->setCellValue('C1', 'No Lot');
            $sheet1->setCellValue('D1', 'Suplier Awal');
            $sheet1->setCellValue('E1', 'Suplier Akhir');
            $sheet1->setCellValue('F1', 'Gr Basah');
            $sheet1->setCellValue('G1', 'Pcs');
            $sheet1->setCellValue('H1', 'Gr Kering');
            $sheet1->setCellValue('I1', 'Total Harga');

            $kolom = 2;
            for ($x = 0; $x < count($r->ceknota); $x++) {
                $nota = $r->ceknota[$x];
                $pembelian = DB::selectOne("SELECT a.tgl, a.no_nota, a.no_lot, b.nm_suplier, a.suplier_akhir, c.gr_basah, c.pcs_awal, c.gr_kering, a.total_harga, f.total_harga as ttl_hrg, a.approve_bk_campur, g.gr_basah as gr_basah_apr, g.pcs_awal as pcs_awal_apr, g.gr_kering as gr_kering_apr
                FROM invoice_bk as a 
                left join tb_suplier as  b on b.id_suplier = a.id_suplier
                left join grading as c on c.no_nota = a.no_nota
                left join invoice_bk_approve as f on f.no_nota = a.no_nota
                left join grading_approve as g on g.no_nota = a.no_nota
                where a.no_nota = '$nota'");

                $sheet1->setCellValue('A' . $kolom, $pembelian->tgl);
                $sheet1->setCellValue('B' . $kolom, $pembelian->no_nota);
                $sheet1->setCellValue('C' . $kolom, $pembelian->no_lot);
                $sheet1->setCellValue('D' . $kolom, $pembelian->nm_suplier);
                $sheet1->setCellValue('E' . $kolom, $pembelian->suplier_akhir);
                $sheet1->setCellValue('F' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->gr_basah_apr : $pembelian->gr_basah);
                $sheet1->setCellValue('G' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->pcs_awal_apr : $pembelian->pcs_awal);
                $sheet1->setCellValue('H' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->gr_kering_apr :  $pembelian->gr_kering);
                $sheet1->setCellValue('I' . $kolom, $pembelian->approve_bk_campur == 'Y' ? $pembelian->ttl_hrg : $pembelian->total_harga);
                $kolom++;
            }

            $sheet1->getStyle('A2:I' . $kolom - 1)->applyFromArray($style);

            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2 = $spreadsheet->getActiveSheet(1);
            $sheet2->setTitle('Buku Campur');
            $sheet2->getStyle('A1:G1')->applyFromArray($style_atas);

            $sheet2->setCellValue('A1', 'ID BK Campur');
            $sheet2->setCellValue('B1', 'No Nota');
            $sheet2->setCellValue('C1', 'No Lot');
            $sheet2->setCellValue('D1', 'Nama Grade');
            $sheet2->setCellValue('E1', 'Pcs');
            $sheet2->setCellValue('F1', 'Gr');
            $sheet2->setCellValue('G1', 'Harga');

            $kolom = 2;
            foreach ($r->ceknota as $nota) {
                $buku_campur = DB::select("SELECT a.id_buku_campur, a.no_nota, a.no_lot, b.nm_grade, a.pcs, a.gr, a.rupiah
                    FROM buku_campur as a
                    left join grade as b on b.id_grade = a.id_grade
                    where a.no_nota = ?
                    group by a.id_buku_campur
                    ", [$nota]);


                foreach ($buku_campur as $b) {
                    $sheet2->setCellValue('A' . $kolom, $b->id_buku_campur);
                    $sheet2->setCellValue('B' . $kolom, $b->no_nota);
                    $sheet2->setCellValue('C' . $kolom, $b->no_lot);
                    $sheet2->setCellValue('D' . $kolom, $b->nm_grade);
                    $sheet2->setCellValue('E' . $kolom, $b->pcs);
                    $sheet2->setCellValue('F' . $kolom, $b->gr);
                    $sheet2->setCellValue('G' . $kolom, $b->rupiah);
                    $kolom++;
                }
            }
            $sheet2->getStyle('A2:G' . $kolom - 1)->applyFromArray($style);




            $namafile = "Buku Campur.xlsx";

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $namafile);
            header('Cache-Control: max-age=0');


            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit();
        } else {
            for ($x = 0; $x < count($r->ceknotaapprove); $x++) {
                DB::table('invoice_bk')->where('no_nota', $r->ceknotaapprove[$x])->update(['approve_bk_campur' => 'Y']);
                DB::table('grading')->where('no_nota', $r->ceknotaapprove[$x])->update(['approve' => 'Y']);

                $invoice = DB::table('invoice_bk')->where('no_nota', $r->ceknotaapprove[$x])->first();
                $grading = DB::table('grading')->where('no_nota', $r->ceknotaapprove[$x])->first();

                $data = [
                    'no_nota' => $r->ceknotaapprove[$x],
                    'no_lot' => $invoice->no_lot,
                    'total_harga' => $invoice->total_harga
                ];
                DB::table('invoice_bk_approve')->insert($data);

                $data = [
                    'no_nota' => $r->ceknotaapprove[$x],
                    'tgl' => $grading->tgl,
                    'no_campur' => $grading->no_campur,
                    'gr_basah' => $grading->gr_basah,
                    'pcs_awal' => $grading->pcs_awal,
                    'gr_kering' => $grading->gr_kering,
                ];
                DB::table('grading_approve')->insert($data);
                return redirect('buku_campur')->with('sukses', 'Data berhasil diapprove');
            }
        }

        // return Excel::download(new Buku_besarExport($tgl1, $tgl2, $id_akun, $totalrow), 'detail_buku_besar.xlsx');

    }

    public function import_buku_campur(Request $r)
    {
        // Mendapatkan file yang diunggah oleh pengguna
        $uploadedFile = $r->file('file'); // Pastikan bahwa Anda menggunakan objek Request

        // Cek jika file yang diunggah adalah file Excel
        $allowedExtensions = ['xlsx'];
        $extension = $uploadedFile->getClientOriginalExtension();
        if (in_array($extension, $allowedExtensions)) {
            // Memproses file Excel
            $spreadsheet = IOFactory::load($uploadedFile->getPathname());

            // Mengambil "Sheet 2" (Buku Campur)
            $sheet2 = $spreadsheet->getSheetByName('Buku Campur');

            // Mendapatkan data dari "Sheet 2"
            $data = [];
            foreach ($sheet2->getRowIterator() as $index => $row) {
                if ($index === 1) {
                    // Baris pertama adalah baris header, lewatkan
                    continue;
                }
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            // dd($data);

            // Sekarang $data berisi data dari "Sheet 2"

            // Simpan data ke tabel di database
            foreach ($data as $rowData) {

                $bk = DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->first();


                $invoice =  DB::table('invoice_bk')->where('no_nota', $bk->no_nota)->first();
                if ($invoice->approve_bk_campur == 'Y') {
                } else {
                    DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->update([
                        // 'no_lot' => $rowData[8],
                        'pcs' => $rowData[5],
                        'gr' => $rowData[6],
                        'rupiah' => $rowData[7],
                        'ket' => $rowData[9],
                        'approve' => 'Y',
                        'gabung' => 'T'
                    ]);

                    $buku_campur_approve = DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->first();

                    if (empty($buku_campur_approve->id_buku_campur)) {
                        DB::table('buku_campur_approve')->insert([
                            'id_buku_campur' => $rowData[0],
                            'pcs' => $rowData[5],
                            'gr' => $rowData[6],
                            'rupiah' => $rowData[7],
                            'ket' => $rowData[9],
                        ]);
                    } else {
                        DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                            'pcs' => $rowData[5],
                            'gr' => $rowData[6],
                            'rupiah' => $rowData[7],
                            'ket' => $rowData[9],
                        ]);
                    }
                }
            }

            // Lakukan tindakan lain sesuai kebutuhan, seperti memberikan respons ke pengguna
            return redirect()->route('pembelian_bk')->with('sukses', 'Data berhasil import');
        } else {
            // File yang diunggah bukan file Excel yang valid, tangani sesuai kebutuhan.
            return redirect()->route('pembelian_bk')->with('error', 'Data tidak valid');
        }
    }
}
