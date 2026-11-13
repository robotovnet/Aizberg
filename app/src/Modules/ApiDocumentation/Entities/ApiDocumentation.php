<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ApiDocumentation\Casts\UnicodeJson;
use Modules\Client\Entities\Client;

class ApiDocumentation extends Model
{
    use SoftDeletes;

    protected $table = 'api_documentations';

    protected $fillable = [
        'client_id',
        'slug',
        'name',
        'short_description',
        'tags',
        'default_locale',
        'is_published',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'name' => UnicodeJson::class,
        'short_description' => UnicodeJson::class,
        'tags' => UnicodeJson::class,
        'is_published' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ApiDocumentationSection::class, 'api_documentation_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function rootSections(): HasMany
    {
        return $this->sections()->whereNull('parent_id');
    }
}
