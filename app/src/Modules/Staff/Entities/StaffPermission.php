<?php

declare(strict_types=1);

namespace Modules\Staff\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StaffPermission
 * @package Modules\Staff\Entities
 * @mixin Eloquent
 */
class StaffPermission extends Model
{
    use SoftDeletes;

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['name', 'policy', 'group_name'];

    /** @var string[] List of attributes to be localized. */
    public const LOCALIZATION_ATTRIBUTES = ['name', 'group_name'];

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'policy', 'group_name'];
}
