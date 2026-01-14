<?php

namespace App\Http\Controllers\EntryCode;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\EntryCode;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntryCodeListController extends Controller
{
    // public function __construct(){
    //      $this->middleware(['permission:entry-code-list',  'role:!trainer']);
    // }

    public function index(Request $request)
    {

        $user = auth()->user();

        $query = EntryCode::latest();

        if ($user->hasRole('client_admin') || $user->hasRole('client_admin_rfID') || $user->hasRole('client_sport')) {

            $client = Client::where('user_id', $user->id)->first();

            if ($client) {
                $query->where('client_id', $client->id);
            }
        }
        if ($user->hasRole('manager')) {

            $client_id = Staff::where('user_id', Auth::id())->value('client_admin_id');
            $query->where('client_id', $client_id);
        }

        $data = $query->paginate(10)->withQueryString();


        return view('entry-code.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }
}
