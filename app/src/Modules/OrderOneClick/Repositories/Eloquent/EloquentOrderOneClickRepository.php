<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Modules\OrderOneClick\Entities\OneClickCatalogSection;
use Modules\OrderOneClick\Entities\OneClickOrder;
use Modules\OrderOneClick\Entities\OneClickSubscribers;
use Modules\OrderOneClick\Repositories\OrderOneClickRepository;
use Carbon\Carbon;
use Modules\Settings\Entities\Settings;

/**
 * Class EloquentOrderOneClickRepository
 * @package Modules\OrderOneClick\Repositories\Eloquent
 */
class EloquentOrderOneClickRepository implements OrderOneClickRepository
{
    /**
     * @inheritDoc
     */
    public function setSubscribers(array $subscribers): self
    {
        $all = [];

        foreach ($subscribers as $subscriber) {
            $all[] = [
                'email' => $subscriber,
            ];
        }

        OneClickSubscribers::truncate();
        OneClickSubscribers::insert($all);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCatalogSections(array $sections): self
    {
        $catalogSections = [];

        foreach ($sections as $sectionsId) {
            $catalogSections[] = [
                'catalog_section_id' => $sectionsId,
            ];
        }

        OneClickCatalogSection::truncate();
        OneClickCatalogSection::insert($catalogSections);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function completed(int $orderId, string $isCompleted): void
    {
        $order = OneClickOrder::find($orderId);
        if ($order) {
            $order->is_completed = $isCompleted;
            $order->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribers(): array
    {
        return OneClickSubscribers::all()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getCatalogSections(): array
    {
        return OneClickCatalogSection::all()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function all(string $onlyCompleted, string $dateFrom, string $dateTo, int $page, ?string $onlyCount): array
    {
        $dateFrom = Carbon::createFromFormat('d/m/Y H:i:s', $dateFrom . ' 00:00:00');
        $dateTo = Carbon::createFromFormat('d/m/Y H:i:s', $dateTo . ' 23:59:59');

        $isCompleted = ($onlyCompleted == 'Y') ? ['Y'] : ['N'];

        if ($onlyCompleted == 'ALL') {
            $isCompleted = ['Y', 'N'];
        }

        $query = OneClickOrder::with(['product' => function ($query) {
                $query->select(['name', 'id', 'vendor_code']);
        }])
        ->whereIn('is_completed', $isCompleted)
        ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if (is_null($onlyCount)) {
            $all = $query
                ->skip(($page - 1) * (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->take((int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->get(OneClickOrder::SELECT_ATTRIBUTES)
                ->toArray();

            foreach ($all as $key => $order) {
                $all[$key]['product'] = Arr::onlyActiveLang(
                    ['name'],
                    App::getLocale(),
                    [$order['product']]
                );

                $all[$key]['product'][0]['url'] = \Helper::productUrl($all[$key]['product'][0]['name'], (int) $all[$key]['product'][0]['id']);
                $all[$key]['created_at'] = Carbon::parse($all[$key]['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
            }

            return $all;
        } else {
            return ['total' => $query->count()];
        }
    }
}
