<?php



use App\Models\Person;



    if (! function_exists('getPeople')) {
      function getPeople($id){
      
            return Person::where('id',$id)->first();

        }
    }





