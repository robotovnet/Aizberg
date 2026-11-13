<?php

declare(strict_types=1);

namespace Modules\Material\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Material
 * @package Modules\Material\Entities
 * @mixin Eloquent
 */
class Material extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'materials';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'shop_id',
        'name',
        'url_slug',
        'code',
        'section_name',
        'type',
        'is_active',
        'meta_keywords',
        'meta_description',
        'content',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'shop_id',
        'name',
        'url_slug',
        'code',
        'section_name',
        'type',
        'is_active',
        'meta_keywords',
        'meta_description',
        'content',
    ];
}
