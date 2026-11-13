<?php

declare(strict_types=1);

namespace Modules\Ad\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class SectionYml
 * @package Modules\Ad\Entities
 * @mixin Eloquent
 */
class SectionYml extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'section_yml';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'section_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'section_id',
    ];
}
