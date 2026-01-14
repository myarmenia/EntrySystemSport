<?php

namespace App\Http\Requests;

// use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EntryCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {


            $rules =[

                'token' => 'required',

            ];
            if(Auth::user()->hasRole('super_admin')){
                $rules['type']='required';
            }
            
            return  $rules;

    }
}
