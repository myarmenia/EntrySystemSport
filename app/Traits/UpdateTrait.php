<?php

namespace App\Traits;

use App\Models\EntryCode;
use App\Models\EventConfig;
use App\Models\Image;
use App\Models\Person;
use App\Models\PersonPermission;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Services\FileUploadService;
use App\Services\Log\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UpdateTrait
{
  abstract function model();

  public function itemUpdate(Request $request, $id)
  {


    $data = $request->except([ '_method' ]);

    $className = $this->model();

    if (class_exists($className)) {

      $model = new $className;

      $item = $model::where('id', $id)->first();

    //   dd( $data);


      $data['status'] = ($item->status == 0 && $request->has('status')) ? 1 : $data['status'] ?? 0;
// dd($data);
        if(auth()->user()->hasRole('client_admin')){

            if($item->activation == 0  && $request->has('activation')){
                $data['activation'] = 1;
            }
            if($item->activation == 1  && $request->has('activation')){
                $data['activation'] = 1;
            }
            if ($item->activation == 1 && !request()->has('activation')) {
            $data['activation'] = 0;
            }
        }
       
        $client_all_tokens = EntryCode::where([
                                                   ['client_id','=',$item->client->id],
                                                   ['id','!=',$item->id]
                                                ])->get();

        foreach($client_all_tokens as $it){

            if($it->token == $request->token){

                session()->flash('repeating_token', 'Թոքենը կրկնվում է');
                    return redirect()->back();

            }
        }


      $item->update($data);



    }
  }
//   =======hin koderic e

     public function people($request, $clientId, $entryCodeid){

        $personPermmission=PersonPermission::where('entry_code_id',$entryCodeid)->first();

        if($personPermmission==null){


            $people = new Person;
            $people->client_id = $clientId;
            $people->name = $request->name;
            $people->surname = $request->surname;
            $people->phone = $request->phone;
            $people->email = $request->email;
            $people->save();

            $personPermission = PersonPermission::create([
                'people_id' => $people->id,
                'entry_code_id' => $entryCodeid

            ]);

        }else{

            $peopleData['name'] = $request->name;
            $peopleData['surname'] = $request->surname;
            $peopleData['phone'] = $request->phone;
            $peopleData['email'] = $request->email;

            $person = Person::where('id',$personPermmission->people->id)->first();
            $person->update($peopleData);


        }

        return true;

  }

}
