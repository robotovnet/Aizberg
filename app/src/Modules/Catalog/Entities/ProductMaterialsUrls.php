<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Modules\Catalog\Entities\Product;
use DB;

/**
 * Class ProductMaterialsUrls
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class ProductMaterialsUrls extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_materials_urls';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'title',
        'url',
        'type',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_materials_urls.id',
        'product_id',
        'title',
        'url',
        'type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function search($search)
    {
        if (
            DB::table('feature_product')
            ->where('value', $search)
            ->count() > 0
        ) {
            return $this->whereIn('product_id', function ($q) use ($search) {
                $q->from('feature_product')
                    ->selectRaw('product_id')
                    ->where('value', $search);
            });
        } else {
            return $this->whereIn('product_id', function ($q) use ($search) {
                $q->from('products')
                    ->selectRaw('id')
                    ->where('name', 'LIKE', "%" . $search . "%");
            });
        }
    }

    public function byProductType($productTypeId)
    {
        return $this->whereIn('product_id', function ($q) use ($productTypeId) {
            $q->from('products')
                ->selectRaw('id')
                ->where('product_group_id', $productTypeId);
        });
    }
}
