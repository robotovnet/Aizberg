<?php

declare(strict_types=1);

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserpic extends Model
{
    protected $table = 'client_userpics';

    protected $fillable = [
        'client_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'size' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
