<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class AdYandexMarket
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class AdYandexMarket extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_ad_yandex_market';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'overview_text',
        'picture_url',
        'is_active',
        'product_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
        'name',
        'overview_text',
        'picture_url',
        'is_active',
    ];

    public function adYandexMarket(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
