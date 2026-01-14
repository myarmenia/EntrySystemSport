<?php

namespace App\Http\Controllers;

use App\Services\CronJobService;
use Illuminate\Http\Request;

class CronJobController extends Controller
{
    public function __construct(protected CronJobService $cronJobService){

    }
     public function index(){


        $data = $this->cronJobService->get();

     }
}
