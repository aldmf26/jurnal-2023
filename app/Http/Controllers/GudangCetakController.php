<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;




class GudangCetakController extends Controller
{
    public function index(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai_g_cetak");
        $cabut = $response->object();

        $cetak_suntik = DB::table('gudang_ctk')->where('suntik', 'suntik')->where('gudang', 'cetak')->get();

        $data =  [
            'title' => 'Gudang Cetak',
            'cabut' => $cabut,
            'cetak' => $cetak_suntik
        ];
        return view('gudangcetak.index', $data);
    }

    public function masuk_bk_grading(Request $r)
    {

        foreach ($r->no_box as $g) {
            $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai_g_cetak_nota?no_box=$g");
            $c = $response->object();

            $box = DB::table('gudang_ctk')->where('no_box', $g)->where('suntik', 'suntik')->first();

            if (!empty($box->no_box)) {
                // DB::table('gudang_ctk')->where('no_box', $g)->delete();
                $data = [
                    'gudang' => 'sortir',
                ];
                DB::table('gudang_ctk')->where('no_box', $g)->update($data);
            } else {
                DB::table('gudang_ctk')->where('no_box', $g)->delete();
                $data = [
                    'partai_h' => $c->nm_partai,
                    'no_box' => $c->no_box,
                    'tipe' => $c->tipe,
                    'pcs_cabut' => $c->pcs_akhir,
                    'gr_cabut' => $c->gr_akhir,
                    'cost_cabut' => $c->ttl_rp,
                    'pcs_timbang_ulang' => $c->pcs_akhir,
                    'gr_timbang_ulang' => $c->gr_akhir,
                    'selesai' => 'selesai',
                    'gudang' => 'sortir',
                ];

                DB::table('gudang_ctk')->insert($data);
            }
        }
        return redirect()->route('gudangcetak.index')->with('sukses', 'Data berhasil ditambhkan ke grading');
    }

    public function export_g_cetak(Request $r)
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
        $sheet1->setTitle('Gudang Cabut Selesai');


        $sheet1->getStyle("A1:L1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'No');
        $sheet1->setCellValue('B1', 'Partai');
        $sheet1->setCellValue('C1', 'No Box');
        $sheet1->setCellValue('D1', 'Tipe');
        $sheet1->setCellValue('E1', 'Pengawas');
        $sheet1->setCellValue('F1', 'Nama Anak');
        $sheet1->setCellValue('G1', 'Kelas');
        $sheet1->setCellValue('H1', 'Pcs');
        $sheet1->setCellValue('I1', 'Gr');
        $sheet1->setCellValue('J1', 'Cost Cabut');
        $sheet1->setCellValue('K1', 'Pcs timbang ulang');
        $sheet1->setCellValue('L1', 'Gr timbang ulang');
        $kolom = 2;
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai_g_cetak");
        $cabut = $response->object();

        $cetak_suntik = DB::table('gudang_ctk')->where('suntik', 'suntik')->where('gudang', 'cetak')->get();

        $no = 1;
        foreach ($cabut as  $g) {
            $gdng_ctk = DB::table('gudang_ctk')
                ->where('no_box', $g->no_box)
                ->first();

            if (isset($gdng_ctk) && $gdng_ctk->gudang == 'sortir') {
                continue;
            }
            $sheet1->setCellValue('A' . $kolom, $no);
            $sheet1->setCellValue('B' . $kolom, $g->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $g->no_box);
            $sheet1->setCellValue('D' . $kolom, $g->tipe);
            $sheet1->setCellValue('E' . $kolom, $g->name);
            $sheet1->setCellValue('F' . $kolom, $g->nama);
            $sheet1->setCellValue('G' . $kolom, $g->id_kelas);
            $sheet1->setCellValue('H' . $kolom, $g->pcs_akhir);
            $sheet1->setCellValue('I' . $kolom, $g->gr_akhir);
            $sheet1->setCellValue('J' . $kolom, $g->ttl_rp);
            $sheet1->setCellValue('K' . $kolom, $gdng_ctk->pcs_timbang_ulang ?? 0);
            $sheet1->setCellValue('L' . $kolom, $gdng_ctk->gr_timbang_ulang ?? 0);
            $no++;
            $kolom++;
        }
        foreach ($cetak_suntik as  $c) {
            $sheet1->setCellValue('A' . $kolom, $no);
            $sheet1->setCellValue('B' . $kolom, $c->partai_h);
            $sheet1->setCellValue('C' . $kolom, $c->no_box);
            $sheet1->setCellValue('D' . $kolom, $c->tipe);
            $sheet1->setCellValue('E' . $kolom, '-');
            $sheet1->setCellValue('F' . $kolom, '-');
            $sheet1->setCellValue('G' . $kolom, '-');
            $sheet1->setCellValue('H' . $kolom, $c->pcs_cabut);
            $sheet1->setCellValue('I' . $kolom, $c->gr_cabut);
            $sheet1->setCellValue('J' . $kolom, $c->cost_cabut);
            $sheet1->setCellValue('K' . $kolom, $c->pcs_timbang_ulang);
            $sheet1->setCellValue('L' . $kolom, $c->pcs_timbang_ulang);
            $no++;
            $kolom++;
        }
        $sheet1->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);
        $namafile = "Gudang Cetak.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function import_bk_ctk(Request $r)
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
                DB::table('gudang_ctk')->where('no_box', $row[2])->delete();

