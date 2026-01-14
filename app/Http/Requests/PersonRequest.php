<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
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

        $rules= [

            'name' => 'required',
            'surname' => 'required',



        ];

        if($this->phone!=null){
            $rules['phone'] =  'regex:/^\+\d{1,3}\d{9,11}$/';
        }

        return  $rules;
    }
    public function messages()
{
    return [

        'phone.regex' => 'Հեռախոսահամարը պետք է սկսվի  "+"նշանով, որին հաջորդում է երկրի կոդը  (1-3 նիշ) և առավելագույնը  11 թվային նիշ',
    ];
}
}
