<?php

declare(strict_types=1);

namespace Modules\Qa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Qa
 * @package Modules\Qa\Entities
 * @mixin Eloquent
 */
class Qa extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_qa';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'client_id',
        'staff_id',
        'reply_to_qid',
        'replied',
        'content',
        'reply_to_mail',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'product_id',
        'client_id',
        'staff_id',
        'reply_to_qid',
        'replied',
        'content',
        'reply_to_mail',
        'created_at',
    ];
}
