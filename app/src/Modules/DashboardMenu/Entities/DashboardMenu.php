<?php

declare(strict_types=1);

namespace Modules\DashboardMenu\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DashboardMenuItems
 * @package Modules\DashboardMenu\Entities
 * @mixin Eloquent
 */
class DashboardMenu extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'code', 'parent', 'level', 'href', 'image_src', 'image_inline', 'symbol', 'order'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'name',
        'code',
        'parent',
        'href',
        'image_src',
        'image_inline',
        'symbol',
        'order',
        'policy',
        'level'];

    /** @var string[] List of attributes to be localized. */
    public const LOCALIZATION_ATTRIBUTES = ['name'];
}
