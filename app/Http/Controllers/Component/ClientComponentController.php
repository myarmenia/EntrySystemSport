<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class ClientComponentController extends Controller
{



    public function component(Request $request){

        return response()->json([
            'html' => view('components.client')->render()
        ]);

    }
}
