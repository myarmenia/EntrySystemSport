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
    public function rules(): array

    {


        return [
            'week_days.*.week_day' => ['required', 'string'],

            'week_days.0.day_start_time'=>['required'],
            'week_days.1.day_start_time'=>['required'],
            'week_days.2.day_start_time'=>['required'],
            'week_days.3.day_start_time'=>['required'],
            'week_days.4.day_start_time'=>['required'],
            'week_days.0.day_end_time'=>['required'],
            'week_days.1.day_end_time'=>['required'],
            'week_days.2.day_end_time'=>['required'],
            'week_days.3.day_end_time'=>['required'],
            'week_days.4.day_end_time'=>['required'],



        ];
    }
    // private function isWeekday($attribute): bool
    // {
    //     preg_match('/week_days\.(\d+)\./', $attribute, $matches);
    //     $index = isset($matches[1]) ? (int) $matches[1] : null;

    //     return $index !== null && $index < 5; // Only require for indexes 0-4 (Monday-Friday)
    // }
}
