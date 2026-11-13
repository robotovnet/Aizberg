<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Str;

/**
 * Class CreateGroupRequest
 * @package Modules\Staff\Http\Requests
 */
class CreateOrUpdateGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'name'                 => 'string',
        'permissions'          => 'string',
        'permissions.*.policy' => 'string',
        'start_policy'         => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'name'                  => 'required|string|max:60|unique:staff_groups,name,NULL,id,deleted_at,NULL',
            'permissions'           => 'required',
            'permissions.*.policy'  => 'required|string|max:60|distinct',
            'start_policy'          => 'required|string|exists:staff_permissions,policy,deleted_at,NULL',
        ];

        // For update
        $groupId = request()->segment(3);
        if ($groupId !== null) {
            $rules['name'] = Str::replaceFirst('NULL', $groupId, $rules['name']);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::create_update_group.validation_messages.';

        $messages['name.required'] = Lang::get($prefix . 'name_required');
        $messages['name.max']      = Lang::get($prefix . 'name_max');
        $messages['name.unique']   = Lang::get($prefix . 'name_unique');
        $messages['permissions.required']   = Lang::get($prefix . 'permissions_required');

        if ($this->has('permissions')) {
            foreach ($this->get('permissions') as $key => $val) {
                $messages['permissions.' . $key . '.policy.required'] =
                    Lang::get($prefix . 'permissions_policy_required');
                $messages['permissions.' . $key . '.policy.max']      =
                    Lang::get($prefix . 'permissions_policy_max');
                $messages['permissions.' . $key . '.policy.distinct'] =
                    Lang::get($prefix . 'permissions_policy_distinct');
            }
        } else {
            $messages['required.permissions.*.policy'] =
                Lang::get($prefix . 'permissions_policy_required');
            $messages['max.permissions.*.policy']      =
                Lang::get($prefix . 'permissions_policy_max');
            $messages['distinct.permissions.*.policy'] =
                Lang::get($prefix . 'permissions_policy_distinct');
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
        $prefix = 'staff::create_update_group.validation_attributes.';

        $attributes['name']         = Lang::get($prefix . 'name');
        $attributes['permissions']  = Lang::get($prefix . 'permissions');
        $attributes['start_policy'] = Lang::get($prefix . 'start_policy');

        if ($this->has('permissions')) {
            foreach ($this->get('permissions') as $key => $val) {
                $attributes['permissions.' . $key . '.policy']  = Lang::get($prefix . 'permissions_policy');
            }
        }

        return $attributes;
    }
}

