<?php

declare(strict_types=1);

namespace Modules\DashboardMenu\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Modules\DashboardMenu\Entities\DashboardMenu;
use Modules\DashboardMenu\Repositories\DashboardMenuRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentDashboardMenuRepository
 * @package Modules\DashboardMenu\Repositories\Eloquent
 */
class EloquentDashboardMenuRepository implements DashboardMenuRepository
{
    /**
     * @var Model An instance of the Eloquent Model
     */
    protected Model $model;

    private array $salesPolicies = [
        'orderoneclick.view',
        'order.view',
        'client.view',
    ];

    private array $settingsPolicies = [
        'ad.yml.view',
        'bonusprogram.level.view',
        'catalog.groups.view',
        'catalog.sections.view',
        'catalog.features.view',
        'catalog.filters.view',
        'catalog.products.view',
        'catalog.sections.management',
        'catalog.sections.management',
        'catalog.brands.view',
        'city.view',
        'clienttype.view',
        'country.view',
        'currency.view',
        'material.view',
        'orderoneclick.management',
        'pricetype.view',
        'settings.view',
        'shop.view',
        'staff.groups.view',
        'staff.view',
        'transportcompany.view',
        'transportcompany.view',
        'transportcompany.view',
        'transportcompany.view',
        'transportcompany.warehouses.view',
        'paymentmethods.view',
        'qa.view',
    ];

    /**
     * EloquentLanguageRepository constructor.
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $policies = Auth::user()->getAllPolicy();

        $locale = App::getLocale();
        $items = $menuItems = [];

        for ($level = 1; $level < 4; $level++) {
            $items[$level] = $this->model
                ->where('level', $level)
                ->orderBy('order')
                ->get(DashboardMenu::SELECT_ATTRIBUTES)
                ->toArray();
        }

        $salesMenuItem = false;
        foreach ($this->salesPolicies as $policy) {
            if (in_array($policy, $policies)) {
                $salesMenuItem = true;
                break;
            }
        }

        $settingsMenuItem = false;
        foreach ($this->settingsPolicies as $policy) {
            if (in_array($policy, $policies)) {
                $settingsMenuItem = true;
                break;
            }
        }

        // Level 1
        foreach ($items[1] as $item1) {
            if ($item1['code'] == '1_sales' && $salesMenuItem === false) {
                continue;
            }
            if ($item1['code'] == '1_settings' && $settingsMenuItem === false) {
                continue;
            }

            $item1 = Arr::onlyActiveLang('name', $locale, [$item1])[0];

            // Level 2
            foreach ($items[2] as $item2) {
                $item2 = Arr::onlyActiveLang('name', $locale, [$item2])[0];

                // Level 3
                foreach ($items[3] as $item3) {
                    $item3 = Arr::onlyActiveLang('name', $locale, [$item3])[0];

                    if ($item2['code'] == $item3['parent']) {
                        // We leave the menu items that the user has access to
                        if (in_array($item3['policy'], $policies) || $item3['policy'] == 'always') {
                            $item2['children'][] = $item3;
                        }
                    }
                }

                if ($item1['code'] == $item2['parent']) {
                    $item1['children'][] = $item2;
                }
            }

            $menuItems[] = $item1;
        }

        // Delete all items that have no children
        foreach ($menuItems as $key => $item1) {
            if ($item1['policy'] != 'always' && !isset($item1['children'])) {
                unset($menuItems[$key]);
            } elseif (isset($item1['children'])) {
                foreach ($item1['children'] as $key2 => $item2) {
                    if ($item2['policy'] != 'always' && !isset($item2['children'])) {
                        unset($menuItems[$key]['children'][$key2]);
                    }
                }

                $menuItems[$key]['children'] = array_values($menuItems[$key]['children']);
            }
        }

        return array_values($menuItems);
    }
}
//DA SDSD D DS  ADS A DASDfda dadsd asadsads fdssdf dfsf sdsf dfs sf sdfsdsfd
