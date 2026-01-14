<?php

namespace App\Http\Controllers;

use App\Exports\ReportFilterExport;
use App\Services\ReportFilterService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportFilterExportController extends Controller
{

    public function __construct(protected ReportFilterService $service){}

    public function __invoke(Request $request)
    {
        // dd($request->all());


        $data = $this->service->filterService($request->all());
        // dd($data);


        return Excel::download(new ReportFilterExport( $data), 'Հաշվետվություն.xlsx');

    }

}
