<?php

declare(strict_types=1);

namespace Modules\Forms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

/**
 * Class FeedbackRequest
 * @package Modules\Forms\Http\Requests
 */
class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
            'captcha' => 'required|captcha',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'forms::feedback.validation_messages.';

        return [
            'name.required' => Lang::get($prefix . 'name_required'),
            'name.string' => Lang::get($prefix . 'name_string'),
            'name.max' => Lang::get($prefix . 'name_max'),

            'email.required' => Lang::get($prefix . 'email_required'),
            'email.email' => Lang::get($prefix . 'email_invalid'),
            'email.max' => Lang::get($prefix . 'email_max'),

            'phone.string' => Lang::get($prefix . 'phone_string'),
            'phone.max' => Lang::get($prefix . 'phone_max'),

            'message.required' => Lang::get($prefix . 'message_required'),
            'message.string' => Lang::get($prefix . 'message_string'),
            'message.max' => Lang::get($prefix . 'message_max'),

            'captcha.required' => Lang::get($prefix . 'captcha_required'),
            'captcha.captcha' => Lang::get($prefix . 'captcha_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'forms::feedback.validation_attributes.';

        return [
            'name' => Lang::get($prefix . 'name'),
            'email' => Lang::get($prefix . 'email'),
            'phone' => Lang::get($prefix . 'phone'),
            'message' => Lang::get($prefix . 'message'),
            'captcha' => Lang::get($prefix . 'captcha'),
        ];
    }
}
