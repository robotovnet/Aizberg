<?php

declare(strict_types=1);

namespace Modules\Showcase\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
class ShowcaseExampleModule extends Model
{
    public $timestamps = false;

    protected $table = 'showcase_example_modules';

    protected $fillable = [
        'showcase_example_id',
        'module_name',
    ];

    protected $casts = [
        'showcase_example_id' => 'integer',
    ];

    public function example(): BelongsTo
    {
        return $this->belongsTo(ShowcaseExample::class, 'showcase_example_id');
    }
}
