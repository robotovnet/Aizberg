<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class ProductPhoto
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class ProductPhoto extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_photos';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'file_path',
        'file_name',
        'file_extension',
        'file_width',
        'file_height',
        'is_main',
        'order',
        'uuid',
        'content_hash',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_photos.id',
        'file_path',
        'file_name',
        'file_extension',
        'file_width',
        'file_height',
        'is_main',
        'order',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
