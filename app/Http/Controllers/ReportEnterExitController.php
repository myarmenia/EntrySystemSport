<?php

namespace App\Http\Controllers;

use App\Services\ReportFilterService;
use Illuminate\Http\Request;

class ReportEnterExitController extends Controller
{
    public function __construct(protected ReportFilterService $service){}
    public function __invoke(Request $request){

        // dd($request->all());
        $data = $this->service->filterService($request->all());

       // dd($data);
        return view('report.enter_exit', compact('data'));

    }
}
