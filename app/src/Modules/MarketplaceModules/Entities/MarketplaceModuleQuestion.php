<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Client\Entities\Client;

class MarketplaceModuleQuestion extends Model
{
    protected $table = 'marketplace_module_questions';

    protected $fillable = [
        'marketplace_module_id',
        'client_id',
        'question_text',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModule::class, 'marketplace_module_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MarketplaceModuleAnswer::class, 'marketplace_module_question_id')
            ->orderBy('created_at');
    }
}
