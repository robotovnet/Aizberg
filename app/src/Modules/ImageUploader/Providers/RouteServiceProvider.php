<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\ImageUploader\Http\Controllers';

    public function boot(): void
    {
        parent::boot();

        Route::model('image', \Modules\ImageUploader\Entities\ImageUpload::class);
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('ImageUploader', '/Routes/web.php'));
    }

}

