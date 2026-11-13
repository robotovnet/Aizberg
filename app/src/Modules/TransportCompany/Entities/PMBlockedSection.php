<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class PMBlockedSection
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class PMBlockedSection extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'pm_blocked_sections';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'section_id',
        'pm_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'section_id',
        'pm_id',
    ];
}
