<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use App\Helpers\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Catalog\Entities\Brand;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Settings\Entities\Settings;

/**
 * Class EloquentBrandRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentBrandRepository implements BrandRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * Repository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Brand not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');
        $this->checkBrandNameUnique($data['name']);

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        // Обработка поля name: объединяем с существующими данными
        if (isset($attributes['name'])) {
            // Получаем текущее значение из базы данных
            $currentName = $this->model->name;
            $existingNames = [];
            
            if ($currentName !== null) {
                $decoded = json_decode($currentName, true);
                if (is_array($decoded)) {
                    $existingNames = $decoded;
                }
            }
            
            // Если name пришел как массив (необработанные данные)
            if (is_array($attributes['name'])) {
                // Объединяем существующие данные с новыми (новые перезаписывают старые)
                foreach ($attributes['name'] as $nameItem) {
                    if (isset($nameItem['lang']) && isset($nameItem['value'])) {
                        $existingNames[$nameItem['lang']] = $nameItem['value'];
                    }
                }
            } else {
                // Если name пришел как JSON строка (уже обработанные данные из контроллера)
                $newNames = json_decode($attributes['name'], true);
                if (is_array($newNames)) {
                    // Объединяем существующие данные с новыми (новые перезаписывают старые)
                    $existingNames = array_merge($existingNames, $newNames);
                }
            }
            
            // Преобразуем обратно в JSON
            $attributes['name'] = Str::getLangJson($existingNames);
        }
        
        Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        $this->checkBrandNameUnique($attributes['name'], $this->model->id);

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $totalProducts = Product::where('brand_id', $this->model->id)->count();

        if ($totalProducts > 0) {
            return throw ValidationException::withMessages(
                ['brand' => 'Нельзя удалить данный бренд, так как в каталоге есть товары данного бренда.']
            );
        }

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(bool $isHidden = false, ?string $search): array
    {
        $all = Brand::orderBy('name', 'asc')
            ->when($isHidden === true, function ($query) {
                return $query->where('is_hidden', 'N');
            })
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                return $query
                    ->where('name', 'LIKE', '%' . $search . '%');
            })
            ->get(Brand::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Brand::find($this->model->id, Brand::SELECT_ATTRIBUTES)->toArray();

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * Checks the brand name for uniqueness.
     * @throws ValidationException
     */
    private function checkBrandNameUnique(string $jsonName, int $exceptId = 0): void
    {
        $langCode = Settings::where('code', 'DEFAULT_LANGUAGE')->first()->toArray()['value'];
        $decodedName = json_decode($jsonName, true);
        
        // Проверяем наличие ключа языка по умолчанию перед обращением
        if (!is_array($decodedName) || !isset($decodedName[$langCode])) {
            // Если нет данных для языка по умолчанию, пропускаем проверку уникальности
            return;
        }
        
        $name = mb_strtolower($decodedName[$langCode]);

        $brands = Brand::where('id', '!=', $exceptId)->get();
        foreach ($brands as $brand) {
            $brandName = json_decode($brand['name'], true);

            if (isset($brandName[$langCode]) && mb_strtolower($brandName[$langCode]) == $name) {
                throw ValidationException::withMessages(
                    ['brand_exist' => 'Бренд с данным именем уже существует']
                );
            }
        }
    }
}
