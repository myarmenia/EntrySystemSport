<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // եթե ունես policy → կարող ես այստեղ փոխել
        return auth()->check();
    }

    public function rules(): array
    {
        $user = auth()->user();

        // գտնում ենք տվյալ user-ի client_id-ն
        $clientId = Client::where('user_id', $user->id)->value('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                Rule::in(['percent', 'fixed']),
            ],

            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percent' && $value > 100) {
                        $fail('Տոկոսային զեղչը չի կարող մեծ լինել 100-ից։');
                    }
                },
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'status' => [
                'required',
                Rule::in(['0', '1']),
            ],

            'package_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'package_ids.*' => [
                'integer',
                Rule::exists('packages', 'id')->where(function ($q) use ($clientId) {
                    if ($clientId) {
                        $q->where('client_id', $clientId);
                    }
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Անունը պարտադիր է։',

            'type.required' => 'Զեղչի տեսակը պարտադիր է։',
            'type.in' => 'Զեղչի տեսակը սխալ է։',

            'value.required' => 'Արժեքը պարտադիր է։',
            'value.numeric' => 'Արժեքը պետք է լինի թիվ։',
            'value.min' => 'Արժեքը չի կարող լինել բացասական։',

            'ends_at.after_or_equal' => 'Վերջի օրը պետք է մեծ կամ հավասար լինի սկզբի օրվան։',

            'status.required' => 'Կարգավիճակը պարտադիր է։',

            'package_ids.required' => 'Պետք է ընտրել առնվազն մեկ փաթեթ։',
            'package_ids.array' => 'Փաթեթների ձևաչափը սխալ է։',
            'package_ids.*.exists' => 'Ընտրված փաթեթներից մեկը տվյալ client-ին չի պատկանում։',
        ];
    }

    /**
     * Թույլատրելի տվյալներ update-ի համար
     */
    public function validatedData(): array
    {
        return $this->only([
            'name',
            'type',
            'value',
            'starts_at',
            'ends_at',
            'status',
        ]);
    }

    /**
     * Փաթեթների ID-ները առանձին helper-ով
     */
    public function packageIds(): array
    {
        return $this->input('package_ids', []);
    }
}
