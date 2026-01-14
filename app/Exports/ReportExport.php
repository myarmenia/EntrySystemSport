<?php

namespace App\Exports;

use App\Traits\ReportTrait;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements  FromCollection, WithHeadings,WithStyles
{
    use ReportTrait;
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $request;
    protected $year;
    protected $month;
    public function __construct($request,$year=null, $month=null)
    {
        $this->request = $request;
        $this->year = $year ?? now()->year;
        $this->month = $month ?? now()->month;
        // dd($this->year, $this->month);

    }
    public function collection()
    {
        // Use the trait's report logic
        // dd($this->request);
        $groupedEntries = $this->report($this->request);
        // dd($groupedEntries);
        $lastElement = end($groupedEntries);
        // dd($lastElement);
        $this->month = $lastElement;
        // dd($this->month);

        unset($groupedEntries['mounth']);
        // dd($lastElement, $groupedEntries);


        $exportData = [];
        $i=0;
        foreach($groupedEntries as $peopleId =>$item){
            $i++;
            // dd($peopleId,$item);
            $dataRow = [
                "number"=>$i,
                'id' => $peopleId,
                'name' => getPeople($peopleId)->name ?? null ,
                'surname' => getPeople($peopleId)->surname ?? null,

            ];
            $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;
            // dd($daysInMonth);
            for ($day = 1; $day <= $daysInMonth; $day++) {

                if(isset($item[$day])){
                    if(isset($item[$day]['delay_display']) && isset($item[$day]['coming'])){
                        $dataRow[ $day] = "+ուշ";
                    }else if(isset($item[$day]['coming'])){
                        $dataRow[ $day] = "+";
                    }else if(isset($item[$day]['anomalia'])){
                        $dataRow[ $day] = "?";
                    }



                }else{
                    $dataRow[ $day] =null;
                }


            }

            $dataRow["totalMonthDayCount"]=$item['totalMonthDayCount'];
            $dataRow["totalWorkingTimePerPerson"]=$item['totalWorkingTimePerPerson'];
            $dataRow["totaldelayPerPerson"]=$item['totaldelayPerPerson'];

            $exportData[] = $dataRow;

        }
        // dd($exportData);
        return collect($exportData);
    }
    public function headings(): array
    {
        // Dynamically create headers including each day from 1 to 31
        $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;
        $dayHeaders = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayHeaders[] =   $day;
        }

        return array_merge(
            ['Հ/Հ',	'ID','Անուն','Ազգանուն' ],  // Fixed headers
            $dayHeaders ,['Օրերի քանակ','ժամերի քանակ','Ուշացման ժամանակի գումար'] // Day headers (Day 1 to Day 31)
        );
    }


    public function styles(Worksheet $sheet)
    {
        // Start styling from the second row onward (data rows)
        $rowStart = 2;
        // return [
        //     1=>['font'=>['bold'=>true]]
        // ];


        // Loop through rows and check for "+", then set the style
        foreach ($sheet->getRowIterator($rowStart) as $row) {
            $rowIndex = $row->getRowIndex();


            foreach ($sheet->getColumnIterator('A') as $column) { // Starting from the "D" column
                $cell = $sheet->getCell($column->getColumnIndex() . $rowIndex);
                if ($cell->getValue() == "+ուշ") {
                    $sheet->getStyle($cell->getCoordinate())->getFont()->getColor()->setRGB('FF0000'); // Red
                }

            }
        }
    }
}
