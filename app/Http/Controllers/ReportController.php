<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Traits\ReportTrait;
use App\Traits\ReportTraitArmobile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // use ReportTrait, ReportTraitArmobile;
    use ReportTrait, ReportTraitArmobile;
    // {
    //     ReportTraitArmobile::ushacum insteadof ReportTrait;
    //     ReportTraitArmobile::calculate insteadof ReportTrait;
    // }

    public function calculateReport($mounth){

        $mounth = $mounth ?? \Carbon\Carbon::now()->format('Y-m');
        // dd($mounth);

        $groupedEntries = $this->report($mounth);
        // dd($groupedEntries);

        return $groupedEntries;
    }

    public function calculateReportArmobile($mounth){



        $mounth = $mounth ?? \Carbon\Carbon::now()->format('Y-m');
        // dd($mounth);

        $groupedEntries = $this->report_armobile($mounth);
        // dd($groupedEntries);

        return $groupedEntries;
    }

    public function index(Request $request){

        $i = 0;

        $mounth = $request->mounth??\Carbon\Carbon::now()->format('Y-m');


        $groupedEntries = $this->calculateReport($mounth);
        // dd($groupedEntries);


        return view('report.index',compact('groupedEntries','mounth','i'));

    }
    public function index_armobile(Request $request){
        // dd(777);
        try {

                $i = 0;
                // dd($request->all());
                $mounth = $request->mounth??\Carbon\Carbon::now()->format('Y-m');
                // dd($mounth);
                $groupedEntries = $this->calculateReportArmobile($mounth);
                // dd($groupedEntries);

                return view('report.index_armobile',compact('groupedEntries','mounth','i'));
            } catch (Exception $e) {

                return view('report.index_armobile')->with('error', $e->getMessage());

            }
    }


    public function export(Request $request)
    {


         $monthParam = $request->query('mounth');

         // If the 'month' parameter is set, use it. Otherwise, use the current month.
         if ($monthParam) {
             $selectedMonth = Carbon::createFromFormat('Y-m', $monthParam);
         } else {
             $selectedMonth = Carbon::now(); // Current month
         }

         // Format the selected month if needed
         $mounth = $selectedMonth->format('Y-m');
        //  dd( $formattedMonth);
        // dd( $mounth);

        return Excel::download(new ReportExport($mounth), 'Հաշվետվություն.xlsx');
    }

}
