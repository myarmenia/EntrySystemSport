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
                 // ===== Break Validation =====
                $breakStart = $day['break_start_time'] ?? null;
                $breakEnd   = $day['break_end_time'] ?? null;

                if ($breakStart || $breakEnd) {
                    // Ստուգում՝ որ երկուսը լրացված են
                    if (!$breakStart || !$breakEnd) {
                        $fail("Օր `$day[week_day]`: Եթե նշվում է ընդմիջման սկիզբը կամ ավարտը, մյուսը նույնպես պետք է լրացված լինի։");
                    } else {
                        // Ստուգում՝ break_end > break_start
                        if ($breakEnd <= $breakStart) {
                            $fail("Օր `$day[week_day]`: Ընդմիջման ավարտի ժամանակը պետք է մեծ լինի սկիզբի ժամանակից։");
                        }
                    }
                }

                // ===== Smoke Validation =====
                // Ստուգում միայն եթե smoke_break array գոյություն ունի և ոչ դատարկ
                if (!empty($day['smoke_break']) && is_array($day['smoke_break'])) {
                    foreach ($day['smoke_break'] as $smokeIndex => $smoke) {
                        $smokeStart = $smoke['smoke_start_time'] ?? null;
                        $smokeEnd   = $smoke['smoke_end_time'] ?? null;

                        // Եթե կա smoke block, պետք է ստուգել՝ լի է start + end
                        if (!$smokeStart && !$smokeEnd) {
                            $fail("Օր `$day[week_day]`, ծխելու ժամ #".($smokeIndex+1).": Բլոկը ստեղծվել է, բայց start և end չեն լրացված։");
                        } elseif ($smokeStart && $smokeEnd && $smokeEnd <= $smokeStart) {
                            $fail("Օր `$day[week_day]`, ծխելու ժամ #".($smokeIndex+1).": Ավարտի ժամանակը պետք է մեծ լինի սկիզբի ժամանակից։");
                        } elseif (($smokeStart && !$smokeEnd) || (!$smokeStart && $smokeEnd)) {
                            $fail("Օր `$day[week_day]`, ծխելու ժամ #".($smokeIndex+1).": Եթե նշվում է սկիզբը կամ ավարտը, մյուսը նույնպես պետք է լրացված լինի։");
                        }
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
