<?php

declare(strict_types=1);

namespace Modules\Blogs\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Client\Entities\Client;

/**
 * @mixin Eloquent
 */
class BlogSection extends Model
{
    protected $table = 'blog_sections';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'sort_order',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(BlogArticle::class, 'section_id');
    }
}
