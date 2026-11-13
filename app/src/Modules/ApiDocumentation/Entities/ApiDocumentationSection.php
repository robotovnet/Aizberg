<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ApiDocumentation\Casts\UnicodeJson;

class ApiDocumentationSection extends Model
{
    protected $table = 'api_documentation_sections';

    protected $fillable = [
        'api_documentation_id',
        'parent_id',
        'level',
        'position',
        'slug',
        'title',
        'description_blocks',
        'http_method',
        'endpoint_url',
        'request_body_content_type',
        'request_body_schema',
        'request_headers',
        'request_parameters',
        'request_example',
        'responses',
    ];

    protected $casts = [
        'api_documentation_id' => 'integer',
        'parent_id' => 'integer',
        'level' => 'integer',
        'position' => 'integer',
        'title' => UnicodeJson::class,
        'description_blocks' => UnicodeJson::class,
        'request_body_schema' => UnicodeJson::class,
        'request_headers' => UnicodeJson::class,
        'request_parameters' => UnicodeJson::class,
        'request_example' => UnicodeJson::class,
        'responses' => UnicodeJson::class,
    ];

    public function documentation(): BelongsTo
    {
        return $this->belongsTo(ApiDocumentation::class, 'api_documentation_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('position')
            ->orderBy('id');
    }
}
