<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ChangeStatusRequest
 * @package Modules\Order\Http\Requests
 */
class ChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'status' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'status' => 'required|in:new,confirm,delivered,ready,1c_err,return,cancel,duplicate,non_call,complete',
        ];
    }
}
