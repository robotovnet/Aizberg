<?php

declare(strict_types=1);

namespace Modules\Blogs\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
class BlogArticleTag extends Model
{
    public $timestamps = false;

    protected $table = 'blog_article_tags';

    protected $fillable = [
        'blog_article_id',
        'tag',
    ];

    protected $casts = [
        'blog_article_id' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }
}
