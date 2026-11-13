<?php

declare(strict_types=1);

namespace App\Providers;

use App\Helpers\LangPhrases;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Str::macro(
            'getNamesJson',
            /**
             * Returns a list of names in json format.
             */
            function (array $allNames): string {
                $names = [];

                foreach ($allNames as $name) {
                    if (!isset($name['value'])) {
                        $name['value'] = '';
                    }
                    $name['value'] = (is_null($name['value'])) ? '' : $name['value'];

                    // Не используем addslashes, так как json_encode сам правильно экранирует
                    $names[$name['lang']] = $name['value'];
                }

                return Str::getLangJson($names);
            }
        );

        Str::macro(
            'getLangJson',
            /**
             * Creates a json with language phrases.
             *
             * @param  array  $phrases
             * @return string
             */
            function (array $phrases): string {
                // Используем стандартный json_encode для корректного экранирования
                return json_encode($phrases, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        );

        Arr::macro(
            'onlyActiveLang',
            /**
             * Leave only the client's current language.
             *
             * @param  array|string[]|string  $fields
             * @param  string        $locale
             * @param  array         $data
             * @return array
             */
            function (array|string $fields, string $locale, array $data): array {
                if (!is_array($fields)) {
                    $fields = [$fields];
                }

                foreach ($fields as $field) {
                    foreach ($data as $key => $entity) {
                        if (isset($entity[$field])) {
                            $data[$key][$field] = @(json_decode($entity[$field], true))[$locale];
                        }
                    }
                }

                return $data;
            }
        );

        Module::macro(
            'getModuleName',
            /**
             * Returns the name of the module from the namespace.
             *
             * @param  string  $namespace
             * @return string
             */
            function (string $namespace): string {
                return strtolower(explode('\\', $namespace)[1]);
            }
        );

        Module::macro(
            'getModuleNames',
            /**
             * Returns an array containing module names
             * @param  string  $modulesList
             * @return array
             */
            function (string $modulesList): array {
                $modules = explode(',', $modulesList);

                return array_map(function ($name) {
                    return ucfirst(trim($name));
                }, $modules);
            }
        );

        Module::macro(
            'getClassName',
            /**
             * Returns the name of a specific class of a specific module.
             *
             * @param  string  $modulePath
             * @param  string  $dirPath
             * @param  string  $className
             * @return string
             */
            function (string $modulePath, string $dirPath, string $className): string {
                $partsPath = explode('/', rtrim($modulePath, '/'));

                $moduleDirName  = array_pop($partsPath);
                $modulesDirName = array_pop($partsPath);

                return implode("\\", [$modulesDirName, $moduleDirName, $dirPath, $className]);
            }
        );

        JsonResponse::macro(
            'successful',
            /**
             * Returns a successful response.
             */
            function (array $data = null): JsonResponse {
                $data = (isset($data)) ? $data : [ 'message' => LangPhrases::SUCCESS_MESSAGE ];

                return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
            }
        );

        JsonResponse::macro(
            'failed',
            /**
             * Returns a failed response.
             */
            function (array $data = null): JsonResponse {
                $data = (isset($data)) ? $data : [ 'message' => LangPhrases::ERROR_MESSAGE ];

                return Response::json($data, 400, [], JSON_UNESCAPED_UNICODE);
            }
        );

        Collection::macro(
            'removeNestedItems',
            /**
             * Removes Nested Items.
             */
            function (Collection $collection, string $sourceItem, array|string $deleteItem): Collection {
                return $collection->map(function ($element) use ($sourceItem, $deleteItem) {
                    $element[$sourceItem] = $element[$sourceItem]->map(function ($nestedElement) use ($deleteItem) {
                        Arr::forget($nestedElement, $deleteItem);
                        return $nestedElement;
                    });

                    return $element;
                });
            }
        );

        Collection::macro(
            'localizeNestedItems',
            /**
             * Localize nested items.
             */
            function (Collection $collection, string $sourceItem, array $localizeItems): Collection {
                return $collection->map(function ($element) use ($sourceItem, $localizeItems) {
                    $element = $element->toArray();

                    $element[$sourceItem] = Arr::onlyActiveLang(
                        $localizeItems,
                        App::getLocale(),
                        $element[$sourceItem]
                    );

                    return $element;
                });
            }
        );

        Collection::macro(
            'localizeAndCleanUp',
            /**
             * Implements localization and removes unnecessary data.
             */
            function (
                Collection $collection,
                string $relation,
                string|array $deleteItem,
                array $localizeItems
            ): Collection {
                return Collection::localizeNestedItems(
                    Collection::removeNestedItems($collection, $relation, $deleteItem),
                    $relation,
                    $localizeItems
                );
            }
        );
    }
}
