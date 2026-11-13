<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Client\Entities\Client;

class MarketplaceModuleReview extends Model
{
    protected $table = 'marketplace_module_reviews';

    protected $fillable = [
        'marketplace_module_id',
        'client_id',
        'review_text',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModule::class, 'marketplace_module_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(MarketplaceModuleReviewVote::class, 'marketplace_module_review_id');
    }
}
