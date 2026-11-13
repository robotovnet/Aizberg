<?php

declare(strict_types=1);

namespace Modules\Lottery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class LotteryDraws
 * @package Modules\Lottery\Entities
 * @mixin Eloquent
 */
class LotteryDraws extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'lottery_draws';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'day',
        'week',
        'year',
        'ticket_id',
        'prize_id',
        'app_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'day',
        'week',
        'year',
        'ticket_id',
        'prize_id',
        'app_id',
    ];


    // Relationships
    public function prize(): HasOne
    {
        return $this->hasOne(LotteryPrizes::class, 'id', 'prize_id')->withTrashed();
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(LotteryTickets::class, 'id', 'ticket_id');
    }

    public function app(): HasOne
    {
        return $this->hasOne(LotteryApps::class, 'id', 'app_id');
    }
}
