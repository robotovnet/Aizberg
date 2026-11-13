<?php

declare(strict_types=1);

namespace Modules\Lottery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class LotteryTickets
 * @package Modules\Lottery\Entities
 * @mixin Eloquent
 */
class LotteryTickets extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'lottery_tickets';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'code',
        'generation_id',
        'status',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'code',
        'generation_id',
        'status',
    ];
}
