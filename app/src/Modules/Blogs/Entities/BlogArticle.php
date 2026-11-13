<?php

declare(strict_types=1);

namespace Modules\Blogs\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;

/**
 * @mixin Eloquent
 */
class BlogArticle extends Model
{
    use SoftDeletes;

    protected $table = 'blog_articles';

    protected $fillable = [
        'client_id',
        'section_id',
        'title',
        'preview_image_path',
        'short_description',
        'content',
        'is_archived',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'section_id' => 'integer',
        'is_archived' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(BlogSection::class, 'section_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(BlogArticleTag::class, 'blog_article_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_article_id');
    }
}
