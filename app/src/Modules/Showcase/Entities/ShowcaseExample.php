<?php

declare(strict_types=1);

namespace Modules\Showcase\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;

/**
 * @mixin Eloquent
 */
class ShowcaseExample extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $table = 'showcase_examples';

    protected $fillable = [
        'client_id',
        'category_id',
        'website_url',
        'name',
        'short_description',
        'moderation_status',
        'approved_at',
        'approved_by_client_id',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'category_id' => 'integer',
        'approved_by_client_id' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'approved_by_client_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShowcaseCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShowcaseExampleImage::class, 'showcase_example_id')
            ->orderBy('sort_order');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ShowcaseExampleModule::class, 'showcase_example_id');
    }

    public function isApproved(): bool
    {
        return $this->moderation_status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->moderation_status === self::STATUS_PENDING;
    }
}
