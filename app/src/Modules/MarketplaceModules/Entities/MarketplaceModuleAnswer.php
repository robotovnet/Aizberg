<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Client\Entities\Client;

class MarketplaceModuleAnswer extends Model
{
    protected $table = 'marketplace_module_answers';

    protected $fillable = [
        'marketplace_module_question_id',
        'client_id',
        'answer_text',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(MarketplaceModuleQuestion::class, 'marketplace_module_question_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
