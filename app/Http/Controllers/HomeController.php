<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EntryCode;
use App\Models\Person;
use App\Models\PersonPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('trainer')) {

            return redirect()->route('visitors.list');
        } else {
            return redirect()->route('entry-codes-list');
        }
    }
}
