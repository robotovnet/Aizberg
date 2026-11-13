<?php

namespace Modules\MatrixType\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class MatrixType
 * @package Modules\MatrixType\Entities
 * @mixin Eloquent
 */
class MatrixType extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'matrix_types';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    protected $fillable = [
        'name',
        'margin_up_to_BP',
        'EXW_price',
        'weight_in_kg',
        'MRP_markup',
        'RRP_markup',
        'margin_up_to_P2',
        'margin_up_to_P3',
        'margin_up_to_P4',
        'mark_up_contract'
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'name',
        'margin_up_to_BP',
        'EXW_price',
        'weight_in_kg',
        'MRP_markup',
        'RRP_markup',
        'margin_up_to_P2',
        'margin_up_to_P3',
        'margin_up_to_P4',
        'mark_up_contract',
        'created_at',
    ];

    protected $casts = [
        'name' => 'string',
        'margin_up_to_BP' => 'float',
        'EXW_price' => 'float',
        'weight_in_kg' => 'float',
        'MRP_markup' => 'float',
        'RRP_markup' => 'float',
        'margin_up_to_P2' => 'float',
        'margin_up_to_P3' => 'float',
        'margin_up_to_P4' => 'float',
        'mark_up_contract' => 'float'
    ];
} 