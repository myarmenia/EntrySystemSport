<?php

namespace App\Services;

use App\Models\Client;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class ReportService{
    public function getClientworkerattendanc($data){

        $client=Client::where('user_id',Auth::id())->first();
        dd($client);
        


    }


}


