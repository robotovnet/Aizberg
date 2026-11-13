<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Str;

/**
 * Class CreateOrUpdateTransportCompanyRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateTransportCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'                         => 'string',
        'names.*.lang'                  => 'string',
        'names.*.value'                 => 'string',
        'additional_comments'           => 'string',
        'additional_comments.*.lang'    => 'string',
        'additional_comments.*.value'   => 'string',
        'code'                          => 'string',
        'accept_orders_start_time'      => 'string',
        'accept_orders_completion_time' => 'string',
        'minimum_order_value_courier'   => 'string',
        'minimum_order_value_pickup'    => 'string',
        'is_support_courier_delivery'   => 'string',
        'is_support_postamats'          => 'string',
        'is_support_pickup_points'      => 'string',
        'is_active'                     => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'                         => 'required|array|min:1',
            'names.*.lang'                  => 'required|string|size:2|exists:languages,code',
            'names.*.value'                 => 'required|string|between:2,60',
            'additional_comments'           => 'required',
            'additional_comments.*.lang'    => 'required|string|size:2|exists:languages,code',
            'additional_comments.*.value'   => 'sometimes|between:0,255',
            'code'                          => 'required|string|between:1,30|unique:transport_companies,code,NULL,id,deleted_at,NULL',
            'accept_orders_start_time'      => 'required|date_format:H:i',
            'accept_orders_completion_time' => 'required|date_format:H:i',
            'minimum_order_value_courier'   => 'required|numeric|min:0|max:999999999',
            'minimum_order_value_pickup'    => 'required|numeric|min:0|max:999999999',
            'is_support_courier_delivery'   => 'required|in:Y,N',
            'is_support_postamats'          => 'required|in:Y,N',
            'is_support_pickup_points'      => 'required|in:Y,N',
            'is_active'                     => 'required|in:Y,N',
        ];

        // For update
        $transportCompanyId = request()->segment(2);

        if ($transportCompanyId !== null) {
            $rules['code'] = Str::replaceFirst('NULL', $transportCompanyId, $rules['code']);
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
        $prefix = 'transportcompany::create_update.validation_messages.';

        $messages['names.required']                = Lang::get($prefix . 'names_required');
        $messages['additional_comments.required']  = Lang::get($prefix . 'additional_comments_required');
        $messages['code.required']                 = Lang::get($prefix . 'code_required');
        $messages['code.unique']                   = Lang::get($prefix . 'code_unique');
        $messages['code.between']                   = Lang::get($prefix . 'code_between');
        $messages['accept_orders_start_time.required']    = Lang::get($prefix . 'accept_orders_start_time_required');
        $messages['accept_orders_start_time.date_format'] = Lang::get($prefix . 'accept_orders_start_time_date_format');
        $messages['accept_orders_completion_time.required']    = Lang::get($prefix . 'accept_orders_completion_time_required');
        $messages['accept_orders_completion_time.date_format'] = Lang::get($prefix . 'accept_orders_completion_time_date_format');
        $messages['minimum_order_value_courier.required'] = Lang::get($prefix . 'minimum_order_value_required');
        $messages['minimum_order_value_courier.numeric']   = Lang::get($prefix . 'minimum_order_value_numeric');
        $messages['minimum_order_value_courier.min']       = Lang::get($prefix . 'minimum_order_value_min');
        $messages['minimum_order_value_pickup.required'] = Lang::get($prefix . 'minimum_order_value_required');
        $messages['minimum_order_value_pickup.numeric']   = Lang::get($prefix . 'minimum_order_value_numeric');
        $messages['minimum_order_value_pickup.min']       = Lang::get($prefix . 'minimum_order_value_min');
        $messages['is_support_courier_delivery.required'] = Lang::get($prefix . 'is_support_courier_delivery_required');
        $messages['is_support_courier_delivery.in']    = Lang::get($prefix . 'is_support_courier_delivery_in');
        $messages['is_support_postamats.required']     = Lang::get($prefix . 'is_support_postamats_required');
        $messages['is_support_postamats.in']           = Lang::get($prefix . 'is_support_postamats_in');
        $messages['is_support_pickup_points.required'] = Lang::get($prefix . 'is_support_pickup_points_required');
        $messages['is_support_pickup_points.in']       = Lang::get($prefix . 'is_support_pickup_points_in');
        $messages['is_active.required']                = Lang::get($prefix . 'is_active_required');
        $messages['is_active.in']                      = Lang::get($prefix . 'is_active_in');

        if (is_array($this->get('names')) && $this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $messages['names.' . $key . '.lang.required'] = Lang::get($prefix . 'names_lang_required');
                $messages['names.' . $key . '.lang.size']     = Lang::get($prefix . 'names_lang_size');
                $messages['names.' . $key . '.lang.exists']   = Lang::get($prefix . 'names_lang_exists');

                $messages['names.' . $key . '.value.required'] = Lang::get($prefix . 'names_value_required');
                $messages['names.' . $key . '.value.between']  = Lang::get($prefix . 'names_value_between');
            }
        } else {
            $messages['required.names.*.lang'] = Lang::get($prefix . 'names_lang_required');
            $messages['size.names.*.lang']     = Lang::get($prefix . 'names_lang_size');
            $messages['exists.names.*.lang']   = Lang::get($prefix . 'names_lang_exists');

            $messages['required.names.*.value'] = Lang::get($prefix . 'names_value_required');
            $messages['between.names.*.value']  = Lang::get($prefix . 'names_value_between');
        }

        if ($this->has('additional_comments')) {
            foreach ($this->get('additional_comments') as $key => $val) {
                $messages['additional_comments.' . $key . '.lang.required'] = Lang::get($prefix . 'names_lang_required');
                $messages['additional_comments.' . $key . '.lang.size']     = Lang::get($prefix . 'names_lang_size');
                $messages['additional_comments.' . $key . '.lang.exists']   = Lang::get($prefix . 'names_lang_exists');

                $messages['additional_comments.' . $key . '.value.between'] = Lang::get($prefix . 'additional_comments_value_between');
            }
        } else {
            $messages['required.additional_comments.*.lang'] = Lang::get($prefix . 'names_lang_required');
            $messages['size.additional_comments.*.lang']     = Lang::get($prefix . 'names_lang_size');
            $messages['exists.additional_comments.*.lang']   = Lang::get($prefix . 'names_lang_exists');

            $messages['between.additional_comments.*.value'] = Lang::get($prefix . 'additional_comments_value_between');
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
        $prefix = 'transportcompany::create_update.validation_attributes.';

        $attributes['names']                         = Lang::get($prefix . 'names');
        $attributes['additional_comments']           = Lang::get($prefix . 'additional_comments');
        $attributes['code']                          = Lang::get($prefix . 'code');
        $attributes['accept_orders_start_time']      = Lang::get($prefix . 'accept_orders_start_time');
        $attributes['accept_orders_completion_time'] = Lang::get($prefix . 'accept_orders_completion_time');
        $attributes['minimum_order_value_courier']   = Lang::get($prefix . 'minimum_order_value_courier');
        $attributes['minimum_order_value_pickup']    = Lang::get($prefix . 'minimum_order_value_pickup');
        $attributes['is_support_courier_delivery']   = Lang::get($prefix . 'is_support_courier_delivery');
        $attributes['is_support_postamats']          = Lang::get($prefix . 'is_support_postamats');
        $attributes['is_support_pickup_points']      = Lang::get($prefix . 'is_support_pickup_points');
        $attributes['is_active']                     = Lang::get($prefix . 'is_active');

        if (is_array($this->get('names')) && $this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $attributes['names.' . $key . '.lang']  = Lang::get($prefix . 'names_lang');
                $attributes['names.' . $key . '.value'] = Lang::get($prefix . 'names');
            }
        }

        if ($this->has('additional_comments')) {
            foreach ($this->get('additional_comments') as $key => $val) {
                $attributes['additional_comments.' . $key . '.lang']  = Lang::get($prefix . 'names_lang');
                $attributes['additional_comments.' . $key . '.value'] = Lang::get($prefix . 'additional_comments');
            }
        }

        $attributes['names.0.value']  = Lang::get($prefix . 'names');

        return $attributes;
    }
}

