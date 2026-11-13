<?php

declare(strict_types=1);

namespace Modules\Lottery\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Entities\Product;
use Eloquent;

/**
 * Class LotteryPrizes
 * @package Modules\Lottery\Entities
 * @mixin Eloquent
 */
class LotteryPrizes extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'lottery_prizes';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'status',
        'week',
        'year',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'product_id',
        'status',
        'week',
        'year',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
