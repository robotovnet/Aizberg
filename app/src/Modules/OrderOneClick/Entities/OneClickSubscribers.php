<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

/**
 * Class OneClickSubscribers
 * @package Modules\OrderOneClick\Entities
 * @mixin Eloquent
 */
class OneClickSubscribers extends Model
{
    /** @var string Table name. */
    protected $table = 'one_click_subscribers';

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'email',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'email',
    ];
}
