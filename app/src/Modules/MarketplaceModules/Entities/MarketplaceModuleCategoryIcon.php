<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Entities;

use Illuminate\Database\Eloquent\Model;

class MarketplaceModuleCategoryIcon extends Model
{
    protected $table = 'marketplace_module_category_icons';

    protected $fillable = [
        'slug',
        'icon_path',
    ];
}
