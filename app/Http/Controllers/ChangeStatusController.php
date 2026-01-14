<?php

namespace App\Http\Controllers;

use App\Services\ChangeStatusService;
use Illuminate\Http\Request;

class ChangeStatusController extends Controller
{
    public function change_status(Request $request)
    {
        // dd($request->all());
        $update = ChangeStatusService::change_status($request);

        return response()->json(['result' => $update]);
    }
}
