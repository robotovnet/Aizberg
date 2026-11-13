<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Str;
use Modules\BonusProgram\Entities\BonusLevel;

/**
 * Class CreateOrUpdateBonusLevelRequest
 * @package Modules\BonusProgram\Http\Requests
 */
class CreateOrUpdateBonusLevelRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'               => 'string',
        'names.*.lang'        => 'string',
        'names.*.value'       => 'string',
        'client_type_id'      => 'string',
        'level_num'           => 'string',
        'amount_up'           => 'string',
        'amount_instantly_up' => 'string',
        'amount_saving'       => 'string',
        'is_entry'            => 'string',
        'calculation_period'  => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'               => 'required|array|min:1',
            'names.*.lang'        => 'required|string|size:2|exists:languages,code',
            'names.*.value'       => 'required|string|between:1,100',
            'client_type_id'      => 'required|integer|exists:client_types,id',
            'level_num'           => 'required|integer|min:1|max:' . $this->maxLevel() . '|unique:bonus_levels,level_num,NULL,id,deleted_at,NULL',
            'amount_up'           => 'required|numeric|min:0|max:900000000',
            'amount_instantly_up' => 'required|numeric|min:0|max:900000000',
            'amount_saving'       => 'required|numeric|min:0|max:900000000',
            'is_entry'            => 'nullable|in:Y,N',
            'calculation_period'  => 'required|integer|min:1',
        ];

        // For update
        $levelId = request()->segment(2);

        if ($levelId !== null) {
            $rules['level_num'] = Str::replaceFirst('NULL', $levelId, $rules['level_num']);
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
        $prefix = 'bonusprogram::create_update_bonus_level.validation_messages.';

        $messages['client_type_id.required'] = Lang::get($prefix . 'client_type_id_required');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'bonusprogram::create_update_bonus_level.validation_attributes.';

        $attributes['names.0.value'] = Lang::get($prefix . 'name');
        $attributes['client_type_id'] = Lang::get($prefix . 'client_type_id');
        $attributes['level_num'] = Lang::get($prefix . 'level_num');
        $attributes['amount_up'] = Lang::get($prefix . 'amount_up');
        $attributes['amount_instantly_up'] = Lang::get($prefix . 'amount_instantly_up');
        $attributes['amount_saving'] = Lang::get($prefix . 'amount_saving');
        $attributes['is_entry'] = Lang::get($prefix . 'is_entry');
        $attributes['calculation_period'] = Lang::get($prefix . 'calculation_period');

        return $attributes;
    }

    private function maxLevel()
    {
        return BonusLevel::max('level_num') + 1;
    }
}
