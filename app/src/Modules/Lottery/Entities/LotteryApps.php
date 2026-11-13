<?php

declare(strict_types=1);

namespace Modules\Lottery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * Class LotteryApps
 * @package Modules\Lottery\Entities
 * @mixin Eloquent
 */
class LotteryApps extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'lottery_apps';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'ticket_id',
        'player_name',
        'player_phone',
        'player_email',
        'screenshot_url',
        'status',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'ticket_id',
        'player_name',
        'player_phone',
        'player_email',
        'screenshot_url',
        'status',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(LotteryTickets::class);
    }
}
