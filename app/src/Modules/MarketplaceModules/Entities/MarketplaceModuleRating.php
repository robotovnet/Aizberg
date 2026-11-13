<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Client\Entities\Client;

class MarketplaceModuleRating extends Model
{
    protected $table = 'marketplace_module_ratings';

    protected $fillable = [
        'marketplace_module_id',
        'client_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModule::class, 'marketplace_module_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
