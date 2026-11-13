<?php

declare(strict_types=1);

namespace Modules\Settings\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Settings.
 * @package Modules\Settings\Entities
 * @mixin Eloquent
 */
class Settings extends Model
{
    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'order',
        'code',
        'name',
        'value',
        'section_code',
        'placeholder',
        'items',
        'type_form_element'
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'order',
        'code',
        'name',
        'value',
        'section_code',
        'placeholder',
        'items',
        'type_form_element'
    ];

    /** @var string[] List of attributes to be localized. */
    public const LOCALIZATION_ATTRIBUTES = ['name', 'placeholder'];
}
