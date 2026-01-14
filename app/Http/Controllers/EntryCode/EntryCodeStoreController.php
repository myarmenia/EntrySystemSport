<?php

namespace App\Http\Controllers\EntryCode;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryCodeRequest;
use App\Models\EntryCode;
use App\Traits\StoreTrait;
use Illuminate\Http\Request;

class EntryCodeStoreController extends Controller
{
    use StoreTrait;

    public function model()
    {
       return EntryCode::class;
    }

     public function store(EntryCodeRequest $request)

     {

       
          $data = $this->itemStore($request);

          if($data){
             return redirect()->route('entry-codes-create');
           }

     }
}
