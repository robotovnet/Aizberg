<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class StaffUpdateRequest
 * @package Modules\Staff\Http\Requests
 */
class StaffUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'first_name' => 'string',
        'last_name'  => 'string',
        'email'      => 'string',
        'phone'      => 'string',
        'groups'     => 'string',
        'groups.*'   => 'string',
        'whatsapp'   => 'string',
        'telegram'   => 'string',
        'max'        => 'string',
    ])]
    public function rules(): array
    {
        $staffId = request()->segment(3);

        return [
            'first_name' => 'required|string|between:2,60',
            'last_name'  => 'required|string|between:2,60',
            'email'      => 'required|email|max:100|unique:staff,email,' . $staffId,
            'phone'      => 'required|digits_between:11,20|unique:staff,phone,' . $staffId,
            'groups'     => 'required',
            'groups.*'   => 'required|numeric|exists:staff_groups,id,deleted_at,NULL',
            'whatsapp'   => 'nullable|string|max:50',
            'telegram'   => 'nullable|string|max:50',
            'max'        => 'nullable|string|max:50',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::staff_update.validation_messages.';

        $messages['first_name.required']  = Lang::get($prefix . 'first_name_required');
        $messages['first_name.between']   = Lang::get($prefix . 'first_name_between');
        $messages['last_name.required']   = Lang::get($prefix . 'last_name_required');
        $messages['last_name.between']    = Lang::get($prefix . 'last_name_between');
        $messages['email.required']       = Lang::get($prefix . 'email_required');
        $messages['email.email']          = Lang::get($prefix . 'email_email');
        $messages['email.max']            = Lang::get($prefix . 'email_max');
        $messages['email.unique']         = Lang::get($prefix . 'email_unique');
        $messages['phone.required']       = Lang::get($prefix . 'phone_required');
        $messages['phone.digits_between'] = Lang::get($prefix . 'phone_digits_between');
        $messages['phone.unique']         = Lang::get($prefix . 'phone_unique');
        $messages['groups.required']      = Lang::get($prefix . 'groups_required');
        $messages['whatsapp.max']         = Lang::get($prefix . 'whatsapp_max');
        $messages['telegram.max']         = Lang::get($prefix . 'telegram_max');
        $messages['max.max']              = Lang::get($prefix . 'max_max');

        if ($this->has('groups')) {
            foreach ($this->get('groups') as $key => $val) {
                $messages['groups.' . $key . '.required'] = Lang::get($prefix . 'groups_item_required');
                $messages['groups.' . $key . '.numeric']  = Lang::get($prefix . 'groups_item_numeric');
                $messages['groups.' . $key . '.exists']   = Lang::get($prefix . 'groups_item_exists');
            }
        } else {
            $messages['required.groups.*'] = Lang::get($prefix . 'groups_item_required');
            $messages['numeric.groups.*']  = Lang::get($prefix . 'groups_item_numeric');
            $messages['exists.groups.*']   = Lang::get($prefix . 'groups_item_exists');
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::staff_update.validation_attributes.';

        $attributes['first_name'] = Lang::get($prefix . 'first_name');
        $attributes['last_name']  = Lang::get($prefix . 'last_name');
        $attributes['email']      = Lang::get($prefix . 'email');
        $attributes['phone']      = Lang::get($prefix . 'phone');
        $attributes['groups']     = Lang::get($prefix . 'groups');
        $attributes['whatsapp']   = Lang::get($prefix . 'whatsapp');
        $attributes['telegram']   = Lang::get($prefix . 'telegram');
        $attributes['max']        = Lang::get($prefix . 'max');

        if ($this->has('groups')) {
            foreach ($this->get('groups') as $key => $val) {
                $attributes['groups.' . $key]  = Lang::get($prefix . 'groups_item');
            }
        }

        return $attributes;
    }
}
