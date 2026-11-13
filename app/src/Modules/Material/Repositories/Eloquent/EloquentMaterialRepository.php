<?php

declare(strict_types=1);

namespace Modules\Material\Repositories\Eloquent;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Modules\Material\Entities\Material;
use Modules\Material\Repositories\MaterialRepository;

/**
 * Class EloquentMaterialRepository
 * @package Modules\Material\Repositories\Eloquent
 */
class EloquentMaterialRepository implements MaterialRepository
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
            throw new ModelNotFoundException('Material not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        // Преобразуем массивы name и content в JSON формат
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = Str::getNamesJson($data['name']);
            Helper::havePhraseOnDefaultLang($data['name'], 'name');
        }

        if (isset($data['content']) && is_array($data['content'])) {
            if (!empty($data['content'])) {
                $data['content'] = Str::getNamesJson($data['content']);
                // Проверяем только если content не пустой
                Helper::havePhraseOnDefaultLang($data['content'], 'content');
            } else {
                // Если content передан как пустой массив, устанавливаем null
                $data['content'] = null;
            }
        }

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        // Обработка поля name: объединяем с существующими данными
        if (isset($attributes['name']) && is_array($attributes['name'])) {
            // Получаем текущее значение из базы данных
            $currentName = $this->model->name;
            $existingNames = [];
            
            if ($currentName !== null) {
                $decoded = json_decode($currentName, true);
                if (is_array($decoded)) {
                    $existingNames = $decoded;
                }
            }
            
            // Объединяем существующие данные с новыми (новые перезаписывают старые)
            foreach ($attributes['name'] as $nameItem) {
                if (isset($nameItem['lang']) && isset($nameItem['value'])) {
                    $existingNames[$nameItem['lang']] = $nameItem['value'];
                }
            }
            
            // Преобразуем обратно в JSON
            $attributes['name'] = Str::getLangJson($existingNames);
            Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
        }

        // Обработка поля content: объединяем с существующими данными
        if (isset($attributes['content']) && is_array($attributes['content'])) {
            if (!empty($attributes['content'])) {
                // Получаем текущее значение из базы данных
                $currentContent = $this->model->content;
                $existingContent = [];
                
                if ($currentContent !== null) {
                    $decoded = json_decode($currentContent, true);
                    if (is_array($decoded)) {
                        $existingContent = $decoded;
                    }
                }
                
                // Объединяем существующие данные с новыми (новые перезаписывают старые)
                foreach ($attributes['content'] as $contentItem) {
                    if (isset($contentItem['lang']) && isset($contentItem['value'])) {
                        $existingContent[$contentItem['lang']] = $contentItem['value'];
                    }
                }
                
                // Преобразуем обратно в JSON
                $attributes['content'] = Str::getLangJson($existingContent);
                // Проверяем только если content не пустой
                Helper::havePhraseOnDefaultLang($attributes['content'], 'content');
            } else {
                // Если content передан как пустой массив, устанавливаем null
                $attributes['content'] = null;
            }
        }

        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(?string $search): array
    {
        $materials = Material::when($search !== null && $search !== '', function ($query) use ($search) {
            // Поиск в JSON поле name для текущей локали и в обычном тексте (для обратной совместимости)
            $locale = App::getLocale();
            return $query->where(function ($q) use ($search, $locale) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) LIKE ?", ['%' . $search . '%'])
                  ->orWhere('name', 'LIKE', '%' . $search . '%');
            });
        })
        ->get(Material::SELECT_ATTRIBUTES)->toArray();

        // Применяем локализацию к полям name и content
        $materials = Arr::onlyActiveLang(['name', 'content'], App::getLocale(), $materials);

        foreach ($materials as &$material) {
            if ($material['section_name'] == 'Страницы') {
                $material['url'] = \Helper::storefrontUrl() . $material['url_slug'] . '/';
            } else {
                $parts = explode("_", $material['code']);
                if (count($parts) == 2) {
                    $material['url'] = \Helper::storefrontUrl() . 'articles/view' . $parts[0] . '/read' . $parts[1] . '/';
                } else {
                    $material['url'] = 'javascript:void(0);';
                }
            }
        }

        return $materials;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $material = Material::find($this->model->id, Material::SELECT_ATTRIBUTES);
        
        if (!$material) {
            return [];
        }
        
        $data = $material->toArray();
        
        // Применяем локализацию к полям name и content
        $data = Arr::onlyActiveLang(['name', 'content'], App::getLocale(), [$data])[0];
        
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function findPage(int $shopId, string $slug): ?array
    {
        $page = Material::where('shop_id', $shopId)
            ->where('url_slug', $slug)
            ->where('type', 'page')
            ->where('is_active', 'Y')
            ->first(Material::SELECT_ATTRIBUTES);

        if ($page) {
            $pageArray = $page->toArray();
            // Применяем локализацию к полям name и content
            $pageArray = Arr::onlyActiveLang(['name', 'content'], App::getLocale(), [$pageArray])[0];
            return $pageArray;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function sections(): array
    {
        return Material::distinct()->get(['section_name'])->toArray();
    }
}
