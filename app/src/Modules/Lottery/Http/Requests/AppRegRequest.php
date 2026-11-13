<?php

namespace Modules\Lottery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class AppRegRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'player_name'  => 'required|string|min:3|max:60',
            'player_phone' => ['required', 'string', 'regex:/^(?:\+7\([0-9]{3}\)[0-9]{3}\-[0-9]{2}\-[0-9]{2}|\+375 \([0-9]{2}\) [0-9]{3}\-[0-9]{2}\-[0-9]{2}|\+375 [0-9]{2} [0-9]{3} [0-9]{2} [0-9]{2})$/'],
            'player_email' => 'required|email',
            'ticket_code'  => 'required|string|exists:lottery_tickets,code',
            'screenshot'   => 'required|image|mimes:jpeg,jpg,png,gif|dimensions:min_width=200,min_height=200|min:1|max:7168',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'lottery::app_reg.validation_messages.';

        $messages['player_name.required']  = Lang::get($prefix . 'player_name_required');
        $messages['player_name.min']       = Lang::get($prefix . 'player_name_min');
        $messages['player_name.max']       = Lang::get($prefix . 'player_name_max');
        $messages['player_phone.required'] = Lang::get($prefix . 'player_phone_required');
        $messages['player_phone.regex']    = Lang::get($prefix . 'player_phone_regex');
        $messages['player_email.required'] = Lang::get($prefix . 'player_email_required');
        $messages['player_email.email'] = Lang::get($prefix . 'player_email_email');
        $messages['ticket_code.required'] = Lang::get($prefix . 'ticket_code_required');
        $messages['ticket_code.exists'] = Lang::get($prefix . 'ticket_code_exists');
        $messages['screenshot.required'] = Lang::get($prefix . 'screenshot_required');
        $messages['screenshot.image'] = Lang::get($prefix . 'screenshot_image');
        $messages['screenshot.dimensions'] = Lang::get($prefix . 'screenshot_dimensions');
        $messages['screenshot.min'] = Lang::get($prefix . 'screenshot_min');
        $messages['screenshot.max'] = Lang::get($prefix . 'screenshot_max');
        $messages['screenshot.mimes'] = Lang::get($prefix . 'screenshot_mimes');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'lottery::app_reg.validation_attributes.';

        $attributes['player_name'] = Lang::get($prefix . 'player_name');
        $attributes['player_phone'] = Lang::get($prefix . 'player_phone');
        $attributes['player_email'] = Lang::get($prefix . 'player_email');
        $attributes['ticket_code'] = Lang::get($prefix . 'ticket_code');
        $attributes['screenshot'] = Lang::get($prefix . 'screenshot');
        
        return $attributes;
    }
}
