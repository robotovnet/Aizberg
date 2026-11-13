<?php

declare(strict_types=1);

namespace Modules\Mask\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Mask.
 * @package Modules\Mask\Entities
 * @mixin Eloquent
 */
class Mask extends Model
{
    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = false;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [];
}
