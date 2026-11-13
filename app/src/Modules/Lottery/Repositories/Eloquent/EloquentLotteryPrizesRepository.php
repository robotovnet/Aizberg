<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Lottery\Entities\LotteryPrizes;
use Modules\Lottery\Repositories\LotteryPrizesRepository;
use Modules\Catalog\Entities\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * Class EloquentLotteryPrizesRepository
 * @package Modules\Lottery\Repositories\Eloquent
 */
class EloquentLotteryPrizesRepository implements LotteryPrizesRepository
{
    public function __construct(
        private Model $model
    ) {}

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Lottery prizes not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $all = LotteryPrizes::query()
            ->where('status', 'playing')
            ->with([
                'product:id,vendor_code,name,product_group_id',
                'product.group:id,plural_name'
            ])
            ->get([
                'id',
                'product_id',
                'status',
                'week',
                'year'
            ])->toArray();


        foreach($all as $key => $prize) {
            $all[$key]['status'] = 'Участвует в розыгрышах';
            $all[$key]['product']['name'] = json_decode($prize['product']['name'], true)[App::getLocale()];
            $all[$key]['product']['group']['plural_name'] = json_decode($prize['product']['group']['plural_name'], true)[App::getLocale()];
        }

        return $all;
    }

    /**
     * @inheritDoc
     */
    public function add(string $productVendorCode): int
    {
        $product = Product::query()
            ->where('vendor_code', $productVendorCode)
            ->firstOrFail();
        
        return LotteryPrizes::create([
            'product_id' => $product->id,
            'status' => 'playing',
            'week' => '0',
            'year' => '0'
        ])->id;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $prizeId): bool
    {
        $prize = LotteryPrizes::query()->findOrFail($prizeId);
        return $prize->delete();
    }
}
