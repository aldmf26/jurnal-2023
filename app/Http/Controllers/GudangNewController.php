<?php

namespace App\Http\Controllers;

use App\Models\GudangBkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class GudangNewController extends Controller
{
    protected $tgl1, $tgl2, $period, $linkApi;
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
        $this->linkApi = "https://sarang.ptagafood.com/api/apibk";
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
        return view('gudangnew.index', $data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
        ];
        return view('gudangnew.tbh_baris', $data);
    }

    public function save_gudang_bk(Request $r)
    {
        for ($x = 0; $x < count($r->ket2); $x++) {
            $id = $r->grade[$x];
            $grade = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/detail_grade_sbw?id=$id");
            $grade = json_decode($grade, TRUE);

            $rwb_id = $r->rwb[$x];
            $rwb = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/detail_rumah_walet?id=$rwb_id");
            $rwb = json_decode($rwb, TRUE);

            dd($rwb);


            DB::table('buku_campur')->insert([
                'no_lot' => $r->lot[$x],
                'id_grade' => '1',
                'pcs' => $r->pcs[$x],
                'gr' => $r->gr[$x],
                'rupiah' => $r->rp_gram[$x],
                'ket' => $r->ket1[$x],
                'ket2' => $r->ket2[$x],
                'lok_tgl' => ' ',
                'approve' => 'Y',
                'gudang' => $r->gudang,
                'gabung' => 'T'
            ]);
            $idBukuCampur = DB::getPdo()->lastInsertId();
            DB::table('buku_campur_approve')->insert([
                'id_buku_campur' => $idBukuCampur,
                'buku' => '',
                'suplier_awal' => ' ',
                'tgl' => $r->tgl[$x],
                'nm_grade' => $grade['data']['grade']['nama'],
                'pcs' => $r->pcs[$x],
                'gr' => $r->gr[$x],
                'rupiah' => $r->rp_gram[$x],
                'no_lot' => $r->lot[$x],
                'ket' => $r->ket1[$x],
                'ket2' => $r->ket2[$x],
                'lok_tgl' => '',
                'gudang' => $r->gudang,
            ]);

            $kode_rwb = $rwb['data']['rumah_walet']['kode'];


            $kode_brg =  $grade['data']['grade']['kode'];
            DB::table('sbw_kotor')->insert(
                [
                    'grade_id' => $id,
                    'rwb_id' => $r->rwb[$x],
                    'nm_partai' => $r->ket2[$x],
                    'no_invoice' => date('d', strtotime($r->tgl[$x])) . '-' . date('mY', strtotime($r->tgl[$x])) . '-' . $kode_brg .  $kode_rwb . '-' . date('m', strtotime($r->tgl[$x] . ' +6 months')) . '-' . date('Y', strtotime($r->tgl[$x] . ' +6 months')),
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->gr[$x] / 1000,
                    'no_kendaraan' => $r->no_kendaraan[$x],
                    'pengemudi' => $r->pengemudi[$x],
                    'tgl' => $r->tgl[$x],


                ]
            );
        }

        if ($r->lokasi == 'herry') {
            return redirect()->route('gudangnew.index', ['nm_gudang' => $r->gudang])->with('sukses', 'Data berhasil ditambhkan');
        } else {
            return redirect()->route('gudangnew.gudang_p_kerja')->with('sukses', 'Data berhasil ditambhkan');
        }
    }




    public function gudang_p_kerja(Request $r)
    {
        $bk = Http::get("https://sarang.ptagafood.com/api/apibk/sum_partai");
        $bk = json_decode($bk, TRUE);
        DB::table('bk_timbang_ulang')->truncate();
        foreach ($bk as $v) {
            $data = [
                'nm_partai' => $v['nm_partai'],
                'pcs' => $v['pcs'],
                'gr' => $v['gr'],
            ];
            DB::table('bk_timbang_ulang')->insert($data);
        }
        $gudang = GudangBkModel::g_p_kerja();

        $grade = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/grade_sbw");
        $grade = json_decode($grade, TRUE);

        $rbw = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/rumah_walet");
        $rbw = json_decode($rbw, TRUE);




        $data =  [
            'title' => 'Gudang Partai Kerja',
            'gudang' => $gudang,
            'linkApi' => $this->linkApi,
            'grade' => $grade['data']['grade'],
            'rbw' => $rbw['data']['rumah_walet'],

        ];
        return view('gudangnew.p_kerja', $data);
    }
    public function export_p_kerja(Request $r)
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
        $sheet1->setTitle('Bk Opname');
        $sheet1->getStyle("A1:L1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'No');
        $sheet1->setCellValue('B1', 'Bulan kerja');
        $sheet1->setCellValue('C1', 'Nama Partai');
        $sheet1->setCellValue('D1', 'Nama Partai ko herry');
        $sheet1->setCellValue('E1', 'Grade');
        $sheet1->setCellValue('F1', 'Pcs');
        $sheet1->setCellValue('G1', 'Gr');
        $sheet1->setCellValue('H1', 'Ttl Rp');
        $sheet1->setCellValue('I1', 'Pcs Ulang');
        $sheet1->setCellValue('J1', 'Gr Ulang');
        $sheet1->setCellValue('K1', 'Susut');
        $sheet1->setCellValue('L1', 'Keterangan Opname');
        $kolom = 2;
        $gudang = GudangBkModel::g_p_kerja();
        foreach ($gudang as $no => $g) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, date('F', strtotime($g->tgl)));
            $sheet1->setCellValue('C' . $kolom, $g->ket2);
            $sheet1->setCellValue('D' . $kolom, $g->ket);
            $sheet1->setCellValue('E' . $kolom, $g->nm_grade);
            $sheet1->setCellValue('F' . $kolom, $g->pcs);
            $sheet1->setCellValue('G' . $kolom, $g->gr);
            $sheet1->setCellValue('H' . $kolom, $g->total_rp);
            $sheet1->setCellValue('I' . $kolom, $g->pcs_bk);
            $sheet1->setCellValue('J' . $kolom, $g->gr_bk);
            $sheet1->setCellValue('K' . $kolom, empty($g->gr_bk) ? 0 : round((1 - ($g->gr_bk / $g->gr)) * 100, 0));
            $sheet1->setCellValue('L' . $kolom, $g->opname_bulan);


            $kolom++;
        }
        $sheet1->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);
        $namafile = "Gudang Cabut Pengawas.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function save_bulan_opname(Request $r)
    {
        for ($x = 0; $x < count($r->partai); $x++) {
            $data = [
                'opname_bulan' => $r->ket[$x],
            ];
            DB::table('buku_campur_approve')->where('ket2', $r->partai[$x])->where('gudang', 'wip')->update($data);
        }

        return redirect()->route('gudangnew.gudang_p_kerja')->with('sukses', 'Data susut ditambhkan');
    }

    public function gudang_c_pgws(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/bikin_box");
        $cabut = $response->object();
        $data =  [
            'title' => 'Gudang Cabut Pgws',
            'cabut' => $cabut,
        ];
        return view('gudangnew.g_cbt_pgws', $data);
    }
    public function gudang_cabut(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai_new");
        $cabut = $response->object();
        $data =  [
            'title' => 'Gudang Cabut',
            'cabut' => $cabut,
        ];
        return view('gudangnew.g_cabut', $data);
    }

    public function import_buku_campur_produksi(Request $r)
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

                    if ($title === 'rencana produksi') {
                        $gudang = 'produksi';
                    } elseif ($title === 'campur produksi') {
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
                            if (empty($rowData[0]) && $gudang == 'wip') {
                                DB::table('buku_campur')->insert([
                                    'ket2' => $rowData[1],
                                    'ket' => $rowData[2],
                                    'id_grade' => '1',
                                    'pcs' => $rowData[5],
                                    'gr' => $rowData[6],
                                    'rupiah' => $rowData[7],
                                    'approve' => 'Y',
                                    'gudang' => $gudang,
                                    'gabung' => 'T'

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
                                    'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                    'nm_grade' => $rowData[4],
                                    'pcs' => $rowData[5],
                                    'gr' => $rowData[6],
                                    'rupiah' => $rowData[7],
                                    'ket' => empty($rowData[2]) ? ' ' : $rowData[2],
                                    'ket2' => empty($rowData[1]) ? ' ' : $rowData[1],
                                    'gudang' => $gudang,
                                ]);
                            } else {
                                $buku_campur = DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->first();
                                DB::table('buku_campur')->where('id_buku_campur', $rowData[0])->update([
                                    'approve' => 'Y',
                                    'gudang' => $gudang,
                                    'gabung' => 'T'
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

                                if (empty($bk_approve) && $gudang == 'wip') {
                                    DB::table('buku_campur_approve')->insert([
                                        'id_buku_campur' => $rowData[0],
                                        'ket2' => empty($rowData[1]) ? ' ' : $rowData[1],
                                        'ket' => empty($rowData[2]) ? ' ' : $rowData[2],
                                        'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                        'nm_grade' => $rowData[4],
                                        'pcs' => $rowData[5],
                                        'gr' => $rowData[6],
                                        'rupiah' => $rowData[7],
                                        'gudang' => $gudang,
                                    ]);
                                } else {
                                    if ($gudang == 'wip') {
                                        DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                                            'id_buku_campur' => $rowData[0],
                                            'ket2' => empty($rowData[1]) ? ' ' : $rowData[1],
                                            'ket' => empty($rowData[2]) ? ' ' : $rowData[2],
                                            'tgl' => empty($tanggalFormatted) ? '0000-00-00' : $tanggalFormatted,
                                            'nm_grade' => $rowData[4],
                                            'pcs' => $rowData[5],
                                            'gr' => $rowData[6],
                                            'rupiah' => $rowData[7],
                                            'lok_tgl' => empty($rowData[12]) ? ' ' : $rowData[12],
                                            'gudang' => $gudang,
                                        ]);
                                    } else {
                                        DB::table('buku_campur_approve')->where('id_buku_campur', $rowData[0])->update([
                                            'id_buku_campur' => $rowData[0],
                                            'buku' => $rowData[1],
                                            'tgl' => $rowData[2],
                                            'nm_grade' => $rowData[3],
                                            'pcs' => $rowData[4],
                                            'gr' => $rowData[5],
                                            'rupiah' => $rowData[6],
                                            'no_lot' => empty($buku_campur->no_nota) ? $rowData[7] : $buku_campur->no_lot,
                                            'ket' => empty($rowData[8]) ? ' ' : $rowData[8],
                                            'ket2' => empty($rowData[9]) ? ' ' : $rowData[9],
                                            'lok_tgl' => empty($rowData[10]) ? ' ' : $rowData[10],
                                            'gudang' => $gudang,
                                        ]);
                                    }
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
                return redirect()->route('gudangnew.gudang_p_kerja')->with('error', 'Data gagal di import');
            }
        }
    }

    public function export_g_c_pgws(Request $r)
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
        $sheet1->setTitle('G Cabut Pgws');
        $sheet1->getStyle("A1:M1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'No');
        $sheet1->setCellValue('B1', 'Partai');
        $sheet1->setCellValue('C1', 'No Box');
        $sheet1->setCellValue('D1', 'Tipe');
        $sheet1->setCellValue('E1', 'Ket');
        $sheet1->setCellValue('F1', 'Warna');
        $sheet1->setCellValue('G1', 'Tgl Terima');
        $sheet1->setCellValue('H1', 'Pengawas');
        $sheet1->setCellValue('I1', 'Penerima');
        $sheet1->setCellValue('J1', 'Pcs');
        $sheet1->setCellValue('K1', 'Gr');
        $sheet1->setCellValue('L1', 'Pcs Sisa');
        $sheet1->setCellValue('M1', 'Gr Sisa');
        $kolom = 2;
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/bikin_box");
        $cabut = $response->object();
        foreach ($cabut as $no => $g) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, $g->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $g->no_box);
            $sheet1->setCellValue('D' . $kolom, $g->tipe);
            $sheet1->setCellValue('E' . $kolom, $g->ket);
            $sheet1->setCellValue('F' . $kolom, $g->warna);
            $sheet1->setCellValue('G' . $kolom, $g->tgl);
            $sheet1->setCellValue('H' . $kolom, $g->pengawas);
            $sheet1->setCellValue('I' . $kolom, $g->name);
            $sheet1->setCellValue('J' . $kolom, $g->pcs_awal);
            $sheet1->setCellValue('K' . $kolom, $g->gr_awal);
            $sheet1->setCellValue('L' . $kolom, $g->pcs_sisa);
            $sheet1->setCellValue('M' . $kolom, $g->gr_sisa);

            $kolom++;
        }
        $sheet1->getStyle('A2:M' . $kolom - 1)->applyFromArray($style);
        $namafile = "Gudang Cabut Pengawas.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function get_no_box(Request $r)
    {
        $response = Http::get(
            "$this->linkApi/cabut_detail",
            [
                'nm_partai' => $r->nm_partai,
                'limit' => $r->limit,
            ]
        );
        $b = $response->object();
        $data =  [
            'bk' => $b,
            'linkApi' => $this->linkApi,
            'nm_partai' => $r->nm_partai,
        ];
        return view('laporan_produksi.get_box', $data);
    }

    public function export_show_box(Request $r)
    {
        $response = Http::get(
            "$this->linkApi/cabut_detail",
            [
                'nm_partai' => $r->nm_partai,
                'no_lot' => $r->no_lot,
                'limit' => $r->limit,
            ]
        );
        $b = $response->object();
        $data =  [
            'bk' => $b,
            'linkApi' => $this->linkApi
        ];
        return view('laporan_produksi.export_show', $data);
    }

    public function get_susut(Request $r)
    {
        $partai = $r->partai;
        $get = DB::table('table_susut')->where('ket', $partai)->where('gudang', 'wip')->first();
        $linkApi = $this->linkApi;
        $response = Http::get("$linkApi/datacabutsum3", ['nm_partai' => $partai]);
        $resSum = $response->object();
        $c = $resSum;

        $bk = DB::selectOne("SELECT sum(a.gr * a.rupiah) as ttl_rp
        FROM buku_campur_approve as a 
        left join buku_campur as b on b.id_buku_campur = a.id_buku_campur
        where a.gudang = 'wip' and b.gabung = 'T' and a.selesai_2 = 'T' and a.ket2 = '$partai';
        ");


        $data = [
            'partai' => $partai,
            'get_partai' => $get,
            'rp_satuan' => $bk->ttl_rp / $c->gr_awal_bk
        ];
        return view('gudangnew.get_susut', $data);
    }

    public function save_susut(Request $r)
    {
        if (!empty($r->partai_kosong)) {
            $data = [
                'pcs' => $r->pcs_susut,
                'gr' => $r->gr_susut,
                'ket' => $r->partai_kosong,
                'gudang' => 'wip'
            ];
            DB::table('table_susut')->insert($data);
            $response =  Http::post("https://sarang.ptagafood.com/api/apibk/edit_bk?partai=$r->partai_kosong&harga=$r->rp_satuan");
        } else {
            $data = [
                'pcs' => $r->pcs_susut,
                'gr' => $r->gr_susut
            ];
            DB::table('table_susut')->where('ket', $r->partai)->where('gudang', 'wip')->update($data);
            $response =  Http::post("https://sarang.ptagafood.com/api/apibk/edit_bk?partai=$r->partai&harga=$r->rp_satuan");
        }







        return redirect()->route('gudangnew.gudang_p_kerja')->with('sukses', 'Data susut ditambhkan');
    }
    public function export_g_cabut(Request $r)
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
        $sheet1->setTitle('Gudang Cabut');


        $sheet1->getStyle("A1:L1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'No');
        $sheet1->setCellValue('B1', 'Partai');
        $sheet1->setCellValue('C1', 'No Box');
        $sheet1->setCellValue('D1', 'Tipe');
        $sheet1->setCellValue('E1', 'Ket');
        $sheet1->setCellValue('F1', 'Warna');
        $sheet1->setCellValue('G1', 'Tgl Terima');
        $sheet1->setCellValue('H1', 'Pengawas');
        $sheet1->setCellValue('I1', 'Nama Anak');
        $sheet1->setCellValue('J1', 'Kelas');
        $sheet1->setCellValue('K1', 'Pcs');
        $sheet1->setCellValue('L1', 'Gr');
        $kolom = 2;
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_selesai_new");
        $cabut = $response->object();
        foreach ($cabut as $no => $g) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, $g->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $g->no_box);
            $sheet1->setCellValue('D' . $kolom, $g->tipe);
            $sheet1->setCellValue('E' . $kolom, $g->ket);
            $sheet1->setCellValue('F' . $kolom, $g->warna);
            $sheet1->setCellValue('G' . $kolom, $g->tgl_terima);
            $sheet1->setCellValue('H' . $kolom, $g->pengawas);
            $sheet1->setCellValue('I' . $kolom, $g->nama_anak);
            $sheet1->setCellValue('J' . $kolom, $g->kelas);
            $sheet1->setCellValue('K' . $kolom, $g->pcs_awal);
            $sheet1->setCellValue('L' . $kolom, $g->gr_awal);

            $kolom++;
        }
        $sheet1->getStyle('A2:L' . $kolom - 1)->applyFromArray($style);
        $namafile = "Gudang Cabut in Progress.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }


    // Laporan 

    public function laporan_produksi(Request $r)
    {
        $gudang = GudangBkModel::getSummaryWipnew();
        $listBulan = DB::table('bulan')->get();
        $data =  [
            'title' => 'Laporan Produksi',
            'gudang' => $gudang,
            'listbulan' => $listBulan,
            'linkApi' => $this->linkApi,
        ];
        return view('laporan_produksi.index', $data);
    }
    public function laporan_boxproduksi(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_laporan");
        $cabut = $response->object();
        $data =  [
            'title' => 'Laporan Box Produksi',
            'cabut' => $cabut,
        ];
        return view('laporan_produksi.cabut', $data);
    }

    public function export_laporan_boxproduksi(Request $r)
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
        $sheet1->setTitle('Gudang Cabut');


        $sheet1->getStyle("A1:U1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'no');
        $sheet1->setCellValue('B1', 'ket / nama partai');
        $sheet1->setCellValue('C1', 'no box');
        $sheet1->setCellValue('D1', 'tipe');
        $sheet1->setCellValue('E1', 'pengawas');
        $sheet1->setCellValue('F1', 'pcs bk');
        $sheet1->setCellValue('G1', 'gr bk');
        $sheet1->setCellValue('H1', 'pcs awal cbt');
        $sheet1->setCellValue('I1', 'gr awal cbt');
        $sheet1->setCellValue('J1', 'pcs akhir cbt');
        $sheet1->setCellValue('K1', 'gr akhir cbt');
        $sheet1->setCellValue('L1', 'eot');
        $sheet1->setCellValue('M1', 'flx');
        $sheet1->setCellValue('N1', 'sst%');
        $sheet1->setCellValue('O1', 'cost cbt');
        $sheet1->setCellValue('P1', 'gr awal eo');
        $sheet1->setCellValue('Q1', 'gr akhir eo');
        $sheet1->setCellValue('R1', 'sst%');
        $sheet1->setCellValue('S1', 'cost eo');
        $sheet1->setCellValue('T1', 'pcs sisa');
        $sheet1->setCellValue('U1', 'gr sisa');
        $kolom = 2;

        $response = Http::get("https://sarang.ptagafood.com/api/apibk/cabut_laporan");
        $cabut = $response->object();

        foreach ($cabut as $no => $c) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, $c->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $c->no_box);
            $sheet1->setCellValue('D' . $kolom, $c->tipe);
            $sheet1->setCellValue('E' . $kolom, $c->name);
            $sheet1->setCellValue('F' . $kolom, $c->pcs_awal);
            $sheet1->setCellValue('G' . $kolom, $c->gr_awal);

            $sheet1->setCellValue('H' . $kolom, $c->pcs_awal_cbt);
            $sheet1->setCellValue('I' . $kolom, $c->gr_awal_cbt);
            $sheet1->setCellValue('J' . $kolom, $c->pcs_akhir_cbt);
            $sheet1->setCellValue('K' . $kolom, $c->gr_akhir_cbt);
            $sheet1->setCellValue('L' . $kolom, $c->eot ?? 0);
            $sheet1->setCellValue('M' . $kolom, $c->flx ?? 0);
            $sheet1->setCellValue('N' . $kolom, $c->gr_awal_cbt == 0 ? 0 : round((1 - $c->gr_akhir_cbt / $c->gr_awal_cbt) * 100, 1));
            $sheet1->setCellValue('O' . $kolom, $c->cost_cabut ?? 0);
            $sheet1->setCellValue('P' . $kolom, $c->gr_eo_awal ?? 0);
            $sheet1->setCellValue('Q' . $kolom, $c->gr_eo_akhir ?? 0);
            $sheet1->setCellValue('R' . $kolom, $c->gr_eo_awal == 0 ? 0 : round((1 - $c->gr_eo_akhir / $c->gr_eo_awal) * 100, 0));
            $sheet1->setCellValue('S' . $kolom, $c->cost_eo ?? 0);
            $sheet1->setCellValue('T' . $kolom, $c->pcs_awal - $c->pcs_awal_cbt);
            $sheet1->setCellValue('U' . $kolom, $c->gr_awal - $c->gr_awal_cbt - $c->gr_eo_awal);
            $kolom++;
        }
        $sheet1->getStyle('A2:U' . $kolom - 1)->applyFromArray($style);

        $namafile = "Laporan Box Produksi.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
