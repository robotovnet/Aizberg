<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUpload extends Model
{
    use HasFactory;

    protected $table = 'image_uploader';

    protected $fillable = [
        'title',
        'category',
        'original_path',
        'micro_path',
    ];
}

