<?php

namespace Modules\Staff\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Staff\Entities\StaffGroup;
use Modules\Staff\Entities\Staff;
use Modules\Staff\Repositories\StaffGroupRepository;
use Modules\Staff\Repositories\StaffRepository;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Staff\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::pattern('group_id', '^[0-9]+$');
        Route::pattern('staff_id', '^[0-9]+$');

        Route::bind('group_id', function ($groupId) {
            return app()->make(StaffGroupRepository::class, ['model' => new StaffGroup()])->setModel($groupId);
        });

        Route::bind('staff_id', function ($staffId) {
            return app()->make(StaffRepository::class, ['model' => new Staff()])->setModel($staffId);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Staff', '/Routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Staff', '/Routes/api.php'));
    }
}
