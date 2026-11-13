<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Eloquent;

/**
 * Class Product
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Product extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'ext_name',
        'vendor_code',
        'brand_id',
        'product_group_id',
        'is_hidden',
        'not_for_everyone',
        'overview_text',
        'stock_status',
        'uuid',
        'views_counter',
        'has_photos',
        'created_at',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'products.id',
        'name',
        'ext_name',
        'vendor_code',
        'brand_id',
        'product_group_id',
        'is_hidden',
        'not_for_everyone',
        'overview_text',
        'stock_status',
        'uuid',
        'views_counter',
        'has_photos',
        'products.created_at',
    ];

    /**
     * Related products.
     */
    public function relatedProducts(): HasMany
    {
        return $this->hasMany(RelatedProduct::class);
    }

    /**
     * Similar products.
     */
    public function similarProducts(): HasMany
    {
        return $this->hasMany(SimilarProduct::class);
    }

    /**
     * Urls materials of products.
     */
    public function materialsUrls(): HasMany
    {
        return $this->hasMany(ProductMaterialsUrls::class);
    }

    /**
     * Advertisement for generator.
     */
    public function adGenerator(): HasMany
    {
        return $this->hasMany(AdGenerator::class);
    }

    /**
     * Advertisement for yandex market.
     */
    public function adYandexMarket(): HasMany
    {
        return $this->hasMany(AdYandexMarket::class);
    }

    /**
     * Product prices.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * In what sections of the catalog is the product displayed?
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    /**
     * Product feature list.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class)
            ->withPivot('value');
    }

    /**
     * Product photos list.
     */
    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductPhoto::class,
            'products',
            'uuid',
            'uuid',
            'uuid',
            'uuid',
        );
    }

    /**
     * Product where serial number description.
     */
    public function whereSn(): HasMany
    {
        return $this->hasMany(ProductWhereSn::class);
    }

    /**
     * Relationship with product group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'product_group_id');
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)->setTimezone('Europe/Moscow')->format('Y-m-d H:i:s');
    }

    public static function productsByType()
    {
        return Product::orderBy('name', 'asc')
            ->get(['name', 'id', 'product_group_id'])
            ->groupBy('product_group_id')
            ->toArray();
    }
}
