<?php

declare(strict_types=1);

namespace Modules\DashboardMenu\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

/**
 * Class DashboardMenuDatabaseSeeder
 * @package Modules\DashboardMenu\Database\Seeders
 */
class DashboardMenuDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'dashboard_menus';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $items = [
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Продажи',
                                                     'en' => 'Sales',
                                                 ]),
                'code'         => '1_sales',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'sales.svg',
                'image_inline' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="3.5" cy="12.5" r="1.5" fill="#428AB4"/><circle cx="10.5" cy="12.5" r="1.5" fill="#428AB4"/><path d="M0.239223 3.19612C0.115465 2.57732 0.588758 2 1.2198 2H10C10.5523 2 11 2.44772 11 3V7C11 7.55228 10.5523 8 10 8H2.0198C1.54312 8 1.13271 7.66354 1.03922 7.19612L0.239223 3.19612Z" fill="#428AB4"/><path d="M3 9H11V10H4C3.44772 10 3 9.55228 3 9Z" fill="#428AB4"/><rect x="12" width="2" height="1" fill="#428AB4"/><path d="M10 1C10 0.447715 10.4477 0 11 0H12V9C12 9.55228 11.5523 10 11 10H10V1Z" fill="#428AB4"/></svg>',
                'level'        => 1,
                'policy'       => null,
                'parent'       => null,
                'symbol'       => null,
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Модули',
                                                     'en' => 'Modules',
                                                 ]),
                'code'         => '1_modules',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'modules.svg',
                'image_inline' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="2" r="2" fill="#428AB4"/><circle cx="12" cy="9" r="2" fill="#428AB4"/><path d="M0 4C0 3.44771 0.447715 3 1 3H10C10.5523 3 11 3.44772 11 4V13C11 13.5523 10.5523 14 10 14H0V4Z" fill="#428AB4"/></svg>',
                'level'        => 1,
                'policy'       => null,
                'parent'       => null,
                'symbol'       => null,
                'order'        => '2000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Отчеты',
                                                     'en' => 'Reports',
                                                 ]),
                'code'         => '1_reports',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'reports.svg',
                'image_inline' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="12" width="12" height="1" fill="#428AB4"/><rect x="1" y="1" width="1" height="11" fill="#428AB4"/><rect x="3" y="4" width="2" height="7" rx="1" fill="#428AB4"/><rect x="6" y="1" width="2" height="10" rx="1" fill="#428AB4"/><rect x="9" y="3" width="2" height="8" rx="1" fill="#428AB4"/><path d="M1.5 0.0067749L2.5 1.5L1.5 1.5L0.5 1.5L1.5 0.0067749Z" fill="#428AB4"/><path d="M13.9932 12.5L12.5 13.5L12.5 12.5L12.5 11.5L13.9932 12.5Z" fill="#428AB4"/></svg>',
                'level'        => 1,
                'policy'       => null,
                'parent'       => null,
                'symbol'       => null,
                'order'        => '3000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Уведомления',
                                                     'en' => 'Notifications',
                                                 ]),
                'code'         => '1_notifications',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'notifications.svg',
                'image_inline' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 2C10.0689 2 11.0243 6.59003 13.0099 10.3558C13.3872 11.0714 12.8884 12 12.0793 12H1.92066C1.11163 12 0.61276 11.0714 0.990098 10.3558C2.97569 6.59003 3.93114 2 7 2Z" fill="#428AB4"/><path d="M6 1C6 0.447715 6.44772 0 7 0C7.55228 0 8 0.447715 8 1V3C8 3.55228 7.55228 4 7 4C6.44772 4 6 3.55228 6 3V1Z" fill="#428AB4"/><path d="M5 13H9C9 13.5523 8.55228 14 8 14H6C5.44772 14 5 13.5523 5 13Z" fill="#428AB4"/></svg>',
                'level'        => 1,
                'policy'       => null,
                'parent'       => null,
                'symbol'       => null,
                'order'        => '4000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Настройки',
                                                     'en' => 'settings',
                                                 ]),
                'code'         => '1_settings',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'settings.svg',
                'image_inline' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.99997 1C4.99997 0.447723 5.44767 0 5.99997 0H7.99994C8.55222 0 8.99994 0.447723 8.99994 1V1.3414C9.75968 1.60992 10.4492 2.0271 11.0335 2.55801L11.1339 2.5C11.6122 2.22385 12.2238 2.38773 12.5 2.86603L13.5 4.59808C13.7761 5.07635 13.6122 5.68796 13.1339 5.96411L12.9301 6.08179C12.9761 6.38113 12.9999 6.68777 12.9999 7C12.9999 7.45935 12.9483 7.90665 12.8506 8.3364L13.1339 8.5C13.6122 8.77615 13.7761 9.38773 13.5 9.86603L12.5 11.5981C12.2238 12.0764 11.6122 12.2402 11.1339 11.9641L10.7074 11.7178C10.1983 12.1185 9.62268 12.4385 8.99994 12.6586V13C8.99994 13.5523 8.55222 14 7.99994 14H5.99997C5.44767 14 4.99997 13.5523 4.99997 13V12.6586C4.35581 12.4309 3.76212 12.0964 3.24027 11.6762L2.74167 11.9641C2.2634 12.2402 1.65177 12.0764 1.37564 11.5981L0.375644 9.86603C0.0995208 9.38773 0.2634 8.77615 0.741672 8.5L1.13523 8.27277C1.0466 7.86255 0.999972 7.43674 0.999972 7C0.999972 6.71072 1.02048 6.42627 1.06003 6.14792L0.741672 5.96411C0.2634 5.68796 0.0995208 5.07635 0.375644 4.59808L1.37564 2.86603C1.65177 2.38773 2.2634 2.22385 2.74167 2.5L2.91849 2.60208C3.51291 2.05017 4.21933 1.61731 4.99997 1.3414V1ZM6.99997 10C8.65683 10 9.99997 8.65686 9.99997 7C9.99997 5.34314 8.65683 4 6.99997 4C5.34311 4 3.99997 5.34314 3.99997 7C3.99997 8.65686 5.34311 10 6.99997 10Z" fill="#428AB4"/></svg>',
                'level'        => 1,
                'policy'       => null,
                'parent'       => null,
                'symbol'       => null,
                'order'        => '5000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Помощь',
                                                     'en' => 'Docs',
                                                 ]),
                'code'         => '1_doc',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 1,
                'policy'       => 'always',
                'parent'       => null,
                'symbol'       => '?',
                'order'        => '6000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Профиль',
                                                     'en' => 'Profile',
                                                 ]),
                'code'         => '1_profile',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 1,
                'policy'       => 'always',
                'parent'       => null,
                'symbol'       => 'A',
                'order'        => '7000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Настройки',
                                                     'en' => 'Settings',
                                                 ]),
                'code'         => '2_module_settings',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'settings.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="4.5" x2="4.5" y2="20" stroke="#F0F0F0"/><line x1="10.5" x2="10.5" y2="20" stroke="#F0F0F0"/><line x1="16.5" x2="16.5" y2="20" stroke="#F0F0F0"/><line x1="3.5" y1="3" x2="3.5" y2="7" stroke="#F0F0F0"/><line x1="5.5" y1="3" x2="5.5" y2="7" stroke="#F0F0F0"/><line x1="9.5" y1="13" x2="9.5" y2="17" stroke="#F0F0F0"/><line x1="11.5" y1="13" x2="11.5" y2="17" stroke="#F0F0F0"/><line x1="15.5" y1="8" x2="15.5" y2="12" stroke="#F0F0F0"/><line x1="17.5" y1="8" x2="17.5" y2="12" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_modules',
                'symbol'       => null,
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Заказы',
                                                     'en' => 'Orders',
                                                 ]),
                'code'         => '2_orders',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'orders.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)" stroke="#F0F0F0"><path d="M3 5.5h5m-5 3h7m-7 3h6m-6 3h6M1.5 2v16m.5-.5h8m-8-15h10m-.5.5v8m-8-9V0m.5.5h10m-.5.5v9m3.708 1.665a4 4 0 1 0 1.628 2.2m.518-2.511-5 5m-2-2.708 2 2"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h20v20H0z"/></clipPath></defs></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_sales',
                'symbol'       => null,
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Пользователи',
                                                     'en' => 'Users',
                                                 ]),
                'code'         => '2_users',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'users.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><path d="M16.1122 13.1994C17.7082 14.824 18.8221 17.0419 19.2749 19.5128H10.0272H10.0257L0.735786 19.4557C1.19893 16.9875 2.32229 14.7763 3.9252 13.1615C5.64803 11.426 7.80962 10.4932 10.0241 10.5C12.2387 10.5068 14.3965 11.4529 16.1122 13.1994Z" stroke="#F0F0F0"/><circle cx="10" cy="5" r="4.5" stroke="#F0F0F0"/></g><defs><clipPath id="clip0"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Города и Склады',
                                                     'en' => 'Cities and Warehouses',
                                                 ]),
                'code'         => '2_cities_warehouses',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'cities.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5 8C17.5 10.0452 16.3296 12.4367 14.723 14.6096C13.2263 16.6338 11.4108 18.388 10 19.3926C8.58919 18.388 6.77373 16.6338 5.27704 14.6096C3.67041 12.4367 2.5 10.0452 2.5 8C2.5 3.85786 5.85786 0.5 10 0.5C14.1421 0.5 17.5 3.85786 17.5 8Z" stroke="#F0F0F0"/><circle cx="10" cy="8" r="3.5" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '3000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Каталог товаров',
                                                     'en' => 'Catalogue',
                                                 ]),
                'code'         => '2_catalogue',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'catalogue.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><path d="M2.64039 19.5L5.39039 8.5H19.3596L16.6096 19.5H2.64039Z" stroke="#F0F0F0"/><path d="M2.50009 20L0.741943 5.00001L5.00009 5L7.50009 5.00001L9.19733 5.00001C9.39476 5.00001 9.58777 5.05845 9.75204 5.16796L10.7482 5.83206C10.9125 5.94158 11.1055 6.00001 11.3029 6.00001H15.0001L15.5001 8.50001" stroke="#F0F0F0"/></g><defs><clipPath id="clip0"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '4000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Справочники',
                                                     'en' => 'Directories',
                                                 ]),
                'code'         => '2_directories',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'directories.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3H9.4L6.82134 2.38603C5.75535 2.13223 4.64465 2.13223 3.57866 2.38603L1 3V19L4.3601 18.3C5.30966 18.1022 6.28998 18.1038 7.23887 18.3049L10 18.89M10 3V18.89M10 3L12.9815 2.33745C13.9816 2.11519 15.0184 2.11519 16.0185 2.33745L19 3V19L15.9348 18.3188C14.9886 18.1086 14.009 18.0971 13.0581 18.2852L10 18.89" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '5000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Интеграции',
                                                     'en' => 'Integrations',
                                                 ]),
                'code'         => '2_integrations',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'integrations.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><line y1="17.5" x2="12" y2="17.5" stroke="#F0F0F0"/><line x1="8" y1="14.5" x2="20" y2="14.5" stroke="#F0F0F0"/><line y1="11.5" x2="12" y2="11.5" stroke="#F0F0F0"/><line x1="8" y1="8.5" x2="20" y2="8.5" stroke="#F0F0F0"/><line y1="5.5" x2="12" y2="5.5" stroke="#F0F0F0"/><line x1="8" y1="2.5" x2="20" y2="2.5" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '6000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Цены',
                                                     'en' => 'Prices',
                                                 ]),
                'code'         => '2_prices',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'prices.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="9.5" stroke="#F0F0F0"/><path d="M13 7.5L12.8187 6.59354C12.6199 5.59942 11.9324 4.77297 10.9911 4.39645L10.8718 4.34871C10.305 4.12202 9.68127 4.07968 9.08909 4.22773L8.98081 4.2548C8.3497 4.41258 7.80005 4.79992 7.4392 5.3412L7.40085 5.39872C7.13948 5.79078 7 6.25145 7 6.72265V6.72265C7 7.52067 7.39883 8.26589 8.06282 8.70855L8.2557 8.83713C8.41813 8.94542 8.59136 9.03654 8.77261 9.10905L11 10L11.382 10.191C12.3736 10.6868 13 11.7003 13 12.809V12.9739C13 13.3199 12.9194 13.6611 12.7647 13.9706V13.9706C12.5912 14.3175 12.3302 14.6132 12.0075 14.8283L11.7558 14.9962C11.263 15.3247 10.6839 15.5 10.0917 15.5H9.74829C9.58304 15.5 9.41808 15.4863 9.25509 15.4592L8.96598 15.411C7.54297 15.1738 6.5 13.9426 6.5 12.5V12.5" stroke="#F0F0F0"/><line x1="9.5" y1="4" x2="9.5" y2="2" stroke="#F0F0F0"/><line x1="9.5" y1="15" x2="9.5" y2="18" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '7000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Параметры',
                                                     'en' => 'Parameters',
                                                 ]),
                'code'         => '2_parameters',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'integrations.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="4.5" x2="4.5" y2="20" stroke="#F0F0F0"/><line x1="10.5" x2="10.5" y2="20" stroke="#F0F0F0"/><line x1="16.5" x2="16.5" y2="20" stroke="#F0F0F0"/><line x1="3.5" y1="3" x2="3.5" y2="7" stroke="#F0F0F0"/><line x1="5.5" y1="3" x2="5.5" y2="7" stroke="#F0F0F0"/><line x1="9.5" y1="13" x2="9.5" y2="17" stroke="#F0F0F0"/><line x1="11.5" y1="13" x2="11.5" y2="17" stroke="#F0F0F0"/><line x1="15.5" y1="8" x2="15.5" y2="12" stroke="#F0F0F0"/><line x1="17.5" y1="8" x2="17.5" y2="12" stroke="#F0F0F0"/></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_settings',
                'symbol'       => null,
                'order'        => '7000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Клиенты',
                                                     'en' => 'Clients',
                                                 ]),
                'code'         => '2_clients',
                'image_src'    => Config::get('dashboardmenu.menu_path_icons_top') . 'user.svg',
                'image_inline' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_424_0)"><circle cx="3.5" cy="6.5" r="2" stroke="#F0F0F0"/><path d="M18.5356 12.6854C18.8749 13.5352 19.0797 14.6575 19.2173 15.9432C19.3346 17.0396 19.4003 18.2231 19.4668 19.4206C19.4683 19.4471 19.4697 19.4735 19.4712 19.5H16.0087H14.4936C14.4662 18.4163 14.3552 17.4272 14.2466 16.5041C14.24 16.4478 14.2333 16.3918 14.2268 16.336C14.1123 15.3656 14.0075 14.4776 14.0004 13.5952C14.0995 13.3448 14.1943 13.0876 14.2837 12.8449C14.3465 12.6746 14.4066 12.5115 14.4637 12.363C14.6206 11.955 14.7711 11.6103 14.9484 11.3283C15.1229 11.0507 15.3133 10.8503 15.5426 10.7166C15.7698 10.5841 16.0698 10.4968 16.4962 10.5C16.8432 10.5026 17.1647 10.6174 17.49 10.928C17.8306 11.2533 18.1832 11.8023 18.5356 12.6854Z" stroke="#F0F0F0"/><path d="M1.46437 12.6854C1.12515 13.5353 0.920318 14.6575 0.782708 15.9432C0.665366 17.0396 0.59967 18.2231 0.533191 19.4206C0.531723 19.4471 0.530254 19.4736 0.528784 19.5H3.99134H5.50638C5.53376 18.4163 5.64482 17.4272 5.75342 16.5041C5.76005 16.4478 5.76665 16.3918 5.77323 16.3361C5.88767 15.366 5.99239 14.4783 5.9996 13.5963C5.64288 12.7159 5.2977 11.9294 4.88265 11.3572C4.46128 10.7763 4.02795 10.496 3.50378 10.5C3.1568 10.5026 2.83534 10.6174 2.51004 10.928C2.16941 11.2533 1.81682 11.8024 1.46437 12.6854Z" stroke="#F0F0F0"/><circle cx="16.5" cy="6.5" r="2" stroke="#F0F0F0"/><path d="M13.0332 10.1792C13.8713 12.3629 14.2633 16.015 14.4707 19.5H10.0147L5.53042 19.4295C5.75071 15.9704 6.23161 12.3863 6.97434 10.1581C7.04041 9.95991 7.09769 9.76291 7.15111 9.57881L7.15342 9.57086C7.20688 9.38662 7.2563 9.21629 7.31063 9.0527C7.421 8.72031 7.54044 8.4522 7.70885 8.23521C8.01482 7.84096 8.58425 7.48884 10.0069 7.50004C11.4327 7.51126 11.9775 7.86233 12.2701 8.24839C12.4329 8.46331 12.5482 8.72816 12.662 9.06271C12.6982 9.16935 12.7367 9.29165 12.7777 9.4219C12.8538 9.66339 12.9384 9.9322 13.0332 10.1792Z" stroke="#F0F0F0"/><circle cx="10" cy="3" r="2.5" stroke="#F0F0F0"/></g><defs><clipPath id="clip0_424_0"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                'level'        => 2,
                'policy'       => null,
                'parent'       => '1_sales',
                'symbol'       => null,
                'order'        => '2000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($items);
        }
    }
}
