<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportEnterExitExport implements FromView, ShouldAutoSize
// , WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View{

            return view('export.exportEnterExit',[

                "mounth" => $this->data['month'],
                "groupedEntries" => $this->data['attendance_sheet'],
                "i" => 0

            ]);

    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }
}
