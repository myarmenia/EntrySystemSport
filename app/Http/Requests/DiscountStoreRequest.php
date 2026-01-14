<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'type' => ['required','in:percent,fixed'],
            'value' => ['required','numeric','min:0.01'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'status' => ['required','boolean'],

            'package_ids' => ['required','array','min:1'],
            'package_ids.*' => ['integer','exists:packages,id'],
        ];
    }
}
