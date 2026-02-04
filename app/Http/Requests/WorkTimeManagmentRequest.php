<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkTimeManagmentRequest extends FormRequest
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


        return [
            'name' => ['required', 'string', 'max:255'],

            'week_days' => ['required', 'array', function($attribute, $value, $fail) {
                // Minimum մեկ օր պետք է ունենա day_start_time և day_end_time
                $validDayExists = false;

                foreach ($value as $day) {
                    if (!empty($day['day_start_time']) && !empty($day['day_end_time'])) {
                        $validDayExists = true;
                        break;
                    }
                }

                if (!$validDayExists) {
                    $fail('Խնդրում ենք լրացնել առնվազն մեկ օրվա ժամի սկիզբը և ավարտը։');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Խնդրում ենք լրացնել անունը։',
        ];
    }

}
