<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleDetailsRequest extends FormRequest
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
    //public function rules(): array
    //
    //{
    //
    //
    //    return [
    //        'week_days.*.week_day' => ['required', 'string'],
    //
    //        'week_days.0.day_start_time'=>['required'],
    //        'week_days.1.day_start_time'=>['required'],
    //        'week_days.2.day_start_time'=>['required'],
    //        'week_days.3.day_start_time'=>['required'],
    //        'week_days.4.day_start_time'=>['required'],
    //        'week_days.0.day_end_time'=>['required'],
    //        'week_days.1.day_end_time'=>['required'],
    //        'week_days.2.day_end_time'=>['required'],
    //        'week_days.3.day_end_time'=>['required'],
    //        'week_days.4.day_end_time'=>['required'],
    //
    //
    //
    //    ];
    //}
    public function rules(): array
    {
        return [
            'week_days' => ['required', 'array'],
            'week_days.*.week_day' => ['required', 'string'],

            // checkbox-ը (եթե նշված է՝ enabled=1)
            'week_days.*.enabled' => ['nullable', 'in:0,1'],

            // Միայն enabled օրերի համար պարտադիր դարձրու ժամերը
            'week_days.*.day_start_time' => ['required_if:week_days.*.enabled,1'],
            'week_days.*.day_end_time'   => ['required_if:week_days.*.enabled,1'],

            // ընդմիջումները optional, բայց եթե մեկը կա՝ մյուսն էլ լինի
            'week_days.*.break_start_time' => ['nullable', 'required_with:week_days.*.break_end_time'],
            'week_days.*.break_end_time'   => ['nullable', 'required_with:week_days.*.break_start_time'],
        ];
    }

    // private function isWeekday($attribute): bool
    // {
    //     preg_match('/week_days\.(\d+)\./', $attribute, $matches);
    //     $index = isset($matches[1]) ? (int) $matches[1] : null;

    //     return $index !== null && $index < 5; // Only require for indexes 0-4 (Monday-Friday)
    // }
}
