<?php

namespace Modules\Lottery\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Lottery\Entities\LotteryPrizes;
use Modules\Lottery\Repositories\Eloquent\EloquentLotteryPrizesRepository;
use Modules\Lottery\Repositories\LotteryPrizesRepository;
use Modules\Lottery\Entities\LotteryApps;
use Modules\Lottery\Repositories\Eloquent\EloquentLotteryAppsRepository;
use Modules\Lottery\Repositories\LotteryAppsRepository;
use Modules\Lottery\Console\LotteryDrawsCommand;
use Modules\Lottery\Entities\LotteryDraws;
use Modules\Lottery\Repositories\Eloquent\EloquentLotteryDrawsRepository;
use Modules\Lottery\Repositories\LotteryDrawsRepository;
use Modules\Lottery\Entities\LotteryTickets;
use Modules\Lottery\Repositories\Eloquent\EloquentLotteryTicketsRepository;
use Modules\Lottery\Repositories\LotteryTicketsRepository;

class LotteryServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Lottery';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'lottery';

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

        $this->app->bind(LotteryPrizesRepository::class, function () {
            return new EloquentLotteryPrizesRepository(new LotteryPrizes());
        });

        $this->app->bind(LotteryAppsRepository::class, function () {
            return new EloquentLotteryAppsRepository(new LotteryApps());
        });

        $this->app->bind(LotteryDrawsRepository::class, function () {
            return new EloquentLotteryDrawsRepository(new LotteryDraws());
        });

        $this->app->bind(LotteryTicketsRepository::class, function () {
            return new EloquentLotteryTicketsRepository(new LotteryTickets());
        });

        $this->commands([
            LotteryDrawsCommand::class,
        ]);
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
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
