<?php

namespace Modules\Order\Services;

use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Catalog\Entities\Product;
use Shuchkin\SimpleXLSXGen;
use Carbon\Carbon;

class ReportsService
{
    public function productsInOrdersReport(string $fromDate, string $toDate): void
    {
        $fromDateCarbon = new Carbon($fromDate . " 00:00:00", 'Europe/Moscow');
        $toDateCarbon = new Carbon($toDate . " 23:59:59", 'Europe/Moscow');

        $orders = Order::where('order_status', 'cancel')
            ->whereBetween('created_at', [$fromDateCarbon->format('Y-m-d 00:00:00'), $toDateCarbon->format('Y-m-d 23:59:59')])
            ->get(['id']);

        $cancelOrders = [];
        foreach ($orders as $order) {
            $cancelOrders[] = $order->id;
        }

        $productsInOrders = OrderItem::whereNotIn('order_id', $cancelOrders)
            ->whereBetween('created_at', [$fromDateCarbon->format('Y-m-d 00:00:00'), $toDateCarbon->format('Y-m-d 23:59:59')])
            ->get(['product_id', 'quantity', 'price']);

        $stats = [];
        foreach ($productsInOrders as $data) {
            if (!isset($stats[$data['product_id']])) {
                $stats[$data['product_id']] = [
                    'quantity' => $data['quantity'],
                    'sum' => $data['quantity'] * $data['price'],
                ];
            } else {
                $stats[$data['product_id']] = [
                    'quantity' => $stats[$data['product_id']]['quantity'] + $data['quantity'],
                    'sum' => $stats[$data['product_id']]['sum'] + ($data['quantity'] * $data['price']),
                ];
            }
        }

        $excelData = [
            ['Статистика по заказам за период c ' . $fromDateCarbon->format('d.m.Y') . ' по ' . $toDateCarbon->format('d.m.Y')],
            ['<b>Наименование</b>', '<center><b>Проданное количество</b></center>', '<center><b>Цена за единицу</b></center>', '<center><b>Сумма</b></center>'],
        ];

        foreach ($stats as $productId => $productStats) {
            $product = Product::find($productId);
            $productName = json_decode($product->name)->ru;
            $avgPrice = round($productStats['sum'] / $productStats['quantity'], 0);

            array_push($excelData, [$productName, $productStats['quantity'], $avgPrice, $productStats['sum']]);
        }

        array_push($excelData, ['<b>Всего</b>', '', '', '<f>SUM(D3:D' . (count($stats) + 2) . ')</f>']);

        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($excelData, 'Stats');

        $xlsx->setColWidth(1, 70);
        $xlsx->setColWidth(2, 25);
        $xlsx->setColWidth(3, 20);
        $xlsx->setColWidth(4, 10);

        $xlsx->mergeCells('A1:D1');
        $xlsx->freezePanes('A3');

        $xlsx->downloadAs('sales_stats_' . date("d_m_Y") . '.xlsx');
    }
}
