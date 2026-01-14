<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSheet;
use App\Services\ReportFilterService;
use Illuminate\Http\Request;

class ReportFilterController extends Controller
{
    /**
     * Handle the incoming request.
     */

    public function __construct(protected ReportFilterService $service){}

    public function __invoke(Request $request)
    {
        // dd($request->all());
        $data = $this->service->filterService($request->all());
        // dd($data);


        return view('report.reportFilter',compact('data'));
    }
}
