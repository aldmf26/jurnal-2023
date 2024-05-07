<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class GudangGradingController extends Controller
{
    protected $link = "https://sarang.ptagafood.com";
    public function getApi()
    {
        return Http::get("$this->link/api/gudang_grading")->object();
    }

    public function index()
    {

        $data = [
            'title' => 'Gudang Siap Grading',
            'datas' => $this->getApi()
        ];
        return view('gudang_grading.index', $data);
    }

    public function export()
    {
        $get = $this->getApi();
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

        // sheet siap grading awal
        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Siap Grading Awal');
        $sheet1->getStyle("A1:G1")->applyFromArray($style_atas);

        // array kolom
        $koloms = [
            'A' => 'tipe',
            'B' => 'no box',
            'C' => 'pcs',
            'D' => 'gr',
            'E' => 'ttl rp',
            'F' => 'cost cbt',
            'G' => 'cost ctk',
        ];
        foreach ($koloms as $kolom => $k) {
            $sheet1->setCellValue("$kolom" . '1', $k);
        }

        $row = 2;
        foreach ($get->cetak as $d) {
            $sheet1->setCellValue("A$row", $d->tipe);
            $sheet1->setCellValue("B$row", $d->no_box);
            $sheet1->setCellValue("C$row", $d->pcs_akhir);
            $sheet1->setCellValue("D$row", $d->gr_akhir);
            $sheet1->setCellValue("E$row", $d->total_rp);
            $sheet1->setCellValue("F$row", $d->cost_cabut);
            $sheet1->setCellValue("G$row", $d->cost_cetak);
            $row++;
        }
        foreach ($get->cabut_selesai as $d) {
            $sheet1->setCellValue("A$row", $d->tipe);
            $sheet1->setCellValue("B$row", $d->no_box);
            $sheet1->setCellValue("C$row", $d->pcs_akhir);
            $sheet1->setCellValue("D$row", $d->gr_akhir);
            $sheet1->setCellValue("E$row", $d->total_rp);
            $sheet1->setCellValue("F$row", $d->cost_cabut);
            $sheet1->setCellValue("G$row", $d->cost_cetak);
            $row++;
        }
        foreach ($get->suntikan as $d) {
            $sheet1->setCellValue("A$row", $d->tipe);
            $sheet1->setCellValue("B$row", $d->no_box);
            $sheet1->setCellValue("C$row", $d->pcs_akhir);
            $sheet1->setCellValue("D$row", $d->gr_akhir);
            $sheet1->setCellValue("E$row", $d->total_rp);
            $sheet1->setCellValue("F$row", $d->cost_cabut);
            $sheet1->setCellValue("G$row", $d->cost_cetak);
            $row++;
        }
        $sheet1->getStyle('A2:G' . $row - 1)->applyFromArray($style);

        // sheet Selesai Grading Awal
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Selesai Grading Awal');
        $sheet2->getStyle('A1:G1')->applyFromArray($style_atas);

        $koloms = [
            'A' => 'tipe',
            'B' => 'no box',
            'C' => 'pcs',
            'D' => 'gr',
            'E' => 'ttl rp',
            'F' => 'cost cbt',
            'G' => 'cost ctk',
        ];
        foreach ($koloms as $kolom => $k) {
            $sheet2->setCellValue("$kolom" . '1', $k);
        }
        $row = 2;
        foreach ($get->grading_selesai as $d) {
            $sheet2->setCellValue("A$row", $d->tipe);
            $sheet2->setCellValue("B$row", $d->no_box);
            $sheet2->setCellValue("C$row", $d->pcs_awal);
            $sheet2->setCellValue("D$row", $d->gr_awal);
            $sheet2->setCellValue("E$row", $d->ttl_rp);
            $sheet2->setCellValue("F$row", $d->cost_cabut);
            $sheet2->setCellValue("G$row", $d->cost_cetak);
            $row++;
        }
        $sheet2->getStyle('A2:G' . $row - 1)->applyFromArray($style);


        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Selesai Grading Awal');
        $sheet3->getStyle('A1:E1')->applyFromArray($style_atas);

        $koloms = [
            'A' => 'grade',
            'B' => 'pcs',
            'C' => 'gr',
            'D' => 'rp gr',
            'E' => 'ttl rp',
        ];
        foreach ($koloms as $kolom => $k) {
            $sheet3->setCellValue("$kolom" . '1', $k);
        }
        $row = 2;
        foreach ($get->gudangBj as $g) {
            $sheet3->setCellValue("A$row", $g->grade);
            $sheet3->setCellValue("B$row", $g->pcs - $g->pcs_kredit);
            $sheet3->setCellValue("C$row", $g->gr - $g->gr_kredit);
            $sheet3->setCellValue("D$row", ($g->ttl_rp - $g->ttl_rp_kredit) / ($g->gr - $g->gr_kredit));
            $sheet3->setCellValue("E$row", $g->ttl_rp - $g->ttl_rp_kredit);
            $row++;
        }
        $sheet3->getStyle('A2:E' . $row - 1)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet4 = $spreadsheet->getActiveSheet(2);
        $sheet4->setTitle('Sudah Jadi Box Kecil');
        $sheet4->getStyle('A1:E1')->applyFromArray($style_atas);

        $koloms = [
            'A' => 'grade',
            'B' => 'pcs',
            'C' => 'gr',
            'D' => 'rp gr',
            'E' => 'ttl rp',
        ];
        foreach ($koloms as $kolom => $k) {
            $sheet4->setCellValue("$kolom" . '1', $k);
        }
        $row = 2;
        foreach ($get->historyBoxKecil as $g) {
            $sheet4->setCellValue("A$row", $g->grade);
            $sheet4->setCellValue("B$row", $g->pcs);
            $sheet4->setCellValue("C$row", $g->gr);
            $sheet4->setCellValue("D$row", $g->rp_gram);
            $sheet4->setCellValue("E$row", $g->gr * $g->rp_gram);
            $row++;
        }
        $sheet4->getStyle('A2:E' . $row - 1)->applyFromArray($style);


        $namafile = "Gudang Grading.xlsx";
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }



    public function selesai()
    {
        $data = [
            'title' => 'Gudang Selesai Grading',
            'gudangbj' => $this->getApi()->gudangBj,
            'historyBoxKecil' => $this->getApi()->historyBoxKecil,
        ];
        return view('gudang_grading.selesai', $data);
    }

    public function create_suntikan(Request $r)
    {
        $response = Http::post('https://sarang.ptagafood.com/api/saveSuntikanGrading', $r->all());
    }
}
