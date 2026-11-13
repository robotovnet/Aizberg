<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;

class MarketplaceModule extends Model
{
    use SoftDeletes;

    protected $table = 'marketplace_modules';

    protected $fillable = [
        'client_id',
        'display_name',
        'install_slug',
        'website_url',
        'source_code_url',
        'license_type',
        'license_url',
        'changelog_url',
        'pricing_type',
        'price',
        'currency',
        'author_display_name',
        'short_description',
        'category',
        'detailed_description',
        'icon_path',
        'ratings_count',
        'aggregate_rating',
        'views_count',
        'is_archived',
        'is_blocked',
        'blocked_at',
        'blocked_by_client_id',
        'is_favorite',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
        'blocked_by_client_id' => 'integer',
        'is_favorite' => 'boolean',
        'aggregate_rating' => 'float',
        'ratings_count' => 'integer',
        'views_count' => 'integer',
        'price' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'blocked_by_client_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(MarketplaceModuleTag::class, 'marketplace_module_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MarketplaceModuleVersion::class, 'marketplace_module_id')
            ->orderByDesc('release_date');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(MarketplaceModuleQuestion::class, 'marketplace_module_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MarketplaceModuleReview::class, 'marketplace_module_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MarketplaceModuleRating::class, 'marketplace_module_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(MarketplaceModuleFavorite::class, 'marketplace_module_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(MarketplaceModulePurchase::class, 'marketplace_module_id');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(MarketplaceModuleScreenshot::class, 'marketplace_module_id')
            ->orderBy('position')
            ->orderBy('id');
    }
}
