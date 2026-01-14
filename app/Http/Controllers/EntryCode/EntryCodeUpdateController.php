<?php

namespace App\Http\Controllers\EntryCode;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryCodeRequest;
use App\Models\EntryCode;
use App\Traits\UpdateTrait;
use Illuminate\Http\Request;

class EntryCodeUpdateController extends Controller
{
    use UpdateTrait;

    public function model(){

        return EntryCode::class;
    }
    public function update(Request $request,$id)
    {
        // $1076
        // dd($request->all());


        $data = $this->itemUpdate($request,$id);
        return redirect()->back();
    }
}
