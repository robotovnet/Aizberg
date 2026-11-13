<?php

declare(strict_types=1);

namespace Modules\Order\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\City\Entities\City;
use Modules\Client\Entities\Client;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Repositories\OrderRepository;
use Carbon\Carbon;
use Modules\Settings\Entities\Settings;
use Modules\Shop\Entities\Shop;
use Modules\Staff\Entities\Staff;
use Modules\TransportCompany\Entities\CourierCity;
use Modules\TransportCompany\Entities\PaymentMethod;
use Modules\TransportCompany\Entities\PickupPoint;
use Modules\TransportCompany\Entities\Postamat;
use Modules\TransportCompany\Entities\TransportCompany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Class EloquentOrderRepository
 * @package Modules\Order\Repositories\Eloquent
 */
class EloquentOrderRepository implements OrderRepository
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
            throw new ModelNotFoundException('Order not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeTracknumber(int $orderId, string $tracknumber): self
    {
        $order = Order::find($orderId);

        $order->track_number = $tracknumber;
        $order->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeMethodPay(int $orderId, int $paymentMethodId): self
    {
        $order = Order::find($orderId);

        $order->payment_method_id = $paymentMethodId;
        $order->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeStatus(int $orderId, string $status): self
    {
        $order = Order::find($orderId);

        $order->order_status = $status;

        if ($status == 'complete' || $status == 'delivered') {
            $order->completed_at = Carbon::now()->format('Y-m-d H:i:s');

            if ($status == 'complete') {
                $order->payment_status = 'paid';
            }
        }

        $order->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function thanksFeedback(int $orderId): self
    {
        // TODO Order thanks feedback

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function details(int $orderId): array
    {
        $data = Order::where('id', $orderId)
            ->with(['client' => function ($query) {
                $query->select(array_merge(['id'], Client::SELECT_ATTRIBUTES));
            }])
            ->with(['staff' => function ($query) {
                $query->select(array_merge(['id'], Staff::SELECT_ATTRIBUTES));
            }])
            ->with(['shop' => function ($query) {
                $query->select(array_merge(['id'], Shop::SELECT_ATTRIBUTES));
            }])
            ->with(['city' => function ($query) {
                $query->select(array_merge(['id'], City::SELECT_ATTRIBUTES));
            }])
            ->with(['paymentMethod' => function ($query) {
                $query->select(array_merge(['id'], PaymentMethod::SELECT_ATTRIBUTES));
            }])
            ->with(['transportCompany' => function ($query) {
                $query->select(array_merge(['id'], TransportCompany::SELECT_ATTRIBUTES));
            }])
            ->first(Order::SELECT_ATTRIBUTES);

        if (!$data) {
            abort(404);
        }

        $needSave = false;

        if ($data->staff_id === null) {
            $data->staff_id = Auth::id();
            $needSave = true;
        }

        if ($data->viewed != 'Y') {
            $data->viewed = 'Y';
            $needSave = true;
        }

        if ($needSave) {
            $data->save();
        }

        $data = $data->toArray();

        $data['transport_company'] = Arr::onlyActiveLang(
            ['name', 'additional_comment'],
            App::getLocale(),
            [$data['transport_company']]
        );

        $data['payment_method'] = Arr::onlyActiveLang(
            ['name', 'additional_comment'],
            App::getLocale(),
            [$data['payment_method']]
        );

        $data['city'] = Arr::onlyActiveLang(
            ['name'],
            App::getLocale(),
            [$data['city']]
        );

        $data['items'] = OrderItem::
            with(['product' => function ($query) {
                $query->select(array_merge(['products.id'], Product::SELECT_ATTRIBUTES));
            }])
            ->with(['product.photos' => function ($query) {
                $query->select(ProductPhoto::SELECT_ATTRIBUTES)
                    ->where('is_main', 'Y')
                    ->whereNull('product_photos.deleted_at')
                    ->take(1);
            }])
            ->where('order_id', $data['id'])
            ->get(OrderItem::SELECT_ATTRIBUTES)
            ->toArray();

        foreach ($data['items'] as &$item) {
            $item['product'] = Arr::onlyActiveLang(
                ['name', 'overview_text'],
                App::getLocale(),
                $item['product']
            );

            $item['product'][0]['url'] = \Helper::productUrl($item['product'][0]['name'], (int) $item['product'][0]['id']);
        }

        $data['delivery_point'] = null;
        if ($data['delivery_point_type'] == 'postamat') {
            $data['delivery_point'] = Postamat::find($data['delivery_point_id'], Postamat::SELECT_ATTRIBUTES);
        } elseif ($data['delivery_point_type'] == 'courier_city') {
            $data['delivery_point'] = CourierCity::find($data['delivery_point_id'], CourierCity::SELECT_ATTRIBUTES);
        } elseif ($data['delivery_point_type'] == 'pickup') {
            $data['delivery_point'] = PickupPoint::find($data['delivery_point_id'], PickupPoint::SELECT_ATTRIBUTES);
        }

        $data['created_at'] = Carbon::parse($data['created_at'])->setTimezone('Europe/Moscow')->format('H:i d.m.Y');
        $data['domain'] = $this->shopDomain();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function all(int $page, array $filters, string $search, ?string $onlyCount): array
    {
        $filterOrderStatus        = $filters['order_status'] ?? null;
        $filterPaymentMethodId    = $filters['payment_method_id'] ?? null;
        $filterTransportCompanyId = $filters['transport_company_id'] ?? null;
        $filterShopId             = $filters['shop_id'] ?? null;
        $filterPaymentStatus      = $filters['payment_status'] ?? null;
        $filterDeliveryPointType  = $filters['delivery_point_type'] ?? null;
        $filterCityId             = $filters['city_id'] ?? null;
        $filterStaffId            = $filters['staff_id'] ?? null;

        if (isset($filters['dateFrom'])) {
            $filters['dateFrom'] = Carbon::createFromFormat('d/m/Y H:i:s', $filters['dateFrom'] . ' 00:00:00');
        } else {
            $filters['dateFrom'] =  Carbon::today();
        }

        if (isset($filters['dateTo'])) {
            $filters['dateTo'] = Carbon::createFromFormat('d/m/Y H:i:s', $filters['dateTo'] . ' 23:59:59');
        } else {
            $filters['dateTo'] = Carbon::tomorrow();
        }

        //$filters['dateTo']   = $filters['dateTo']->format('Y-m-d h:i:s');
        //$filters['dateFrom'] = $filters['dateFrom']->format('Y-m-d h:i:s');

        //DB::enableQueryLog();
        $query = Order::whereBetween('orders.created_at', [$filters['dateFrom'], $filters['dateTo']])
            ->when(isset($filters['order_status']) !== false, function ($query) use ($filterOrderStatus) {
                return $query->where('order_status', $filterOrderStatus);
            })
            ->when(isset($filters['payment_method_id']) !== false, function ($query) use ($filterPaymentMethodId) {
                return $query->where('payment_method_id', $filterPaymentMethodId);
            })
            ->when(isset($filters['transport_company_id']) !== false, function ($query) use ($filterTransportCompanyId) {
                return $query->where('transport_company_id', $filterTransportCompanyId);
            })
            ->when(isset($filters['shop_id']) !== false, function ($query) use ($filterShopId) {
                return $query->where('shop_id', $filterShopId);
            })
            ->when(isset($filters['payment_status']) !== false, function ($query) use ($filterPaymentStatus) {
                return $query->where('payment_status', $filterPaymentStatus);
            })
            ->when(isset($filters['delivery_point_type']) !== false, function ($query) use ($filterDeliveryPointType) {
                return $query->where('delivery_point_type', $filterDeliveryPointType);
            })
            ->when(isset($filters['city_id']) !== false, function ($query) use ($filterCityId) {
                return $query->where('city_id', $filterCityId);
            })
            ->when(isset($filters['staff_id']) !== false, function ($query) use ($filterStaffId) {
                return $query->where('staff_id', $filterStaffId);
            })

            ->when(isset($filters['unviewed']) !== false, function ($query) {
                return $query->where('viewed', 'N');
            })
            ->when(strlen($search) >= 3, function ($query) use ($search) {
                if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                    return $query
                        ->leftJoin('clients', function ($join) {
                            $join->on('clients.id', '=', 'orders.client_id');
                        })
                        ->where('client_email', '=', $search)
                        ->orWhere('clients.email', '=', $search);
                }
                
                return $query
                    ->leftJoin('clients', function ($join) {
                        $join->on('clients.id', '=', 'orders.client_id');
                    })
                    ->where('client_email', 'LIKE', '%' . $search . '%')
                    ->orWhere('orders.id', trim($search))
                    ->orWhere('client_phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('client_phone_add', 'LIKE', '%' . $search . '%')
                    ->orWhere('client_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('client_surname', 'LIKE', '%' . $search . '%')
                    ->orWhere('clients.phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('clients.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('clients.first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('clients.last_name', 'LIKE', '%' . $search . '%');
            })
            ->with(['client' => function ($query) {
                $query->select(array_merge(['id'], Client::SELECT_ATTRIBUTES));
            }])
            ->with(['staff' => function ($query) {
                $query->select(array_merge(['id'], Staff::SELECT_ATTRIBUTES));
            }])
            ->with(['shop' => function ($query) {
                $query->select(array_merge(['id'], Shop::SELECT_ATTRIBUTES));
            }])
            ->with(['city' => function ($query) {
                $query->select(array_merge(['id'], City::SELECT_ATTRIBUTES));
            }])
            ->with(['paymentMethod' => function ($query) {
                $query->select(array_merge(['id'], PaymentMethod::SELECT_ATTRIBUTES));
            }])
            ->with(['transportCompany' => function ($query) {
                $query->select(array_merge(['id'], TransportCompany::SELECT_ATTRIBUTES));
            }]);

        if (is_null($onlyCount)) {
            $all = $query
                ->skip(($page - 1) * (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->take((int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->orderBy('id', 'desc')
                ->get(Order::SELECT_ATTRIBUTES)
                ->toArray();

            //print_r(DB::getQueryLog());

            foreach ($all as &$order) {
                $order['created_at'] = Carbon::parse($order['created_at'])->setTimezone('Europe/Moscow')->format('H:i d.m.Y');
                //echo print_r($order, true);

                $order['domain'] = $this->shopDomain();

                $order['transport_company'] = Arr::onlyActiveLang(
                    ['name', 'additional_comment'],
                    App::getLocale(),
                    [$order['transport_company']]
                );

                $order['payment_method'] = Arr::onlyActiveLang(
                    ['name', 'additional_comment'],
                    App::getLocale(),
                    [$order['payment_method']]
                );

                $order['city'] = Arr::onlyActiveLang(
                    ['name'],
                    App::getLocale(),
                    [$order['city']]
                );
            }

            return $all;
        } else {
            $onPage = (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'];
            $total = $query->count();

            return [
                'total'      => $total,
                'per_page'   => $onPage,
                'total_page' => ceil($total / $onPage),
            ];
        }
    }

    private function shopDomain()
    {
        $names = explode('.', parse_url(env('APP_URL'))['host']);
        return $names[count($names) - 2] . '.' . $names[count($names) - 1];
    }

    public function create(array $data): int
    {
        // Сохраняем информацию о заказе
        $order = new Order();
            $order->client_id            = $data['client_id'];
            $order->staff_id             = null;
            $order->order_status         = 'new';
            $order->shop_id              = $data['shop_id'];
            $order->city_id              = $data['city_id'];
            $order->payment_method_id    = $data['payment_method_id'];
            $order->payment_status       = 'waiting';
            $order->transport_company_id = $data['transport_company_id'];
            $order->delivery_point_type  = $data['delivery_point_type'];
            $order->delivery_point_id    = $data['delivery_point_id'];
            $order->track_number         = null;
            $order->delivery_cost        = $data['delivery_cost'];
            $order->total_products_cost  = $data['total_products_cost'];
            $order->total_order_cost     = ($data['delivery_cost'] + $data['total_products_cost']);
            $order->order_comment        = strip_tags($data['order_comment']);
            $order->delivery_address     = strip_tags($data['delivery_address']);
            $order->viewed               = 'N';
            $order->client_email         = $data['email'];
            $order->client_phone         = $data['phone'];
            $order->client_name          = $data['client_name'];
            $order->client_surname       = $data['client_surname'];
            $order->is_mobile_order      = $data['is_mobile_order'];
            $order->referrer             = $data['referrer'];
            $order->address_comment      = $data['bank_transfer_details']; // реквизиты для безналичного платежа
            $order->client_phone_add     = is_null($data['postcode']) ? "" : $data['postcode']; // почтовый индекс
        $order->save();

        // Сохраним информацию о составе заказа
        foreach ($data['products'] as $product) {
            $orderItem = new OrderItem();
                $orderItem->order_id   = $order->id;
                $orderItem->product_id = $product['product_id'];
                $orderItem->quantity   = $product['quantity'];
                $orderItem->price      = $product['price'];
                $orderItem->total_cost = ($product['quantity'] * $product['price']);
            $orderItem->save();
        }

        return $order->id;
    }
}
