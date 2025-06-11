<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class GradeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Grade',
            'grade' => DB::table('grade_congan')->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')->orderBy('grade_congan.aktif', 'DESC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')
                ->select('grade_congan.*', 'kategori.nm_kategori')
                ->get(),
            'kategori' => DB::table('kategori')->get()
        ];
        return view('grade.index', $data);
    }

    public function load_row(Request $r)
    {
        $data = [
            'kategori' => DB::table('kategori')->get(),
            'count' => $r->count
        ];
        return view('grade.load_row', $data);
    }

    public function save(Request $r)
    {

        for ($i = 0; $i < count($r->kategori); $i++) {
            $data = [
                'kategori_id' => $r->kategori[$i],
                'nm_grade' => $r->nm_grade[$i],
                'urutan' => $r->urutan[$i],
                'aktif' => 'Y'
            ];
            DB::table('grade_congan')->insert($data);
        }

        return redirect()->route('grade.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function non(Request $r)
    {

        for ($i = 0; $i < count($r->id); $i++) {
            DB::table('grade_congan')->where('id_grade_cong', $r->id[$i])->update([
                'aktif' => 'T'
            ]);
        }
        return redirect()->route('grade.index')->with('sukses', 'Data Berhasil DInonaktifkan');
    }

    public function getEdit(Request $r)
    {
        $data = [
            'id' => $r->id,
            'grade' => DB::table('grade_congan')->where('id_grade_cong', $r->id)->first(),
            'kategori' => DB::table('kategori')->get()
        ];
        return view('grade.getEdit', $data);
    }

    public function Edit(Request $r)
    {
        $data = [
            'kategori_id' => $r->kategori,
            'nm_grade' => $r->nm_grade,
            'urutan' => $r->urutan,
        ];
        DB::table('grade_congan')->where('id_grade_cong', $r->id)->update($data);
        return redirect()->route('grade.index')->with('sukses', 'Data Berhasil Diubah');
    }

    public function export_grade(Request $r)
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
        $sheet1->setTitle('Grade aktif');

        $sheet1->getStyle("A1:D1")->applyFromArray($style_atas);
        $sheet1->getStyle("G1:H1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'Kategori ID');
        $sheet1->setCellValue('C1', 'Grade');
        $sheet1->setCellValue('D1', 'Urutan');

        $sheet1->setCellValue('G1', 'ID');
        $sheet1->setCellValue('H1', 'Kategori');
        $kolom = 2;
        $grade = DB::table('grade_congan')->where('aktif', 'Y')->orderBy('kategori_id', 'ASC')->orderBy('urutan', 'ASC')->get();
        foreach ($grade as $d) {
            $sheet1->setCellValue('A' . $kolom, $d->id_grade_cong);
            $sheet1->setCellValue('B' . $kolom, $d->kategori_id);
            $sheet1->setCellValue('C' . $kolom, $d->nm_grade);
            $sheet1->setCellValue('D' . $kolom, $d->urutan);
            $kolom++;
        }
        $sheet1->getStyle('A2:D' . $kolom - 1)->applyFromArray($style);
        $kolom = 2;
        $kategori = DB::table('kategori')->get();
        foreach ($kategori as $d) {
            $sheet1->setCellValue('G' . $kolom, $d->id);
            $sheet1->setCellValue('H' . $kolom, $d->nm_kategori);
            $kolom++;
        }


        $sheet1->getStyle('G2:H' . $kolom - 1)->applyFromArray($style);



        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Grade non aktif');

        $sheet3->getStyle("A1:D1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'ID');
        $sheet3->setCellValue('B1', 'Kategori ID');
        $sheet3->setCellValue('C1', 'Grade');
        $sheet3->setCellValue('D1', 'Urutan');




        $kolom = 2;
        $grade2 = DB::table('grade_congan')->where('aktif', 'T')->orderBy('kategori_id', 'ASC')->orderBy('urutan', 'ASC')->get();
        foreach ($grade2 as $d) {
            $sheet3->setCellValue('A' . $kolom, $d->id_grade_cong);
            $sheet3->setCellValue('B' . $kolom, $d->kategori_id);
            $sheet3->setCellValue('C' . $kolom, $d->nm_grade);
            $sheet3->setCellValue('D' . $kolom, $d->urutan);

            $kolom++;
        }

        $sheet3->getStyle('A2:D' . $kolom - 1)->applyFromArray($style);



        $namafile = "Grade Congan.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function import_grade(Request $r)
    {
        $hasError = false;
        $kategoriIdsFromExcel = []; // penampung ID kategori dari file Excel

        if ($r->hasFile('file')) {
            $file = $r->file('file');
            $filePath = $file->storeAs('temp', 'imported_file.xlsx');

            DB::beginTransaction(); // Mulai transaction

            try {
                $spreadsheet = IOFactory::load(storage_path("app/{$filePath}"));
                $sheetNames = $spreadsheet->getSheetNames();

                foreach ($sheetNames as $sheetName) {
                    $currentSheet = $spreadsheet->getSheetByName($sheetName);
                    $title = $currentSheet->getTitle();

                    // Tentukan aktif atau tidak berdasarkan nama sheet
                    if ($title === 'Grade aktif') {
                        $aktif = 'Y';
                    } elseif ($title === 'Grade non aktif') {
                        $aktif = 'T';
                    } else {
                        continue;
                    }

                    foreach ($currentSheet->getRowIterator() as $rowIndex => $row) {
                        if ($rowIndex === 1) continue;

                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);

                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        // Proses Grade (Kolom A-D)
                        $id = $rowData[0] ?? null;
                        $kategori_id = $rowData[1] ?? null;
                        $nm_grade = $rowData[2] ?? null;
                        $urutan = $rowData[3] ?? null;

                        if (!empty($kategori_id) && !empty($nm_grade)) {
                            if (empty($id)) {
                                DB::table('grade_congan')->insert([
                                    'kategori_id' => $kategori_id,
                                    'nm_grade' => $nm_grade,
                                    'urutan' => $urutan,
                                    'aktif' => $aktif,
                                ]);
                            } else {
                                DB::table('grade_congan')->updateOrInsert(
                                    ['id_grade_cong' => $id],
                                    [
                                        'kategori_id' => $kategori_id,
                                        'nm_grade' => $nm_grade,
                                        'urutan' => $urutan,
                                        'aktif' => $aktif,
                                    ]
                                );
                            }
                        }

                        // Proses Kategori (hanya di Grade aktif)
                        if ($title === 'Grade aktif') {
                            $kategoriId = $rowData[6] ?? null;
                            $kategoriNama = $rowData[7] ?? null;

                            if (!empty($kategoriId) && !empty($kategoriNama)) {
                                DB::table('kategori')->updateOrInsert(
                                    ['id' => $kategoriId],
                                    ['nm_kategori' => $kategoriNama]
                                );

                                // Simpan ID kategori yang muncul di file
                                $kategoriIdsFromExcel[] = $kategoriId;
                            }
                        }
                    }
                }

                // Setelah semua selesai, hapus kategori yang tidak ada di file Excel
                if (!empty($kategoriIdsFromExcel)) {
                    DB::table('kategori')
                        ->whereNotIn('id', $kategoriIdsFromExcel)
                        ->delete();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $hasError = true;
                return redirect()->route('grade.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
            } finally {
                unlink(storage_path("app/{$filePath}"));
            }
        }

        return redirect()->route('grade.index')->with('sukses', 'Data berhasil diimport');
    }
}
