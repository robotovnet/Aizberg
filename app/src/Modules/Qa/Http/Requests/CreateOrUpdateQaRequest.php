<?php

declare(strict_types=1);

namespace Modules\Qa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateQaRequest
 * @package Modules\Qa\Http\Requests
 */
class CreateOrUpdateQaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'product_id'    => 'string',
        'client_id'     => 'string',
        'staff_id'      => 'string',
        'reply_to_qid'  => 'string',
        'replied'       => 'string',
        'content'       => 'string',
        'reply_to_mail' => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'product_id'    => 'required|min:1|exists:products,id',
            'client_id'     => 'required|min:1|exists:clients,id',
            'staff_id'      => 'nullable|min:1|exists:staff,id',
            'reply_to_qid'  => 'nullable|min:1|exists:product_qa,id',
            'replied'       => 'required|in:Y,N',
            'content'       => 'required|string|min:1',
            'reply_to_mail' => 'required|in:Y,N',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'qa::create_update.validation_messages.';

        $messages['product_id.required'] = Lang::get($prefix . 'product_id_required');
        $messages['client_id.required']  = Lang::get($prefix . 'client_id_required');
        $messages['staff_id.required']   = Lang::get($prefix . 'staff_id_required');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'qa::create_update.validation_attributes.';

        $attributes = [];

        $attributes['product_id'] = Lang::get($prefix . 'product_id');
        $attributes['client_id'] = Lang::get($prefix . 'client_id');
        $attributes['staff_id'] = Lang::get($prefix . 'staff_id');
        $attributes['reply_to_qid'] = Lang::get($prefix . 'reply_to_qid');
        $attributes['replied'] = Lang::get($prefix . 'replied');
        $attributes['content'] = Lang::get($prefix . 'content');
        $attributes['reply_to_mail'] = Lang::get($prefix . 'reply_to_mail');


        return $attributes;
    }
}
