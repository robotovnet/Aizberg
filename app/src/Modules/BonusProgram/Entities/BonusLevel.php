<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class BonusLevel
 * @package Modules\BonusProgram\Entities
 * @mixin Eloquent
 */
class BonusLevel extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'bonus_levels';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'client_type_id',
        'level_num',
        'amount_up',
        'amount_instantly_up',
        'amount_saving',
        'is_entry',
        'calculation_period',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'name',
        'client_type_id',
        'level_num',
        'amount_up',
        'amount_instantly_up',
        'amount_saving',
        'is_entry',
        'calculation_period',
    ];
}
