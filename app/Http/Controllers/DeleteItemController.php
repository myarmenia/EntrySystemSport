<?php

namespace App\Http\Controllers;

use App\Services\DeleteItemService;
use Illuminate\Http\Request;

class DeleteItemController extends Controller
{
    public function index($tb_name, $id)
    {

   
         try {

            $delete = DeleteItemService::delete($tb_name, $id);
            return response()->json(['result' => $delete]);

            } catch (\Exception $e) {

                return response()->json(['error' => $e->getMessage()], 404);
            }




    }
}
