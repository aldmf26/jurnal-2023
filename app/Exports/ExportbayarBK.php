<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportbayarBK implements FromView, WithEvents
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
        $pembelian = DB::select("SELECT a.tgl, a.no_nota, a.suplier_akhir, a.total_harga, a.lunas,  c.qty, a.lunas, b.nm_suplier
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.qty) as qty  FROM pembelian as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where  a.tgl between '$this->tgl1' and '$this->tgl2'
        order by a.no_nota ASC;");


        return view('exports.bayarBK', [
            'pembelian' => $pembelian,
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
                        'size'  =>  12,
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
                        'size'  =>  12,
                        'bold' => false
                    ]
                ]);
            },
        ];
    }
}
