<?php

namespace Modules\YandexWordstat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class YandexWordstat
 * @package Modules\YandexWordstat\Entities
 * @mixin Eloquent
 */
class YandexWordstat extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'yandex_wordstats';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    protected $fillable = [
        'vendor_code',
        'phrase',
        'type',
        'total',
        'response',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'vendor_code',
        'phrase',
        'type',
        'total',
        'response',
    ];

    protected $casts = [
        'vendor_code' => 'string',
        'phrase' => 'string',
        'type' => 'string',
        'total' => 'integer',
        'response' => 'string',
    ];
}
