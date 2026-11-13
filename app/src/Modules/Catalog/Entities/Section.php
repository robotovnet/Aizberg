<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Section
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Section extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'catalog_sections';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'parent_section_id',
        'product_group_id',
        'thumbnail',
        'is_hidden',
        'not_for_everyone',
        'sorting_method',
        'order',
        'seo_starting_phrases',
        'seo_desc',
        'should_display_on_main_page',
        'tiled_order',
        'is_active_tab_parameters',
        'is_bp_disabled',
        'additional_text',
        'starting_text_product_name',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'catalog_sections.id',
        'name',
        'parent_section_id',
        'product_group_id',
        'thumbnail',
        'is_hidden',
        'not_for_everyone',
        'sorting_method',
        'order',
        'seo_starting_phrases',
        'seo_desc',
        'should_display_on_main_page',
        'tiled_order',
        'is_active_tab_parameters',
        'is_bp_disabled',
        'additional_text',
        'starting_text_product_name',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_catalog_products');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'product_group_id');
    }
}
