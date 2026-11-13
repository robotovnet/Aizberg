<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Client\Entities\Client;

class MarketplaceModuleReviewVote extends Model
{
    protected $table = 'marketplace_module_review_votes';

    protected $fillable = [
        'marketplace_module_review_id',
        'client_id',
        'is_helpful',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModuleReview::class, 'marketplace_module_review_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
