<?php

namespace App\Http\Controllers;

use App\Exports\ReportEnterExitExport;
use App\Services\ReportFilterService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportEnterExitExportController extends Controller
{
    public function __construct(protected ReportFilterService $service){}

    public function __invoke(Request $request)
    {

        $data = $this->service->filterService($request->all());

        // dd($data);
        return Excel::download(new ReportEnterExitExport( $data), 'Հաշվետվություն-մուտքի-ելքի.xlsx');

    }
}
