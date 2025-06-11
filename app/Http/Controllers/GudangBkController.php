<?php

namespace App\Http\Controllers;

use App\Models\GudangBkModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class GudangBkController extends Controller
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

    function index(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;

        if (empty($r->nm_gudang)) {
            $nmgudang = 'bk';
        } else {
            $nmgudang = $r->nm_gudang;
        }
        $gudang = GudangBkModel::getPembelianBk($nmgudang);

        $listBulan = DB::table('bulan')->get();
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Gudang BK',
            'gudang' => $gudang,
            'listbulan' => $listBulan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'presiden' => auth()->user()->posisi_id == 1 ? true : false,
            'nm_gudang' => $nmgudang
        ];
        return view('gudang_bk.index', $data);
    }

    public function export_buku_campur_bk(Request $r)
    {
        $this->export_gudang_bk($r);
        // if (auth()->user()->posisi_id == 1) {
        // } else {
        //     $this->export_gudang_produksi($r);
        // }
    }



    private function export_gudang_bk(Request $r)
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
        $sheet1->setTitle('baku dari bahan sdh grade');

        $sheet1->getStyle("A1:L1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'Buku');
        $sheet1->setCellValue('C1', 'Suplier Awal');
        $sheet1->setCellValue('C1', 'Date');
        $sheet1->setCellValue('D1', 'Grade');
        $sheet1->setCellValue('E1', 'Pcs');
        $sheet1->setCellValue('F1', 'Gram');
        $sheet1->setCellValue('G1', 'Rp/Gr');
        $sheet1->setCellValue('H1', 'Lot');
        $sheet1->setCellValue('I1', 'Keterangan/Nama Partai Herry');
        $sheet1->setCellValue('J1', 'Keterangan/Nama Partai Sinta');
        $sheet1->setCellValue('K1', 'Ttl Rp');
        $sheet1->setCellValue('L1', 'Lok');
        $sheet1->setCellValue('N2', 'kl mau import barang baru id kosongkan');
        $kolom = 2;
        $pembelian = GudangBkModel::export_getPembelianBk('bk');
        foreach ($pembelian as $d) {
            $sheet1->setCellValue('A' . $kolom, $d->id_buku_campur);
            $sheet1->setCellValue('B' . $kolom, $d->buku);
            $sheet1->setCellValue('C' . $kolom, $d->suplier_awal);
            $sheet1->setCellValue('C' . $kolom, $d->tgl);
            $sheet1->setCellValue('D' . $kolom, $d->nm_grade);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr);
            $sheet1->setCellValue('G' . $kolom, $d->rupiah);
            $sheet1->setCellValue('H' . $kolom, $d->no_lot);
            $sheet1->setCellValue('I' . $kolom, $d->ket);
            $sheet1->setCellValue('J' . $kolom, $d->ket2);
            $sheet1->setCellValue('K' . $kolom, $d->rupiah * $d->gr);
            $sheet1->setCellValue('L' . $kolom, $d->lok_tgl);


            $kolom++;
        }

        $sheet1->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);

        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(1);
        // $sheet2 = $spreadsheet->getActiveSheet();
        // $sheet2->setTitle('⁠penggabungan grd sama');

        // $sheet2->getStyle("A1:M1")->applyFromArray($style_atas);
        // $sheet2->setCellValue('A1', 'ID');
        // $sheet2->setCellValue('B1', 'Buku');
        // $sheet2->setCellValue('C1', 'Suplier Awal');
        // $sheet2->setCellValue('D1', 'Date');
        // $sheet2->setCellValue('E1', 'Grade');
        // $sheet2->setCellValue('F1', 'Pcs');
        // $sheet2->setCellValue('G1', 'Gram');
        // $sheet2->setCellValue('H1', 'Rp/Gr');
        // $sheet2->setCellValue('I1', 'Lot');
        // $sheet2->setCellValue('J1', 'Keterangan/Nama Partai Herry');
        // $sheet2->setCellValue('K1', 'Keterangan/Nama Partai Sinta');
        // $sheet2->setCellValue('L1', 'Ttl Rp');
        // $sheet2->setCellValue('M1', 'Lok');
        // $sheet2->setCellValue('O2', 'kl mau import barang baru id kosongkan');
        // $kolom = 2;
        // $kolom = 2;
        // $pembelian = GudangBkModel::export_getPembelianBk('gabung');

        // foreach ($pembelian as $d) {
        //     $sheet2->setCellValue('A' . $kolom, $d->id_buku_campur);
        //     $sheet2->setCellValue('B' . $kolom, $d->buku);
        //     $sheet2->setCellValue('C' . $kolom, $d->suplier_awal);
        //     $sheet2->setCellValue('D' . $kolom, $d->tgl);
        //     $sheet2->setCellValue('E' . $kolom, $d->nm_grade);
        //     $sheet2->setCellValue('F' . $kolom, $d->pcs);
        //     $sheet2->setCellValue('G' . $kolom, $d->gr);
        //     $sheet2->setCellValue('H' . $kolom, $d->rupiah);
        //     $sheet2->setCellValue('I' . $kolom, $d->no_lot);
        //     $sheet2->setCellValue('J' . $kolom, $d->ket);
        //     $sheet2->setCellValue('K' . $kolom, $d->ket2);
        //     $sheet2->setCellValue('L' . $kolom, $d->rupiah * $d->gr);
        //     $sheet2->setCellValue('M' . $kolom, $d->lok_tgl);
        //     $kolom++;
        // }

        // $sheet2->getStyle('A2:M' . $kolom - 1)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Rencana Produksi');

        $sheet3->getStyle("A1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'ID');
        $sheet3->setCellValue('B1', 'Buku');

        $sheet3->setCellValue('C1', 'Date');
        $sheet3->setCellValue('D1', 'Grade');
        $sheet3->setCellValue('E1', 'Pcs');
        $sheet3->setCellValue('F1', 'Gram');
        $sheet3->setCellValue('G1', 'Rp/Gr');
        $sheet3->setCellValue('H1', 'Lot');
        $sheet3->setCellValue('I1', 'Keterangan/Nama Partai Herry');
        $sheet3->setCellValue('J1', 'Keterangan/Nama Partai Sinta');
        $sheet3->setCellValue('K1', 'Ttl Rp');
        $sheet3->setCellValue('L1', 'Lok');
        $sheet3->setCellValue('N2', 'kl mau import barang baru id kosongkan');
        $kolom = 2;
        $pembelian = GudangBkModel::export_getPembelianBk('produksi');
        foreach ($pembelian as $d) {
            $sheet3->setCellValue('A' . $kolom, $d->id_buku_campur);
            $sheet3->setCellValue('B' . $kolom, $d->buku);
            // $sheet3->setCellValue('C' . $kolom, $d->suplier_awal);
            $sheet3->setCellValue('C' . $kolom, $d->tgl);
            $sheet3->setCellValue('D' . $kolom, $d->nm_grade);
            $sheet3->setCellValue('E' . $kolom, $d->pcs);
            $sheet3->setCellValue('F' . $kolom, $d->gr);
            $sheet3->setCellValue('G' . $kolom, $d->rupiah);
            $sheet3->setCellValue('H' . $kolom, $d->no_lot);
            $sheet3->setCellValue('I' . $kolom, $d->ket);
            $sheet3->setCellValue('J' . $kolom, $d->ket2);
            $sheet3->setCellValue('K' . $kolom, $d->rupiah * $d->gr);
            $sheet3->setCellValue('L' . $kolom, $d->lok_tgl);

            $kolom++;
        }

        $sheet3->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);

        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(2);
        // $sheet4 = $spreadsheet->getActiveSheet();
        // $sheet4->setTitle('Reject');

        // $sheet4->getStyle("A1:M1")->applyFromArray($style_atas);
        // $sheet4->setCellValue('A1', 'ID');
        // $sheet4->setCellValue('B1', 'Buku');
        // $sheet4->setCellValue('C1', 'Suplier Awal');
        // $sheet4->setCellValue('D1', 'Date');
        // $sheet4->setCellValue('E1', 'Grade');
        // $sheet4->setCellValue('F1', 'Pcs');
        // $sheet4->setCellValue('G1', 'Gram');
        // $sheet4->setCellValue('H1', 'Rp/Gr');
        // $sheet4->setCellValue('I1', 'Lot');
        // $sheet4->setCellValue('J1', 'Keterangan/Nama Partai Herry');
        // $sheet4->setCellValue('K1', 'Keterangan/Nama Partai Sinta');
        // $sheet4->setCellValue('L1', 'Ttl Rp');
        // $sheet4->setCellValue('M1', 'Lok');

        // $sheet4->setCellValue('O2', 'kl mau import barang baru id kosongkan');
        // $kolom = 2;
        // $pembelian = GudangBkModel::export_getPembelianBk('reject');
        // foreach ($pembelian as $d) {
        //     $sheet4->setCellValue('A' . $kolom, $d->id_buku_campur);
        //     $sheet4->setCellValue('B' . $kolom, $d->buku);
        //     $sheet4->setCellValue('C' . $kolom, $d->suplier_awal);
        //     $sheet4->setCellValue('D' . $kolom, $d->tgl);
        //     $sheet4->setCellValue('E' . $kolom, $d->nm_grade);
        //     $sheet4->setCellValue('F' . $kolom, $d->pcs);
        //     $sheet4->setCellValue('G' . $kolom, $d->gr);
        //     $sheet4->setCellValue('H' . $kolom, $d->rupiah);
        //     $sheet4->setCellValue('I' . $kolom, $d->no_lot);
        //     $sheet4->setCellValue('J' . $kolom, $d->ket);
        //     $sheet4->setCellValue('K' . $kolom, $d->ket2);
        //     $sheet4->setCellValue('L' . $kolom, $d->rupiah * $d->gr);
        //     $sheet4->setCellValue('M' . $kolom, $d->lok_tgl);


        //     $kolom++;
        // }

        // $sheet4->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet4 = $spreadsheet->getActiveSheet();
        $sheet4->setTitle('campur produksi');

        $sheet4->getStyle("A1:L1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'ID');
        $sheet4->setCellValue('B1', 'Buku');
        // $sheet4->setCellValue('C1', 'Suplier Awal');
        $sheet4->setCellValue('C1', 'Date');
        $sheet4->setCellValue('D1', 'Grade');
        $sheet4->setCellValue('E1', 'Pcs');
        $sheet4->setCellValue('F1', 'Gram');
        $sheet4->setCellValue('G1', 'Rp/Gr');
        $sheet4->setCellValue('H1', 'Lot');
        $sheet4->setCellValue('I1', 'Keterangan/Nama Partai Herry');
        $sheet4->setCellValue('J1', 'Keterangan/Nama Partai Sinta');
        $sheet4->setCellValue('K1', 'Ttl Rp');
        $sheet4->setCellValue('L1', 'Lok');

        $sheet4->setCellValue('N2', 'kl mau import barang baru id kosongkan');
        $kolom = 2;
        $pembelian = GudangBkModel::export_getPembelianBk('wip');
        foreach ($pembelian as $d) {
            $sheet4->setCellValue('A' . $kolom, $d->id_buku_campur);
            $sheet4->setCellValue('B' . $kolom, $d->buku);
            // $sheet4->setCellValue('C' . $kolom, $d->suplier_awal);
            $sheet4->setCellValue('C' . $kolom, $d->tgl);
            $sheet4->setCellValue('D' . $kolom, $d->nm_grade);
            $sheet4->setCellValue('E' . $kolom, $d->pcs);
            $sheet4->setCellValue('F' . $kolom, $d->gr);
            $sheet4->setCellValue('G' . $kolom, $d->rupiah);
            $sheet4->setCellValue('H' . $kolom, $d->no_lot);
            $sheet4->setCellValue('I' . $kolom, $d->ket);
            $sheet4->setCellValue('J' . $kolom, $d->ket2);
            $sheet4->setCellValue('K' . $kolom, $d->rupiah * $d->gr);
            $sheet4->setCellValue('L' . $kolom, $d->lok_tgl);

            $kolom++;
        }

        $sheet4->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);

        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(5);
        // $sheet6 = $spreadsheet->getActiveSheet();
        // $sheet6->setTitle('wip cetak');

        // $sheet6->getStyle("A1:M1")->applyFromArray($style_atas);
        // $sheet6->setCellValue('A1', 'ID');
        // $sheet6->setCellValue('B1', 'Buku');
        // $sheet6->setCellValue('C1', 'Suplier Awal');
        // $sheet6->setCellValue('D1', 'Date');
        // $sheet6->setCellValue('E1', 'Grade');
        // $sheet6->setCellValue('F1', 'Pcs');
        // $sheet6->setCellValue('G1', 'Gram');
        // $sheet6->setCellValue('H1', 'Rp/Gr');
        // $sheet6->setCellValue('I1', 'Lot');
        // $sheet6->setCellValue('J1', 'Keterangan/Nama Partai Herry');
        // $sheet6->setCellValue('K1', 'Keterangan/Nama Partai Sinta');
        // $sheet6->setCellValue('L1', 'Ttl Rp');
        // $sheet6->setCellValue('M1', 'Lok');

        // $sheet6->setCellValue('O2', 'kl mau import barang baru id kosongkan');
        // $kolom = 2;
        // $pembelian = GudangBkModel::export_getPembelianBk('wipcetak');
        // foreach ($pembelian as $d) {
        //     $sheet6->setCellValue('A' . $kolom, $d->id_buku_campur);
        //     $sheet6->setCellValue('B' . $kolom, $d->buku);
        //     $sheet6->setCellValue('C' . $kolom, $d->suplier_awal);
        //     $sheet6->setCellValue('D' . $kolom, $d->tgl);
        //     $sheet6->setCellValue('E' . $kolom, $d->nm_grade);
        //     $sheet6->setCellValue('F' . $kolom, $d->pcs);
        //     $sheet6->setCellValue('G' . $kolom, $d->gr);
        //     $sheet6->setCellValue('H' . $kolom, $d->rupiah);
        //     $sheet6->setCellValue('I' . $kolom, $d->no_lot);
        //     $sheet6->setCellValue('J' . $kolom, $d->ket);
        //     $sheet6->setCellValue('K' . $kolom, $d->ket2);
        //     $sheet6->setCellValue('L' . $kolom, $d->rupiah * $d->gr);
        //     $sheet6->setCellValue('M' . $kolom, $d->lok_tgl);

        //     $kolom++;
        // }

        // $sheet6->getStyle('A2:M' . $kolom - 1)->applyFromArray($style);

        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(6);
        // $sheet7 = $spreadsheet->getActiveSheet();
        // $sheet7->setTitle('wip sortir');

        // $sheet7->getStyle("A1:M1")->applyFromArray($style_atas);
        // $sheet7->setCellValue('A1', 'ID');
        // $sheet7->setCellValue('B1', 'Buku');
        // $sheet7->setCellValue('C1', 'Suplier Awal');
        // $sheet7->setCellValue('D1', 'Date');
        // $sheet7->setCellValue('E1', 'Grade');
        // $sheet7->setCellValue('F1', 'Pcs');
        // $sheet7->setCellValue('G1', 'Gram');
        // $sheet7->setCellValue('H1', 'Rp/Gr');
        // $sheet7->setCellValue('I1', 'Lot');
        // $sheet7->setCellValue('J1', 'Keterangan/Nama Partai Herry');
        // $sheet7->setCellValue('K1', 'Keterangan/Nama Partai Sinta');
        // $sheet7->setCellValue('L1', 'Ttl Rp');
        // $sheet7->setCellValue('M1', 'Lok');

        // $sheet7->setCellValue('O2', 'kl mau import barang baru id kosongkan');
        // $kolom = 2;
        // $pembelian = GudangBkModel::export_getPembelianBk('wipsortir');
        // foreach ($pembelian as $d) {
        //     $sheet7->setCellValue('A' . $kolom, $d->id_buku_campur);
        //     $sheet7->setCellValue('B' . $kolom, $d->buku);
        //     $sheet7->setCellValue('C' . $kolom, $d->suplier_awal);
        //     $sheet7->setCellValue('D' . $kolom, $d->tgl);
        //     $sheet7->setCellValue('E' . $kolom, $d->nm_grade);
        //     $sheet7->setCellValue('F' . $kolom, $d->pcs);
        //     $sheet7->setCellValue('G' . $kolom, $d->gr);
        //     $sheet7->setCellValue('H' . $kolom, $d->rupiah);
        //     $sheet7->setCellValue('I' . $kolom, $d->no_lot);
        //     $sheet7->setCellValue('J' . $kolom, $d->ket);
        //     $sheet7->setCellValue('K' . $kolom, $d->ket2);
        //     $sheet7->setCellValue('L' . $kolom, $d->rupiah * $d->gr);
        //     $sheet7->setCellValue('M' . $kolom, $d->lok_tgl);

        //     $kolom++;
        // }

        // $sheet7->getStyle('A2:M' . $kolom - 1)->applyFromArray($style);


        $namafile = "Gudang Bahan baku.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
    public function export_gudang_produksi(Request $r)
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




        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('rencana produksi');

        $sheet2->getStyle("A1:K1")->applyFromArray($style_atas);

        $sheet2->setCellValue('A1', 'ID');
        $sheet2->setCellValue('B1', 'Buku');
        // $sheet2->setCellValue('C1', 'Suplier Awal');
        $sheet2->setCellValue('C1', 'Date');
        $sheet2->setCellValue('D1', 'Grade');
        $sheet2->setCellValue('E1', 'Pcs');
        $sheet2->setCellValue('F1', 'Gram');
        $sheet2->setCellValue('G1', 'Rp Gram');
        $sheet2->setCellValue('H1', 'Lot');
        $sheet2->setCellValue('I1', 'Keterangan/Nama Partai Herry');
        $sheet2->setCellValue('J1', 'Keterangan/Nama Partai Sinta');
        $sheet2->setCellValue('K1', 'Lok');
        $kolom = 2;
        $pembelian = GudangBkModel::export_getPembelianBk('produksi');
        foreach ($pembelian as $d) {
            $sheet2->setCellValue('A' . $kolom, $d->id_buku_campur);
            $sheet2->setCellValue('B' . $kolom, $d->buku);
            // $sheet2->setCellValue('C' . $kolom, $d->suplier_awal);
            $sheet2->setCellValue('C' . $kolom, $d->tgl);
            $sheet2->setCellValue('D' . $kolom, $d->nm_grade);
            $sheet2->setCellValue('E' . $kolom, $d->pcs);
            $sheet2->setCellValue('F' . $kolom, $d->gr);
            $sheet2->setCellValue('G' . $kolom, $d->rupiah);
            $sheet2->setCellValue('H' . $kolom, $d->no_lot);
            $sheet2->setCellValue('I' . $kolom, $d->ket);
            $sheet2->setCellValue('J' . $kolom, $d->ket2);
            $sheet2->setCellValue('K' . $kolom, $d->lok_tgl);

            $kolom++;
        }
        $sheet2->getStyle('A2:K' . $kolom - 1)->applyFromArray($style);
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('campur produksi');

        $sheet3->getStyle("A1:H1")->applyFromArray($style_atas);

        $sheet3->setCellValue('A1', 'ID');
        $sheet3->setCellValue('B1', 'Keterangan/Nama Partai Sinta');
        $sheet3->setCellValue('C1', 'Keterangan/Nama Partai Herry');
        $sheet3->setCellValue('D1', 'Date');
        $sheet3->setCellValue('E1', 'Grade');
        $sheet3->setCellValue('F1', 'Pcs');
        $sheet3->setCellValue('G1', 'Gr');
        $sheet3->setCellValue('H1', 'Rp Gram');

        $sheet3->setCellValue('J1', 'Id dikosongkan jika ingin menambahkan data baru');
        $kolom = 2;
        $pembelian = GudangBkModel::export_getPembelianBk('wip');
        foreach ($pembelian as $d) {
            $sheet3->setCellValue('A' . $kolom, $d->id_buku_campur);
            $sheet3->setCellValue('B' . $kolom, $d->ket2);
            $sheet3->setCellValue('C' . $kolom, $d->ket);
            $sheet3->setCellValue('D' . $kolom, $d->tgl);
            $sheet3->setCellValue('E' . $kolom, $d->nm_grade);
            $sheet3->setCellValue('F' . $kolom, $d->pcs);
            $sheet3->setCellValue('G' . $kolom, $d->gr);
            $sheet3->setCellValue('H' . $kolom, $d->rupiah);

            $kolom++;
        }
        $sheet3->getStyle('A2:H' . $kolom - 1)->applyFromArray($style);


        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(2);
        // $sheet3 = $spreadsheet->getActiveSheet();
        // $sheet3->setTitle('Gudang Produksi Gabung');

        // $sheet3->getStyle("A1:G1")->applyFromArray($style_atas);

        // $sheet3->setCellValue('A1', 'Grade');
        // $sheet3->setCellValue('B1', 'Pcs');
        // $sheet3->setCellValue('C1', 'Gr');
        // $sheet3->setCellValue('D1', 'Rp/Gr');
        // $sheet3->setCellValue('E1', 'Keterangan / Nama Partai Herry');
        // $sheet3->setCellValue('F1', 'Keterangan / Nama Partai Sinta');
        // $sheet3->setCellValue('G1', 'Total Rp');
        // $sheet3->setCellValue('J2', 'kl mau import barang baru id kosongkan');
        // $kolom = 2;
        // $pembelian = GudangBkModel::getProduksiGabung();
        // foreach ($pembelian as $d) {
        //     $sheet3->setCellValue('A' . $kolom, $d->nm_grade);
        //     $sheet3->setCellValue('B' . $kolom, $d->pcs);
        //     $sheet3->setCellValue('C' . $kolom, $d->gr);
        //     $sheet3->setCellValue('D' . $kolom, $d->total_rp / $d->gr);
        //     $sheet3->setCellValue('E' . $kolom, $d->ket);
        //     $sheet3->setCellValue('F' . $kolom, $d->ket2);
        //     $sheet3->setCellValue('G' . $kolom, $d->total_rp);
        //     $kolom++;
        // }
        // $sheet3->getStyle('A2:G' . $kolom - 1)->applyFromArray($style);


        $namafile = "Gudang Produksi.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    // public function import_buku_campur_bk(Request $r)
    // {
    //     $this->import_buku_bk($r);
    //     return redirect()->route('gudangnew.index')->with('sukses', 'Data berhasil import');
    // }


    private function import_gudang_produksi(Request $r)
    {
        $hasError = false; // Variabel flag untuk melacak apakah ada kesalahan

        if ($r->hasFile('file')) {
            $file = $r->file('file');
            $filePath = $file->storeAs('temp', 'imported_file.xlsx');

            try {
                $spreadsheet = IOFactory::load(storage_path("app/{$filePath}"));
                $sheetNames = $spreadsheet->getSheetNames();

                foreach ($sheetNames as $sheetName) {
                    $currentSheet = $spreadsheet->getSheetByName($sheetName);
                    $title = $currentSheet->getTitle();

                    if ($title === 'Produksi (ini nama herry)') {
                        $gudang = 'produksi';
                    } elseif ($title === 'wip ( ini nama sinta)') {
                        $gudang = 'wip';
                    } else {
                        $hasError = true;
                    }

                    foreach ($currentSheet->getRowIterator() as $rowIndex => $row) {
                        if ($rowIndex === 1) {
                            continue;
                        }
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();

                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        if (empty(array_filter(array_slice($rowData, 0, 13), 'strlen'))) {
                            continue;
                        }

                        try {
                            if (empty($rowData[0])) {
                                DB::table('buku_campur')->insert([
                                    'id_grade' => '1',
                                    'pcs' => $rowData[5],
                                    'gr' => $rowData[6],
                                    'no_lot' => $rowData[7],
                                    'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                    'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                    'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                                    'approve' => 'Y',
                                    'gudang' => $gudang,

                                ]);

                                $idBukuCampur = DB::getPdo()->lastInsertId();
                                $tgl = $rowData[3];
                                if (is_numeric($tgl)) {
                                    $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                                    $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                                } else {
                                    // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                                    $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                                }
                                DB::table('buku_campur_approve')->insert([
                                    'id_buku_campur' => $idBukuCampur,
                                    'buku' => empty($rowData[1]) ? ' ' : $rowData[1],
                                    'suplier_awal' => empty($rowData[2]) ? ' ' : $rowData[2],
                                    'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                    'nm_grade' => $rowData[4],
                                    'pcs' => $rowData[5],
                                    'gr' => $rowData[6],
                                    'no_lot' => $rowData[7],
                                    'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                    'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                    'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                                    'gudang' => $gudang,
                                ]);
                            } else {
                                $buku_campur = DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->first();
                                DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->update([
                                    'approve' => 'Y',
                                    'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                    'gudang' => $gudang,
                                ]);

                                $bk_approve = DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->first();
                                $tgl = $rowData[3];
                                if (is_numeric($tgl)) {
                                    // Jika nilai berupa angka, konversi ke format tanggal
                                    $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                                    $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                                } else {
                                    // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                                    $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                                }
                                if (empty($bk_approve)) {
                                    DB::table('buku_campur_approve')->insert([
                                        'id_buku_campur' => $rowData[0],
                                        'buku' => $rowData[1],
                                        'suplier_awal' => $rowData[2],
                                        'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                        'nm_grade' => $rowData[4],
                                        'pcs' => $rowData[5],
                                        'gr' => $rowData[6],
                                        'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                        'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                        'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                        'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                                        'gudang' => $gudang,
                                    ]);
                                } else {
                                    DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                                        'id_buku_campur' => $rowData[0],
                                        'buku' => $rowData[1],
                                        'suplier_awal' => $rowData[2],
                                        'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                        'nm_grade' => $rowData[4],
                                        'pcs' => $rowData[5],
                                        'gr' => $rowData[6],
                                        'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                        'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                        'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                        'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                                        'gudang' => $gudang,
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            // Jika terjadi kesalahan, atur flag dan hentikan pembaruan
                            $hasError = true;
                            break 2; // Keluar dari kedua loop (sheet dan row)
                        }
                    }
                }
            } catch (\Exception $e) {
                echo 'Error loading spreadsheet: ' . $e->getMessage();
            }

            unlink(storage_path("app/{$filePath}"));

            // Jika ada kesalahan, batalkan semua pembaruan
            if ($hasError) {
                echo 'Pembaruan dibatalkan karena terjadi kesalahan.';
            }
        }
    }

    public function import_gudang_produksi_new(Request $r)
    {
        $uploadedFile = $r->file('file');
        $allowedExtensions = ['xlsx'];
        $extension = $uploadedFile->getClientOriginalExtension();

        if (in_array($extension, $allowedExtensions)) {
            $spreadsheet = IOFactory::load($uploadedFile->getPathname());
            $sheet2 = $spreadsheet->getSheetByName('campur produksi');
            if (!$sheet2) {
                return redirect()->route('gudangnew.gudang_p_kerja')->with('error', 'Sheet dengan nama yang diharapkan tidak ditemukan dalam file Excel');
            }
            $data = [];

            foreach ($sheet2->getRowIterator() as $index => $row) {
                if ($index === 1) {
                    continue;
                }

                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            $importGagal = false;

            DB::beginTransaction(); // Mulai transaksi database

            try {
                foreach ($data as $rowData) {
                    if (empty($rowData[0])) {
                        DB::table('buku_campur')->insert([
                            'id_grade' => '1',
                            'pcs' => $rowData[4],
                            'gr' => $rowData[5],
                            'rupiah' => $rowData[6],
                            'no_lot' => $rowData[7],
                            'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                            'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                            'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                            'approve' => 'Y',
                            'gudang' => 'wip',
                        ]);
                        $idBukuCampur = DB::getPdo()->lastInsertId();
                        $tgl = $rowData[2];
                        if (is_numeric($tgl)) {
                            $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                            $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                        } else {
                            // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                            $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                        }
                        DB::table('buku_campur_approve')->insert([
                            'id_buku_campur' => $idBukuCampur,
                            'buku' => empty($rowData[1]) ? ' ' : $rowData[1],
                            // 'suplier_awal' => empty($rowData[2]) ? ' ' : $rowData[2],
                            'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                            'nm_grade' => $rowData[3],
                            'pcs' => $rowData[4],
                            'gr' => $rowData[5],
                            'rupiah' => $rowData[6],
                            'no_lot' => $rowData[7],
                            'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                            'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                            'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                            'gudang' => 'wip',
                        ]);
                    } else {
                        DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->update([
                            'id_grade' => '1',
                            'pcs' => $rowData[4],
                            'gr' => $rowData[5],
                            'rupiah' => $rowData[6],
                            'no_lot' => $rowData[7],
                            'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                            'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                            'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                            'approve' => 'Y',
                            'gudang' => 'wip',
                        ]);
                        $tgl = $rowData[2];
                        if (is_numeric($tgl)) {
                            $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                            $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                        } else {
                            // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                            $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                        }
                        DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                            'buku' => empty($rowData[1]) ? ' ' : $rowData[1],
                            // 'suplier_awal' => empty($rowData[2]) ? ' ' : $rowData[2],
                            'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                            'nm_grade' => $rowData[3],
                            'pcs' => $rowData[4],
                            'gr' => $rowData[5],
                            'rupiah' => $rowData[6],
                            'no_lot' => $rowData[7],
                            'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                            'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                            'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                            'gudang' => 'wip',
                        ]);
                    }

                    DB::table('buku_campur_approve')->where('ket', $rowData[8])->update(['ket2' => empty($rowData[9]) ? ' ' : $rowData[9]]);
                }
                DB::commit(); // Konfirmasi transaksi jika berhasil
                return redirect()->route('gudangnew.gudang_p_kerja')->with('sukses', 'Data berhasil import');
            } catch (\Exception $e) {
                DB::rollback(); // Batalkan transaksi jika terjadi kesalahan lain
                return redirect()->route('gudangnew.gudang_p_kerja')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
            }
        } else {
            return redirect()->route('gudangnew.gudang_p_kerja')->with('error', 'File yang diunggah bukan file Excel yang valid');
        }
    }

    public function import_buku_campur_bk(Request $r)
    {

        $hasError = false; // Variabel flag untuk melacak apakah ada kesalahan
        if ($r->hasFile('file')) {
            $file = $r->file('file');
            $filePath = $file->storeAs('temp', 'imported_file.xlsx');

            try {
                $spreadsheet = IOFactory::load(storage_path("app/{$filePath}"));
                $sheetNames = $spreadsheet->getSheetNames();

                foreach ($sheetNames as $sheetName) {
                    $currentSheet = $spreadsheet->getSheetByName($sheetName);
                    $title = $currentSheet->getTitle();

                    if ($title === 'baku dari bahan sdh grade') {
                        DB::table('buku_campur')->where('gudang', 'bk')->update(['gabung' => 'Y']);
                        $gudang = 'bk';
                    } elseif ($title === 'Rencana Produksi') {
                        DB::table('buku_campur')->where('gudang', 'produksi')->update(['gabung' => 'Y']);
                        $gudang = 'produksi';
                    } elseif ($title === 'Reject') {
                        DB::table('buku_campur')->where('gudang', 'reject')->update(['gabung' => 'Y']);
                        $gudang = 'reject';
                    } elseif ($title === 'campur produksi') {
                        DB::table('buku_campur')->where('gudang', 'wip')->update(['gabung' => 'Y']);
                        $gudang = 'wip';
                    } else {
                        $hasError = true;
                    }

                    foreach ($currentSheet->getRowIterator() as $rowIndex => $row) {
                        if ($rowIndex === 1) {
                            continue;
                        }
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();

                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        if (empty(array_filter(array_slice($rowData, 0, 13), 'strlen'))) {
                            continue;
                        }

                        try {
                            if (empty($rowData[0])) {
                                DB::table('buku_campur')->insert([
                                    'no_lot' => $rowData[8],
                                    'id_grade' => '1',
                                    'pcs' => $rowData[5],
                                    'gr' => $rowData[6],
                                    'rupiah' => $rowData[7],
                                    'ket' => empty($rowData[9]) ? ' ' : $rowData[9],
                                    'ket2' => empty($rowData[10]) ? ' ' : $rowData[10],
                                    'lok_tgl' => empty($rowData[11]) ? ' ' : $rowData[11],
                                    'approve' => 'Y',
                                    'gudang' => $gudang,
                                    'gabung' => 'T'

                                ]);
                                $idBukuCampur = DB::getPdo()->lastInsertId();

                                $tgl = $rowData[2];
                                if (is_numeric($tgl)) {
                                    $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                                    $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                                } else {
                                    // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                                    $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                                }
                                DB::table('buku_campur_approve')->insert([
                                    'id_buku_campur' => $idBukuCampur,
                                    'buku' => empty($rowData[1]) ? ' ' : $rowData[1],
                                    // 'suplier_awal' => empty($rowData[2]) ? ' ' : $rowData[2],
                                    'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                    'nm_grade' => $rowData[3],
                                    'pcs' => $rowData[4],
                                    'gr' => $rowData[5],
                                    'rupiah' => $rowData[6],
                                    'no_lot' => $rowData[7],
                                    'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                    'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                    'lok_tgl' => empty($rowData[11]) ? ' ' : $rowData[11],
                                    'gudang' => $gudang,
                                ]);
                            } else {
                                $buku_campur = DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->first();
                                DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->update([
                                    'approve' => 'Y',
                                    'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                    'gudang' => $gudang,
                                    'gabung' => 'T'
                                ]);

                                $bk_approve = DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->first();

                                $tgl = $rowData[2];
                                if (is_numeric($tgl)) {
                                    // Jika nilai berupa angka, konversi ke format tanggal
                                    $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                                    $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                                } else {
                                    // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                                    $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                                }

                                if (empty($bk_approve)) {
                                    DB::table('buku_campur_approve')->insert([
                                        'id_buku_campur' => $rowData[0],
                                        'buku' => $rowData[1],
                                        // 'suplier_awal' => $rowData[2],
                                        'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                        'nm_grade' => $rowData[3],
                                        'pcs' => $rowData[4],
                                        'gr' => $rowData[5],
                                        'rupiah' => $rowData[6],
                                        'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                        'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                        'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                        'lok_tgl' => empty($rowData[11]) ? ' ' : $rowData[11],
                                        'gudang' => $gudang,
                                    ]);
                                } else {
                                    DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                                        'id_buku_campur' => $rowData[0],
                                        'buku' => $rowData[1],
                                        // 'suplier_awal' => $rowData[2],
                                        'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                        'nm_grade' => $rowData[3],
                                        'pcs' => $rowData[4],
                                        'gr' => $rowData[5],
                                        'rupiah' => $rowData[6],
                                        'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                        'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                        'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                        'lok_tgl' => empty($rowData[11]) ? ' ' : $rowData[11],
                                        'gudang' => $gudang,
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            // Jika terjadi kesalahan, atur flag dan hentikan pembaruan
                            $hasError = true;
                            break 2; // Keluar dari kedua loop (sheet dan row)
                        }
                    }
                }
            } catch (\Exception $e) {
                echo 'Error loading spreadsheet: ' . $e->getMessage();
            }

            unlink(storage_path("app/{$filePath}"));

            // Jika ada kesalahan, batalkan semua pembaruan
            if ($hasError) {
                return redirect()->back()->with('error', 'Nama sheet yg di import tidak sesuai');
            }
        }
        return redirect()->route('gudangBk.index')->with('sukses', 'Data berhasil import');
    }



    public function import_wip_cetak(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                if (empty($row[0])) {
                    DB::table('gudang_ctk')->insert([
                        'partai_h' => $row[1],
                        'no_box' => $row[2],
                        'tipe' => $row[3],
                        'grade' => $row[4],
                        'pcs_cabut' => $row[5],
                        'gr_cabut' => $row[6],
                        'ttl_rp' => $row[7],
                        'cost_cabut' => $row[8],
                        'pcs_timbang_ulang' => $row[9],
                        'gr_timbang_ulang' => $row[10],
                        'selesai' => $row[11],
                    ]);
                } else {
                    DB::table('gudang_ctk')->where('id_gudang_ctk', $row[0])->update([
                        'partai_h' => $row[1],
                        'no_box' => $row[2],
                        'tipe' => $row[3],
                        'grade' => $row[4],
                        'pcs_cabut' => $row[5],
                        'gr_cabut' => $row[6],
                        'ttl_rp' => $row[7],
                        'cost_cabut' => $row[8],
                        'pcs_timbang_ulang' => $row[9],
                        'gr_timbang_ulang' => $row[10],
                        'selesai' => $row[11],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('halawal.index', ['nm_gudang' => 'wip'])->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function wip(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;


        if (empty($r->nm_gudang)) {
            $nmgudang = 'bk';
        } else {
            $nmgudang = $r->nm_gudang;
        }

        $gudang = GudangBkModel::getPembelianBk($nmgudang);
        if ($nmgudang == 'wipcetak') {
            $view = 'gudang_bk.wipcetak';
        } else {
            $view = 'gudang_bk.wip';
        }
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai");
        $cabut = $response->object();
        $listBulan = DB::table('bulan')->get();
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Gudang BK',
            'gudang' => $gudang,
            'listbulan' => $listBulan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'presiden' => auth()->user()->posisi_id == 1 ? true : false,
            'nm_gudang' => $nmgudang,
            'cabut' => $cabut,
            'wip_cetak' => DB::table('gudang_ctk')->where('selesai', 'selesai')->get()
        ];
        return view($view, $data);
    }

    public function export_wip_cetak(Request $r)
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
        $sheet1->setTitle('wipcetak');


        $sheet1->getStyle("A1:L1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'Partai h');
        $sheet1->setCellValue('C1', 'No Box');
        $sheet1->setCellValue('D1', 'Tipe');
        $sheet1->setCellValue('E1', 'Grade');
        $sheet1->setCellValue('F1', 'Pcs sdh cabut');
        $sheet1->setCellValue('G1', 'Gr sdh cabut');
        $sheet1->setCellValue('H1', 'Ttl Rp');
        $sheet1->setCellValue('I1', 'Cost Cabut');
        $sheet1->setCellValue('J1', 'Pcs timbang ulang');
        $sheet1->setCellValue('K1', 'Gr timbang ulang');
        $sheet1->setCellValue('L1', 'Selesai');
        $kolom = 2;
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai");
        $cabut = $response->object();

        $wip_cetak = DB::table('gudang_ctk')->where('selesai', 'selesai')->get();

        foreach ($cabut as $d) {
            $bk = GudangBkModel::getPartaicetak($d->nm_partai);
            $gdng_ctk = DB::table('gudang_ctk')->where('no_box', $d->no_box)->where('selesai', 'selesai')->first();

            if (empty($gdng_ctk->selesai)) {
            } else {
                continue;
            }
            $sheet1->setCellValue('A' . $kolom, $gdng_ctk->id_gudang_ctk ?? '');
            $sheet1->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $d->no_box);
            $sheet1->setCellValue('D' . $kolom, $d->tipe);
            $sheet1->setCellValue('E' . $kolom, $bk->nm_grade ?? '');
            $sheet1->setCellValue('F' . $kolom, $d->pcs_akhir);
            $sheet1->setCellValue('G' . $kolom, $d->gr_akhir);
            $sheet1->setCellValue('H' . $kolom, empty($bk->total_rp) ? '0' : ($bk->total_rp / $bk->gr) * $d->gr_akhir);
            $sheet1->setCellValue('I' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('J' . $kolom, $gdng_ctk->pcs_timbang_ulang ?? 0);
            $sheet1->setCellValue('K' . $kolom, $gdng_ctk->gr_timbang_ulang ?? 0);
            $sheet1->setCellValue('L' . $kolom, 'proses');

            $kolom++;
        }
        foreach ($wip_cetak as $c) {
            $bk = GudangBkModel::getPartaicetak($c->partai_h);
            $ttl_rp = $bk->total_rp ?? 0;
            $gr = $bk->gr ?? 0;
            $sheet1->setCellValue('A' . $kolom, $c->id_gudang_ctk);
            $sheet1->setCellValue('B' . $kolom, $c->partai_h);
            $sheet1->setCellValue('C' . $kolom, $c->no_box);
            $sheet1->setCellValue('D' . $kolom, $c->tipe);
            $sheet1->setCellValue('E' . $kolom, $c->grade);
            $sheet1->setCellValue('F' . $kolom, $c->pcs_cabut);
            $sheet1->setCellValue('G' . $kolom, $c->gr_cabut);
            $sheet1->setCellValue('H' . $kolom, $c->ttl_rp == 0 ? ($ttl_rp == 0 ? 0 : ($ttl_rp / $gr) * $c->gr_cabut) : 0);
            $sheet1->setCellValue('I' . $kolom, $c->cost_cabut);
            $sheet1->setCellValue('J' . $kolom, $c->pcs_timbang_ulang ?? 0);
            $sheet1->setCellValue('K' . $kolom, $c->gr_timbang_ulang ?? 0);
            $sheet1->setCellValue('L' . $kolom, $c->selesai);

            $kolom++;
        }
        $sheet1->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);
        $namafile = "Wip Cetak.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }


    function gudangProduksiGabung(Request $r)
    {
        $gudang = GudangBkModel::getProduksiGabung();

        $listBulan = DB::table('bulan')->get();
        $id_user = auth()->user()->id;
        $data =  [
            'title' => 'Gudang BK',
            'gudang' => $gudang,
            'listbulan' => $listBulan,
            'presiden' => auth()->user()->posisi_id == 1 ? true : false,
            'nm_gudang' => 'summary_produksi'
        ];
        return view('gudang_bk.summary_produksi', $data);
    }

    function save_bj_baru(Request $r)
    {
        DB::beginTransaction();

        try {
            for ($x = 0; $x < count($r->partai); $x++) {
                $existingData = DB::table('gudang_ctk')->where('no_box', $r->no_box[$x])->exists();
                if ($existingData) {
                    throw new Exception('Data already exists in gudang ctk');
                }
                $data = [
                    'partai_h' => $r->partai[$x],
                    'no_box' => $r->no_box[$x],
                    'tipe' => $r->tipe[$x],
                    'grade' => $r->grade[$x],
                    'pcs_cabut' => $r->pcs[$x],
                    'gr_cabut' => $r->gr[$x],
                    'selesai' => 'selesai',
                    'ttl_rp' => $r->ttl_rp[$x],
                    'cost_cabut' => $r->cost_cabut[$x],
                    'suntikan' => 'Y'
                ];
                DB::table('gudang_ctk')->insert($data);
            }
            DB::commit();
            return redirect()->route('halawal.index', ['nm_gudang' => 'wip'])->with('sukses', 'Data berhasil import');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function pindah_gudang(Request $r)
    {
        for ($x = 0; $x < count($r->id_nota); $x++) {
            $data = [
                'gudang' => 'sortir'
            ];
            DB::table('gudang_ctk')->where('id_gudang_ctk', $r->id_nota[$x])->update($data);
        }

        for ($i = 0; $i < count($r->id_nota_cetak); $i++) {
            $data = [
                'gudang' => 'cetak'
            ];
            DB::table('gudang_ctk')->where('id_gudang_ctk', $r->id_nota_cetak[$i])->update($data);
        }
    }
}
