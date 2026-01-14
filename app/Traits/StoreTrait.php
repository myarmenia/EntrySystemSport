<?php
namespace App\Traits;

use App\Models\Client;
use App\Models\EntryCode;
use App\Models\PersonPermission;
use App\Models\Staff;
use App\Services\FileUploadService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

trait StoreTrait
{

    abstract function model();

    public function itemStore( $request)
    {
        $client = $request->client_id ?? null;
        if(auth()->user()->hasRole('client_admin')||auth()->user()->hasRole('client_sport')){

            $client = Client::where('user_id',Auth::id())->value('id');
        }
        if(auth()->user()->hasRole('manager')){
            // dd(777);

            $client = Staff::where('user_id',Auth::id())->value('client_admin_id');
             // dd($client);
        }


            $request['type']="faceID";
            $request['client_id'] = $client;

            if( $request->has('activation')){
                $request['activation'] = 1;
            }
            if( !$request->has('activation')){
                $request['activation'] = 0;
            }



        $data = $request->all();


        $className = $this->model();
        // dd($className);

        if (class_exists($className)) {

            $model = new $className;
            $entry_code = EntryCode::where(['token'=>$request->token,'client_id'=>$request->client_id])->first();
            if($entry_code){

                session()->flash('repeating_token', 'Թոքենը կրկնվում է');

                return redirect()->back();
            }


            $item = $model::create($data);
            session()->flash('success', 'Թոքենը ստեղծվել է');
        return true;

    }



    }


}