                DB::table('gudang_ctk')->insert([
                    'partai_h' => $row[1],
                    'no_box' => $row[2],
                    'tipe' => $row[3] ?? ' ',
                    // 'grade' => $row[4],
                    'pcs_cabut' => $row[7],
                    'gr_cabut' => $row[8],
                    'ttl_rp' => '0',
                    'cost_cabut' => $row[9],
                    'pcs_timbang_ulang' => $row[10],
                    'gr_timbang_ulang' => $row[11],
                    'selesai' => 'selesai',
                    'gudang' => 'cetak'
                ]);
            }
            DB::commit();
            return redirect()->route('gudangcetak.index')->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function save_cetak(Request $r)
    {
        try {
            for ($x = 0; $x < count($r->no_box); $x++) {
                $box = DB::table('gudang_ctk')->where('no_box', $r->no_box[$x])->first();
                if (!empty($box->no_box)) {
                    throw new Exception("Nomor Box '{$r->no_box[$x]}' sudah ada.");
                } else {
                    $data = [
                        'partai_h' => $r->partai[$x],
                        'no_box' => $r->no_box[$x],
                        'tipe' => $r->tipe[$x],
                        'pcs_cabut' => $r->pcs[$x],
                        'gr_cabut' => $r->gr[$x],
                        'pcs_timbang_ulang' => $r->pcs[$x],
                        'gr_timbang_ulang' => $r->gr[$x],
                        'cost_cabut' => $r->cost_cabut[$x],
                        'ttl_rp' => $r->ttl_rp[$x],
                        'suntik' => 'suntik'
                    ];
                    DB::table('gudang_ctk')->insert($data);
                }
            }
            return redirect()->route('gudangcetak.index')->with('sukses', 'Data berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    // 

    public function g_ctk_pgws(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cetak_pgws");
        $cetak = $response->object();

        $data =  [
            'title' => 'Gudang Cetak',
            'cetak' => $cetak,
        ];
        return view('gudangcetak.cetak_pgws', $data);
    }
    public function g_ctk_in_progres(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cetak_belum_selesai");
        $cetak = $response->object();

        $data =  [
            'title' => 'Gudang cetak in progress',
            'cetak' => $cetak,
        ];
        return view('gudangcetak.cetak_in_progress', $data);
    }

    // Laporan
    public function lap_box_cetak(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cetak_laporan");
        $cabut = $response->object();
        $data =  [
            'title' => 'Laporan Box Produksi',
            'cabut' => $cabut,
        ];
        return view('laporan_produksi.cetak', $data);
    }

    public function export_laporan_boxproduksicetak(Request $r)
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
        $sheet1->setTitle('Gudang Cetak');


        $sheet1->getStyle("A1:U1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'no');
        $sheet1->setCellValue('B1', 'ket / nama partai');
        $sheet1->setCellValue('C1', 'no box');
        $sheet1->setCellValue('D1', 'tipe');
        $sheet1->setCellValue('E1', 'pengawas');
        $sheet1->setCellValue('F1', 'pcs bk');
        $sheet1->setCellValue('G1', 'gr bk');

        $sheet1->setCellValue('H1', 'pcs awal ctk');
        $sheet1->setCellValue('I1', 'gr awal ctk');
        $sheet1->setCellValue('J1', 'pcs tdk ctk');
        $sheet1->setCellValue('K1', 'gr tdk ctk');
        $sheet1->setCellValue('L1', 'pcs awal ctk');
        $sheet1->setCellValue('M1', 'gr awal ctk');
        $sheet1->setCellValue('N1', 'pcs cu');
        $sheet1->setCellValue('O1', 'gr cu');
        $sheet1->setCellValue('P1', 'pcs akhir ctk');
        $sheet1->setCellValue('Q1', 'gr akhir ctk');

        $sheet1->setCellValue('R1', 'sst%');
        $sheet1->setCellValue('S1', 'cost ctk');
        $sheet1->setCellValue('T1', 'pcs sisa');
        $sheet1->setCellValue('U1', 'gr sisa');
        $kolom = 2;

        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cetak_laporan");
        $cabut = $response->object();

        foreach ($cabut as $no => $c) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, $c->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $c->no_box);
            $sheet1->setCellValue('D' . $kolom, $c->tipe);
            $sheet1->setCellValue('E' . $kolom, $c->name);
            $sheet1->setCellValue('F' . $kolom, $c->pcs_awal);
            $sheet1->setCellValue('G' . $kolom, $c->gr_awal);

            $sheet1->setCellValue('H' . $kolom, $c->pcs_awal_ambil);
            $sheet1->setCellValue('I' . $kolom, $c->gr_awal_ambil);
            $sheet1->setCellValue('J' . $kolom, $c->pcs_tdk_ctk);
            $sheet1->setCellValue('K' . $kolom, $c->gr_tdk_ctk);
            $sheet1->setCellValue('L' . $kolom, $c->pcs_awal_ctk);
            $sheet1->setCellValue('M' . $kolom, $c->gr_awal_ctk);
            $sheet1->setCellValue('N' . $kolom, $c->pcs_cu);
            $sheet1->setCellValue('O' . $kolom, $c->gr_cu);
            $sheet1->setCellValue('P' . $kolom, $c->pcs_akhir);
            $sheet1->setCellValue('Q' . $kolom, $c->gr_akhir);

            $sheet1->setCellValue('R' . $kolom, $c->gr_awal_ctk == 0 ? 0 : round((1 - (($c->gr_akhir + $c->gr_cu) / $c->gr_awal_ctk)) * 100, 1));

            $sheet1->setCellValue('S' . $kolom, $c->ttl_rp ?? 0);
            $sheet1->setCellValue('T' . $kolom, $c->pcs_awal - $c->pcs_awal_ambil);
            $sheet1->setCellValue('U' . $kolom, $c->gr_awal - $c->gr_awal_ambil);
            $kolom++;
        }
        $sheet1->getStyle('A2:U' . $kolom - 1)->applyFromArray($style);

        $namafile = "Laporan Box Produksi Cetak.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
