<?php

declare(strict_types=1);

namespace Modules\Staff\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Staff\Repositories\Eloquent\EloquentStaffRepository;
use Modules\Staff\Repositories\StaffRepository;
use Modules\Staff\Repositories\Eloquent\EloquentStaffGroupRepository;
use Modules\Staff\Repositories\StaffGroupRepository;
use Modules\Staff\Repositories\Eloquent\EloquentStaffPermissionRepository;
use Modules\Staff\Repositories\StaffPermissionRepository;
use Modules\Staff\Entities\Staff;
use Modules\Staff\Entities\StaffGroup;
use Modules\Staff\Entities\StaffPermission;
use Illuminate\Support\Facades\Config;
use Modules\Staff\Services\ConfirmationCodeService as CodeService;
use Modules\Staff\Services\UserpicService;

/**
 * Class StaffServiceProvider
 * @package Modules\Staff\Providers
 */
class StaffServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'Staff';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'staff';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(StaffRepository::class, function () {
            return new EloquentStaffRepository(new Staff());
        });

        $this->app->bind(StaffGroupRepository::class, function () {
            return new EloquentStaffGroupRepository(new StaffGroup());
        });

        $this->app->bind(StaffPermissionRepository::class, function () {
            return new EloquentStaffPermissionRepository(new StaffPermission());
        });

        $this->app->bind(CodeService::class, function () {
            return new CodeService(request()->segment(3));
        });

        $this->app->bind(UserpicService::class, function () {
            return new UserpicService(request()->file('userpic'));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
