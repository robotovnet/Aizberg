<?php

declare(strict_types=1);

namespace Modules\Settings\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\Settings\Repositories\SettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\SettingsSection;
use Modules\Settings\Entities\Settings;

/**
 * Class EloquentSettingsRepository
 * @package Modules\Settings\Repositories\Eloquent
 */
class EloquentSettingsRepository implements SettingsRepository
{
    /** @var Model An instance of the Eloquent Model */
    protected Model $model;

    /**
     * EloquenSettingsRepository constructor.
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
            throw new ModelNotFoundException('Setting not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSettingByCode(string $code): string
    {
        return ($this->model->where('code', $code)->first(['value'])->toArray())['value'];
    }

    /**
     * @inheritDoc
     */
    public function all(?string $code): array
    {
        $type = ($code == null) ? 'system' : 'module';

        $all = SettingsSection::
            with(['items' => function ($query) {
                $query
                    ->orderBy('order')
                    ->select(Settings::SELECT_ATTRIBUTES);
            }])
            ->when($code != null, function ($query) use ($code) {
                return $query->where('code', $code);
            })
            ->where('type', $type)
            ->orderBy('order')
            ->get(SettingsSection::SELECT_ATTRIBUTES);

        return Arr::onlyActiveLang(['name'], App::getLocale(), Collection::localizeAndCleanUp(
            $all,
            'items',
            ['section_code'],
            Settings::LOCALIZATION_ATTRIBUTES
        )->toArray());
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $settingCode): array
    {
        $data = Settings::where('code', $settingCode)
            ->first(Settings::SELECT_ATTRIBUTES)
            ->toArray();

        if (count($data) > 0) {
            return Arr::onlyActiveLang(
                Settings::LOCALIZATION_ATTRIBUTES,
                App::getLocale(),
                [$data]
            )[0];
        }

        return throw new ModelNotFoundException('Setting not found.');
    }
}
