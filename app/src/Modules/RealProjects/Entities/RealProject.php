<?php

declare(strict_types=1);

namespace Modules\RealProjects\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Проект, в котором используется Aizberg (блок «В реальных проектах»).
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $banner_path
 * @property int $sort
 *
 * @package Modules\RealProjects\Entities
 * @mixin Eloquent
 */
class RealProject extends Model
{
    /** @var string Table name. */
    protected $table = 'real_projects';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var int Sort value used when it is not passed. */
    public const DEFAULT_SORT = 500;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'url',
        'banner_path',
        'sort',
    ];

    /** @var array Attribute casts. */
    protected $casts = [
        'sort' => 'integer',
    ];
}
