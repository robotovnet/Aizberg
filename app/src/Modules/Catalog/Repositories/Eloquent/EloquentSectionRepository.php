<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Modules\Catalog\Entities\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Catalog\Repositories\SectionRepository;
use App\Helpers\Helper;
use Modules\TransportCompany\Entities\PMBlockedSection;

/**
 * Class EloquentSectionRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentSectionRepository implements SectionRepository
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
            throw new ModelNotFoundException('Section not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        Helper::havePhraseOnDefaultLang($data['name'], 'name');

        if ($data['parent_section_id'] == 0) {
            $data['parent_section_id'] = null;
        }

        $data['seo_starting_phrases'] = null;
        $data['seo_desc'] = null;

        if (isset($data['seo'])) {
            $data['seo_starting_phrases'] = implode(',', $data['seo']['starting_phrases']);
            $data['seo_desc'] = $data['seo']['desc'];
        }

        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        if (!isset($attributes['thumbnail'])) {
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
                
                // Если name пришел как JSON строка (уже обработанные данные из контроллера)
                $newNames = json_decode($attributes['name'], true);
                if (is_array($newNames)) {
                    // Объединяем существующие данные с новыми (новые перезаписывают старые)
                    $existingNames = array_merge($existingNames, $newNames);
                }
                
                // Преобразуем обратно в JSON
                $attributes['name'] = Str::getLangJson($existingNames);
                
                Helper::havePhraseOnDefaultLang($attributes['name'], 'name');
            }

            if ($attributes['parent_section_id'] == 0) {
                $attributes['parent_section_id'] = null;
            }

            $attributes['seo_starting_phrases'] = null;
            $attributes['seo_desc'] = null;

            if (isset($attributes['seo'])) {
                $attributes['seo_starting_phrases'] = (is_null($attributes['seo']['starting_phrases'])) ?
                    '' :
                    implode(',', $attributes['seo']['starting_phrases']);

                $attributes['seo_desc'] = $attributes['seo']['desc'];
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
        PMBlockedSection::where('section_id', $this->model->id)->delete();

        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function seoDataDelete(): self
    {
        $this->model->seo_starting_phrases = null;
        $this->model->seo_desc = null;
        $this->model->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(bool $needLevel = true, bool $withoutRootSections = false, string $orderField = 'order'): array
    {
        $all = Section::withCount('products')->withCount('relatedProducts')
            ->when($withoutRootSections === true, function ($query) {
                return $query->whereNotNull('parent_section_id');
            })
            ->orderBy($orderField)
            ->get(Section::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($all as &$section) {
            if (isset($section['seo_starting_phrases'])) {
                $section['seo_starting_phrases'] = explode(',', $section['seo_starting_phrases']);
            }

            if ($needLevel) {
                $section['level_nesting'] = $this->getSectionLevel($section['id']);
            }
        }

        return Arr::onlyActiveLang(['name'], App::getLocale(), $all);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = Section::with('relatedProducts')
            ->find($this->model->id, Section::SELECT_ATTRIBUTES)->toArray();

        if ($data['seo_starting_phrases'] != null) {
            $data['seo_starting_phrases'] = explode(',', $data['seo_starting_phrases']);
        }

        return Arr::onlyActiveLang(['name'], App::getLocale(), [$data])[0];
    }

    /**
     * @inheritDoc
     */
    public function displayOnMainPage(string $shouldDisplay): void
    {
        $this->model->update(['should_display_on_main_page' => $shouldDisplay]);
    }

    /**
     * @inheritDoc
     */
    public function relatedProducts(array $products): void
    {
        $this->model->relatedProducts()->detach();
        $this->model->relatedProducts()->attach($products);
    }

    /**
     * @inheritDoc
     */
    public function order(array $order): void
    {
        foreach ($order as $item) {
            $section = Section::find($item['section_id']);
            $section->order = $item['order'];
            $section->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function seoData(array $data): void
    {
        $this->model->seo_starting_phrases = (is_array($data['starting_phrases']))
            ? implode(',', $data['starting_phrases']) : '' ;
        $this->model->seo_desc = $data['desc'];
        $this->model->save();
    }

    /**
     * @inheritDoc
     */
    public function orderTiledCatalog(array $order): void
    {
        foreach ($order as $item) {
            $section = Section::find($item['section_id']);
            $section->tiled_order = $item['order'];
            $section->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function getParentSectionId(): int
    {
        return (int)$this->model->parent_section_id;
    }

    /**
     * Defines the nesting level of a catalog section
     */
    private function getSectionLevel(int $sectionId): int
    {
        $level = 0;
        $section = Section::find($sectionId);

        while (true) {
            $level++;

            if (!isset($section['parent_section_id']) || $section['parent_section_id'] === null) {
                break;
            } else {
                $section = Section::find($section['parent_section_id']);
            }
        }

        return $level;
    }
}
