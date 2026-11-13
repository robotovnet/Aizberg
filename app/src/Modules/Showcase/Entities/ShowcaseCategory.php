<?php

declare(strict_types=1);

namespace Modules\Showcase\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
class ShowcaseCategory extends Model
{
    protected $table = 'showcase_categories';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function examples(): HasMany
    {
        return $this->hasMany(ShowcaseExample::class, 'category_id');
    }
}
