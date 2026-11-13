<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class OneClickCatalogSection
 * @package Modules\OrderOneClick\Entities
 * @mixin Eloquent
 */
class OneClickCatalogSection extends Model
{
    /** @var string Table name. */
    protected $table = 'one_click_catalog_sections';

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'catalog_section_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'catalog_section_id',
    ];
}
