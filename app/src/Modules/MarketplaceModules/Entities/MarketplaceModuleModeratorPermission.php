<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Client\Entities\Client;

class MarketplaceModuleModeratorPermission extends Model
{
    protected $table = 'marketplace_module_moderator_permissions';

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
