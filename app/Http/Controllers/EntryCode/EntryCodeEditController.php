<?php

namespace App\Http\Controllers\EntryCode;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\EntryCode;
use Illuminate\Http\Request;

class EntryCodeEditController extends Controller
{
    public function edit($id){

        $data = EntryCode::where('id',$id)->with('active_person')->first();
        $clients = Client::all();

       return view('entry-code.edit', compact('data','clients'));
   }
}
