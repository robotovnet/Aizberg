<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceModuleVersion extends Model
{
    protected $table = 'marketplace_module_versions';

    protected $fillable = [
        'marketplace_module_id',
        'version',
        'release_date',
        'changes',
    ];

    protected $casts = [
        'release_date' => 'datetime',
        'changes' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModule::class, 'marketplace_module_id');
    }
}
