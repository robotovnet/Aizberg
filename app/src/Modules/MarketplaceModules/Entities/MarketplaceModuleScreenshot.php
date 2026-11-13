<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceModuleScreenshot extends Model
{
    protected $table = 'marketplace_module_screenshots';

    protected $fillable = [
        'marketplace_module_id',
        'path',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModule::class, 'marketplace_module_id');
    }
}
