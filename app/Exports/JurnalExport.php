<?php

namespace App\Exports;

use App\Models\Jurnal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class JurnalExport  implements FromView, WithEvents
{
    protected $tgl1;
    protected $tgl2;
    protected $id_proyek;
    protected $id_buku;
    protected $totalrow;

    public function __construct($tgl1, $tgl2, $id_proyek, $id_buku, $totalrow)
    {
        $this->tgl1 = $tgl1;
        $this->tgl2 = $tgl2;
        $this->id_proyek = $id_proyek;
        $this->id_buku = $id_buku;
        $this->totalrow = $totalrow;
    }

    public function view(): View
    {

        $idp = $this->id_proyek == 0 ? '' : "and a.id_proyek = $this->id_proyek";

        $jurnal =  DB::select("SELECT a.admin, a.no_urut, b.kode_akun,  a.id_akun, a.tgl, a.debit, a.kredit, a.ket,a.no_nota, b.nm_akun, c.nm_post FROM jurnal as a 
            left join akun as b on b.id_akun = a.id_akun
            left join tb_post_center as c on c.id_post_center = a.id_post_center
            where a.id_buku = '$this->id_buku' and a.tgl between '$this->tgl1' and '$this->tgl2' $idp order by a.no_urut ASC");


        return view('exports.jurnal', [
            'jurnal' => $jurnal,
        ]);
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $totalrow = $this->totalrow + 1;
                $cellRange = 'A1:L1';
                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->setAutoFilter($cellRange);
                $event->sheet->getStyle('A1:L1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  10,
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A2:L' . $totalrow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  10,
                        'bold' => false
                    ]
                ]);
            },
        ];
    }
}
