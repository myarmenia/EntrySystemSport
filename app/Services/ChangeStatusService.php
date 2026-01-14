<?php
namespace App\Services;

use App\Models\PersonPermission;
use Illuminate\Support\Facades\DB;
class ChangeStatusService
{
    public static function change_status($request){
        // dd($request->all());
        $status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
// dd($status);
        $data = ['status' => $status];

        $update = DB::table($request->tb_name)
        ->where('id', $request->id)
        ->update([$request->field_name => $status]);

        if($request->field_name=="activation" && !$status){


            $change_person_permission = self::updatePersonPermission($request->id);
        }

         return $update ? $update : false;
    }
    public static function updatePersonPermission($id){

            $person_permission = PersonPermission::where(['entry_code_id'=>$id,'status'=>1])->first();
            if($person_permission){
                $person_permission->status = 0;
                $person_permission->save();
            }

    }
}
