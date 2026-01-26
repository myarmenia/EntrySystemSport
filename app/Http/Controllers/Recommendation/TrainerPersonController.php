<?php

namespace App\Http\Controllers\Recommendation;

use App\Http\Controllers\Controller;
use App\Models\PersonSessionBooking;
use Illuminate\Http\Request;

class TrainerPersonController extends Controller
{
    public function index(){
        $data = PersonSessionBooking::where('trainer_id',auth()->id())->get();
        return response()->json(['message'=>$data]);
    }
    public function store(Request $request){
        dd($request->all());

    }
}
