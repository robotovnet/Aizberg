<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateDeliverySettingsRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateDeliverySettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'days_transfer'                    => 'string',
        'transfer_time_tomorrow'           => 'string',
        'transfer_time_after_tomorrow'     => 'string',
        'transport_company_working_days.*' => 'string',
        'transport_company_weekends.*'     => 'string',
        'delivery_type'                    => 'string',
        'transport_company_id'             => 'string',
        'delivery_point_id'                => 'string',
        'copy_settings'                    => 'string',
    ])]
    public function rules(): array
    {
        return [
            'days_transfer'                    => 'required|numeric|min:0',
            'transfer_time_tomorrow'           => 'required|date_format:H:i',
            'transfer_time_after_tomorrow'     => 'nullable|date_format:H:i',
            'transport_company_working_days.*' => 'required|in:Mn,Tu,Wd,Th,Fr,Sa,Sn',
            'transport_company_weekends.*'     => 'required|in:Mn,Tu,Wd,Th,Fr,Sa,Sn',
            'delivery_type'                    => 'required|in:postamat,courier_city,pickup_point',
            'transport_company_id'             => 'required|numeric|min:1|exists:transport_companies,id',
            'delivery_point_id'                => 'nullable|numeric|min:1',
            'copy_settings'                    => 'nullable|array',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'transportcompany::delivery_settings.validation_messages.';

        $messages['days_transfer.required']                    = Lang::get($prefix . 'days_transfer_required');
        $messages['transfer_time_tomorrow.required']           = Lang::get($prefix . 'transfer_time_tomorrow_required');
        $messages['transfer_time_after_tomorrow.required']     = Lang::get($prefix . 'transfer_time_after_tomorrow_required');
        $messages['transport_company_working_days.required']   = Lang::get($prefix . 'transport_company_working_days_required');
        $messages['transport_company_weekends.required']       = Lang::get($prefix . 'transport_company_weekends_required');
        $messages['delivery_type.required']                    = Lang::get($prefix . 'delivery_type_required');
        $messages['transport_company_id.required']             = Lang::get($prefix . 'transport_company_id_required');
        $messages['days_transfer.numeric']                     = Lang::get($prefix . 'days_transfer_numeric');
        $messages['days_transfer.min']                         = Lang::get($prefix . 'days_transfer_min');
        $messages['transfer_time_tomorrow.date_format']        = Lang::get($prefix . 'transfer_time_tomorrow_date_format');
        $messages['transfer_time_after_tomorrow.date_format']  = Lang::get($prefix . 'transfer_time_after_tomorrow_date_format');
        $messages['delivery_type.in']                          = Lang::get($prefix . 'delivery_type_in');
        $messages['delivery_point_id.numeric']                 = Lang::get($prefix . 'delivery_point_id_numeric');
        $messages['delivery_point_id.min']                     = Lang::get($prefix . 'delivery_point_id_min');
        $messages['transport_company_id.exists']               = Lang::get($prefix . 'transport_company_id_exists');
        $messages['transport_company_id.numeric']              = Lang::get($prefix . 'transport_company_id_numeric');
        $messages['transport_company_id.min']                  = Lang::get($prefix . 'transport_company_id_min');

        if ($this->has('transport_company_working_days')) {
            foreach ($this->get('transport_company_working_days') as $key => $val) {
                $messages['transport_company_working_days.' . $key . '.in'] =
                    Lang::get($prefix . 'days_names');
            }
        } else {
            $messages['in.transport_company_working_days.*'] =
                Lang::get($prefix . 'days_names');
        }

        if ($this->has('transport_company_weekends')) {
            foreach ($this->get('transport_company_weekends') as $key => $val) {
                $messages['transport_company_weekends.' . $key . '.in'] =
                    Lang::get($prefix . 'days_names');
            }
        } else {
            $messages['in.transport_company_weekends.*'] =
                Lang::get($prefix . 'days_names');
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
        $prefix = 'transportcompany::delivery_settings.validation_attributes.';

        $attributes['days_transfer']                   = Lang::get($prefix . 'days_transfer');
        $attributes['transfer_time_tomorrow']          = Lang::get($prefix . 'transfer_time_tomorrow');
        $attributes['transfer_time_after_tomorrow']    = Lang::get($prefix . 'transfer_time_after_tomorrow');
        $attributes['transport_company_working_days']  = Lang::get($prefix . 'transport_company_working_days');
        $attributes['transport_company_weekends']      = Lang::get($prefix . 'transport_company_weekends');
        $attributes['delivery_type']                   = Lang::get($prefix . 'delivery_type');
        $attributes['delivery_point_id']               = Lang::get($prefix . 'delivery_point_id');
        $attributes['transport_company_id']            = Lang::get($prefix . 'transport_company_id');

        return $attributes;
    }
}
