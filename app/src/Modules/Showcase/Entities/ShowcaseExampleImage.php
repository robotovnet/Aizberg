<?php

declare(strict_types=1);

namespace Modules\Showcase\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
class ShowcaseExampleImage extends Model
{
    public $timestamps = false;

    protected $table = 'showcase_example_images';

    protected $fillable = [
        'showcase_example_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'showcase_example_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function example(): BelongsTo
    {
        return $this->belongsTo(ShowcaseExample::class, 'showcase_example_id');
    }
}
