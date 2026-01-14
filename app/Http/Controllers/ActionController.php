<?php

namespace App\Http\Controllers;

use App\Events\ActionEvent;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    // public function index()
    // {
    //     return view('welcome');
    // }

    public function action()
    {
        // dd(request()->input('buttonId'), request()->input('emoji'));
        event(
            new ActionEvent(
                buttonId: request()->input('buttonId'),
                emoji: request()->input('emoji'),
            )
        );
    }
}
