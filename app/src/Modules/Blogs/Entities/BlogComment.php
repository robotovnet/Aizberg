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
class BlogComment extends Model
{
    use SoftDeletes;

    protected $table = 'blog_comments';

    protected $fillable = [
        'blog_article_id',
        'client_id',
        'parent_id',
        'body',
    ];

    protected $casts = [
        'blog_article_id' => 'integer',
        'client_id' => 'integer',
        'parent_id' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }
}
