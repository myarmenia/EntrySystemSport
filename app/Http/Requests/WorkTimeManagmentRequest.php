<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkTimeManagmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'week_days' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {

                    $hasAtLeastOneWorkDay = false;

                    foreach ($value as $dayIndex => $day) {

                        $dayStart = $day['day_start_time'] ?? null;
                        $dayEnd   = $day['day_end_time'] ?? null;

                        // ===== even one full day =====
                        if ($dayStart && $dayEnd) {
                            $hasAtLeastOneWorkDay = true;
                        }

                        // ===== check break or smoke =====
                        $hasBreak =
                            array_key_exists('break_start_time', $day) ||
                            array_key_exists('break_end_time', $day);

                        $hasSmoke =
                            !empty($day['smoke_break']) && is_array($day['smoke_break']);

                        // ===== if isset break or smoke → required day_start/day_end =====
                        if (($hasBreak || $hasSmoke) && (!$dayStart || !$dayEnd)) {

                            $fail(
                                "week_days.$dayIndex.day_time",
                                'Ընդմիջում կամ ծխելու ժամ ավելացնելու դեպքում տվյալ օրվա աշխատանքային ժամերը պարտադիր են։'
                            );
                        }

                        // ================= BREAK =================
                        if ($hasBreak) {
                            // dd('hasBreak');

                            $breakStart = $day['break_start_time'] ?? null;
                            $breakEnd   = $day['break_end_time'] ?? null;

                            if (!$breakStart && !$breakEnd) {
                                $fail(
                                    "week_days.$dayIndex.break_time",
                                    'Ընդմիջման սկիզբն ու ավարտը պարտադիր են։'
                                );
                            }
                             elseif (($breakStart && !$breakEnd) || (!$breakStart && $breakEnd)) {
                                // dd(111);

                                $fail(
                                    "week_days.$dayIndex.break_time",
                                    'Ընդմիջման սկիզբն ու ավարտը պարտադիր են։'
                                );

                            } elseif ($breakStart && $breakEnd && $breakEnd <= $breakStart) {

                                $fail(
                                    "week_days.$dayIndex.break_time",
                                    'Ընդմիջման ավարտը պետք է մեծ լինի սկիզբից։'
                                );
                            }
                        }

                        // ================= SMOKE =================
                        if ($hasSmoke) {

                            foreach ($day['smoke_break'] as $smokeIndex => $smoke) {

                                $smokeStart = $smoke['smoke_start_time'] ?? null;
                                $smokeEnd   = $smoke['smoke_end_time'] ?? null;

                                $errorKey = "week_days.$dayIndex.smoke_break.$smokeIndex";
                                if (!$smokeStart &&  !$smokeEnd) {

                                    $fail(
                                        $errorKey,
                                        'Ծխելու ժամի սկիզբն ու ավարտը պարտադիր են։'
                                    );

                                }
                                elseif (($smokeStart && !$smokeEnd) || (!$smokeStart && $smokeEnd)) {

                                    $fail(
                                        $errorKey,
                                        'Ծխելու ժամի սկիզբն ու ավարտը պարտադիր են։'
                                    );

                                } elseif ($smokeStart && $smokeEnd && $smokeEnd <= $smokeStart) {

                                    $fail(
                                        $errorKey,
                                        'Ծխելու ժամի ավարտը պետք է մեծ լինի սկիզբից։'
                                    );
                                }
                            }
                        }
                    }
                    // dd(!$hasAtLeastOneWorkDay);
                    if (!$hasAtLeastOneWorkDay) {

                        $fail(
                            'week_days',
                            'Անհրաժեշտ է լրացնել առնվազն մեկ օրվա աշխատանքային ժամի սկիզբը և ավարտը։'
                        );
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Անվանում դաշտը պարտադիր է։',
        ];
    }
}
