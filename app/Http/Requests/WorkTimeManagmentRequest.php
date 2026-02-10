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


    $data= [
        'name' => ['required', 'string', 'max:255'],


        'week_days' => [

            'array',
            function ($attribute, $value, $fail) {

                $validDayExists = false;

                foreach ($value as $dayIndex => $day) {
                    // dd($day);

                    $dayName = $day['week_day'] ?? 'Օր';

                    // ===== Minimum մեկ օր =====
                    if (!empty($day['day_start_time']) && !empty($day['day_end_time'])) {
                        $validDayExists = true;
                    }

                    // ===== Break validation =====

                    // if (isset($day['break_start_time']) || isset($day['break_end_time'])) {
                    //     dd(11);
                    if (array_key_exists('break_start_time', $day) && array_key_exists('break_end_time', $day) ) {

                        if ($day['break_start_time'] === null  || $day['break_end_time'] === null) {


                            $fail(
                                "week_days.$dayIndex.break_time",
                                "Ընդմիջման սկիզբը և ավարտը պետք է երկուսն էլ լրացված լինեն։"
                            );
                        }
                        elseif ($day['break_end_time'] <= $day['break_start_time']) {

                            $fail(
                                "week_days.$dayIndex.break_time",
                                "Ընդմիջման ավարտը պետք է մեծ լինի սկիզբի ժամանակից։"
                            );
                        }
                    }

                    // ===== Smoke validation =====
                    if (!empty($day['smoke_break']) && is_array($day['smoke_break'])) {

                        foreach ($day['smoke_break'] as $smokeIndex => $smoke) {

                            $smokeStart = $smoke['smoke_start_time'] ?? null;
                            $smokeEnd   = $smoke['smoke_end_time'] ?? null;

                            $errorKey = "week_days.$dayIndex.smoke_break.$smokeIndex";

                            if (!$smokeStart && !$smokeEnd) {

                                $fail(
                                    $errorKey,
                                    // "Օր `$dayName`, ծխելու ժամ #".($smokeIndex).": Սկիզբ և ավարտը չեն լրացված։"
                                     "Ծխելու ժամի սկիզբ և ավարտը չեն լրացված։"
                                );

                            } elseif ($smokeStart && $smokeEnd && $smokeEnd <= $smokeStart) {

                                $fail(
                                    $errorKey,
                                    "Ծխելու ժամի ավարտը պետք է մեծ լինի սկիզբից։"
                                );

                            } elseif (($smokeStart && !$smokeEnd) || (!$smokeStart && $smokeEnd)) {

                                $fail(
                                    $errorKey,
                                    "Ծխելու ժամի  սկիզբն ու ավարտը պարտադիր են։"
                                );
                            }
                        }
                    }
                }

                if (!$validDayExists) {
                   
                    $fail(
                        'week_days.'.$dayIndex,
                        'Անհրաժեշտ է լրացնել առնվազն մեկ օրվա աշխատանքային ժամի սկիզբը և ավարտը։'
                    );
                }
            }
        ],

    ];
    return $data;

}


    public function messages(): array
    {
        return [
            'name.required' => 'Խնդրում ենք լրացնել անունը։',
        ];
    }

}
