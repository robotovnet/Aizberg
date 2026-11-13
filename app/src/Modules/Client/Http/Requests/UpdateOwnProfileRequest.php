<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class UpdateOwnProfileRequest extends FormRequest
{
    private const PROFILE_FIELDS = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'country_code',
        'city_id',
        'timezone',
    ];

    /**
     * Normalize values before validation and persistence.
     */
    protected function prepareForValidation(): void
    {
        if ($this->exists('phone') && is_string($this->input('phone'))) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]+/', '', $this->input('phone')),
            ]);
        }

        if ($this->exists('country_code') && is_string($this->input('country_code'))) {
            $this->merge([
                'country_code' => mb_strtolower($this->input('country_code')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $clientId = Auth::guard('client')->id();

        return [
            'first_name' => 'sometimes|required|string|between:2,60',
            'last_name' => 'sometimes|required|string|between:2,60',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:100',
                Rule::unique('clients', 'email')->ignore($clientId),
            ],
            'phone' => [
                'sometimes',
                'required',
                'digits_between:11,20',
                Rule::unique('clients', 'phone')->ignore($clientId),
            ],
            'country_code' => 'sometimes|required|string|size:2|exists:countries,code',
            'city_id' => 'sometimes|required|integer|min:1|exists:cities,id',
            'timezone' => 'sometimes|required|timezone|exists:timezones,name',
        ];
    }

    /**
     * Require at least one supported profile field.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count(array_intersect(self::PROFILE_FIELDS, array_keys($this->all()))) === 0) {
                $validator->errors()->add(
                    'profile',
                    Lang::get('client::profile.no_profile_fields')
                );
            }
        });
    }
}
