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


class BkinExport  implements FromView, WithEvents
{
    protected $tgl1;
    protected $tgl2;
    protected $totalrow;

    public function __construct($tgl1, $tgl2, $totalrow)
    {
        $this->tgl1 = $tgl1;
        $this->tgl2 = $tgl2;
        $this->totalrow = $totalrow;
    }

    public function view(): View
    {
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
            where a.tgl between '$this->tgl1' and '$this->tgl2'
            order by a.no_nota ASC");


        return view('exports.pembelian', [
            'pembelian' => $pembelian,
        ]);
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $totalrow = $this->totalrow + 1;
                $cellRange = 'A1:V1';
                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->setAutoFilter($cellRange);
                $event->sheet->getStyle('A1:V1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  12,
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A2:V' . $totalrow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  12,
                        'bold' => false
                    ]
                ]);
            },
        ];
    }
}
