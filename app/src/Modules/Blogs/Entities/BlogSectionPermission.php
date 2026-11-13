<?php

declare(strict_types=1);

namespace Modules\Blogs\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Client\Entities\Client;

/**
 * @mixin Eloquent
 */
class BlogSectionPermission extends Model
{
    protected $table = 'blog_section_permissions';

    protected $fillable = [
        'client_id',
    ];

    protected $casts = [
        'client_id' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
