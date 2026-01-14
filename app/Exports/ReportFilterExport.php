<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportFilterExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

    public function view(): View
    {
        // dd($this->data);
        return view('export.exportReportFilter', [
            'data' => $this->data,

        ]);
    }
}
