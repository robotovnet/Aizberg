<?php

namespace Modules\YandexWordstat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\YandexWordstat\Entities\YandexWordstat;

class GetStatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private const API_BASE_URL = 'http://xmlriver.com/wordstat/new/json';
    private const PAGETYPE = 'words';
    private const PERIOD = 'month';

    private string $url;

     public function __construct(
        public string $vendorCode,
        public string $query,
        public string $type,
    ) {
        $this->query = $query;
        $this->vendorCode = $vendorCode;
        $this->type = $type;
        $this->url = $this->buildApiUrl();
    }

    private function buildApiUrl(): string
    {
        $start = date('01.m.Y', strtotime('first day of -3 months'));
        $end = date('t.m.Y', strtotime('-1 month'));

        return sprintf(
            '%s?user=%s&key=%s&pagetype=%s&period=%s&start=%s&end=%s',
            self::API_BASE_URL,
            (int) config('yandex_wordstat.xmlriver_user_id'),
            config('yandex_wordstat.xmlriver_api_key'),
            self::PAGETYPE,
            self::PERIOD,
            $start,
            $end
        );
    }

    /**
     * Выполнить задание.
     */
    public function handle(): int
    {
        // Инициализация cURL
        $ch = curl_init();

        // Настройка параметров запроса
        curl_setopt($ch, CURLOPT_URL, $this->url . '&query=' . urlencode($this->query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Выполнение запроса
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return -1;
        }

        // Закрытие соединения
        curl_close($ch);

        // Декодирование JSON-ответа
        $data = json_decode($response, true);

        if (isset($data['error'])) {
            // Надо вернуть ошибку с описанием
            throw new \Exception($data['error']);
        }

        /*
        // Для вкладки "Динамика"
        $totalLastMonths = 0;
        if (isset($data['graph'])) {
            foreach ($data['graph']['images']['timeSeries']['rawValues'] as $key => $value) {
                $value = $value['absoluteValue'] ?? 0;
                $totalLastMonths += (int)$value;
            }
        }
        */

        $totalLastMonths = $data['popular'][0]['value'] ?? 0;

        $yandexWordstat = new YandexWordstat();
        $yandexWordstat->vendor_code = $this->vendorCode;
        $yandexWordstat->phrase = $this->query;
        $yandexWordstat->type = $this->type;
        $yandexWordstat->response = $response;
        $yandexWordstat->total = $totalLastMonths;
        $yandexWordstat->save();

        return 0;

        //echo "Запрос: <b>{$query}</b><br>\n";
        //echo "{$data['totalValue']} (за последние 30 дней)<br>\n";
        //echo "{$totalLastMonths} (за три месяца)<br>\n";
    }
}