<?php

namespace Modules\YandexWordstat\Services;

use Illuminate\Http\UploadedFile;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use Modules\YandexWordstat\Jobs\GetStatJob;
use Modules\YandexWordstat\Entities\YandexWordstat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class WordstatService
{
    public function uploadExcel(UploadedFile $file)
    {
        if ($xlsx = SimpleXLSX::parse($file)) {
            
            foreach ($xlsx->rows() as $line => $row) {
                if($line == 0) {
                    continue;
                }

                if (trim($row[0]) == "") {
                    break;
                }

                $vendorCode = trim($row[0]);

                $allPN     = explode(",", $row[1]);
                $allModels = explode(",", $row[2]);

                foreach ($allPN as $pn) {
                    if ($this->phraseHasStat(trim($pn)) || trim($pn) == "") {
                        continue;
                    }

                    GetStatJob::dispatch($vendorCode, trim($pn), 'pn')->onQueue('wordstat');
                }

                foreach ($allModels as $model) {
                    if ($this->phraseHasStat(trim($model)) || trim($model) == "") {
                        continue;
                    }

                    GetStatJob::dispatch($vendorCode, trim($model), 'model')->onQueue('wordstat');
                }
            }

            return response()->json([
                'success' => true,
            ]);
        } else {
            echo SimpleXLSX::parseError();
        }
    }

    public function downloadExcel()
    {
        $excelData = [
            [
                '<style bgcolor="#92D050"><b>АРТИКУЛ</b></style>', 
                '<style bgcolor="#92D050"><b>СОВМЕСТИМЫЕ P/N</b></style>',
                '<style bgcolor="#92D050"><b>МОДЕЛИ</b></style>',
                '<style bgcolor="#FFC000"><b>ЗАПРОСЫ - СОВМЕСТИМЫЕ P/N</b></style>',
                '<style bgcolor="#FFC000"><b>НЕТ ЗАПРОСОВ - СОВМЕСТИМЫЕ P/N</b></style>', 
                '<style bgcolor="#FFFF00"><b>ЗАПРОСЫ МОДЕЛИ</b></style>', 
                '<style bgcolor="#FFFF00"><b>НЕТ ЗАПРОСОВ - МОДЕЛИ</b></style>', 
                '<style bgcolor="#00B0F0"><b>ЗАПРОСЫ - PN/МОДЕЛЬ</b></style>',
                '<style bgcolor="#00B0F0"><b>НЕТ ЗАПРОСОВ - PN/МОДЕЛЬ</b></style>',
                '<style bgcolor="#BA55D3"><b>ТОП ЗАПРОС</b></style>',
                '<style bgcolor="#BA55D3"><b>ОБЩЕЕ КОЛ-ВО ЗАПРОСОВ</b></style>'
            ],
        ];

        $stats = YandexWordstat::select('vendor_code')
            ->distinct()
            ->get()
            ->pluck('vendor_code');

        foreach ($stats as $vendorCode) {
            $stats = YandexWordstat::where('vendor_code', $vendorCode)
                ->orderBy('id', 'asc')
                ->get(['phrase','type','total']);
            
            $b = []; // СОВМЕСТИМЫЕ P/N
            $c = []; // МОДЕЛИ
            $d = []; // ЗАПРОСЫ - СОВМЕСТИМЫЕ P/N
            $e = []; // НЕТ ЗАПРОСОВ - СОВМЕСТИМЫЕ P/N
            $f = []; // ЗАПРОСЫ МОДЕЛИ
            $g = []; // НЕТ ЗАПРОСОВ - МОДЕЛИ
            $h = []; // ЗАПРОСЫ - PN/МОДЕЛЬ
            $i = []; // НЕТ ЗАПРОСОВ - PN/МОДЕЛЬ
            $totals = [];
            foreach ($stats as $stat) {
                $totals[] = $stat->total;

                if($stat->type == 'pn') {
                    $b[] = trim($stat->phrase);
                
                    if ($stat->total > 0) {
                        $d[$stat->total] = trim($stat->phrase);
                        $h[$stat->total] = trim($stat->phrase);
                    } else {
                        $e[] = trim($stat->phrase);
                        $i[] = trim($stat->phrase);
                    }
                
                } else {
                    $c[] = trim($stat->phrase);

                    if ($stat->total > 0) {
                        $f[$stat->total] = trim($stat->phrase);
                        $h[$stat->total] = trim($stat->phrase);
                    } else {
                        $g[] = trim($stat->phrase);
                        $i[] = trim($stat->phrase);
                    }
                }
            }

            krsort($d);
            krsort($f);
            krsort($h);

            array_push($excelData, [
                "<top>" . $vendorCode . "</top>",
                "<top><wraptext>" . implode(', ', $b) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $c) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $d) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $e) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $f) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $g) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $h) . "</wraptext></top>",
                "<top><wraptext>" . implode(', ', $i) . "</wraptext></top>",
                "<top>" . max($totals) . "</top>",
                "<top>" . array_sum($totals) . "</top>",
            ]);
        }

        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($excelData, 'Wordstat');

        $xlsx->setColWidth(1, 15);
        $xlsx->setColWidth(2, 25);
        $xlsx->setColWidth(3, 25);
        $xlsx->setColWidth(4, 35);
        $xlsx->setColWidth(5, 40);
        $xlsx->setColWidth(6, 35);
        $xlsx->setColWidth(7, 35);
        $xlsx->setColWidth(8, 35);
        $xlsx->setColWidth(9, 35);
        $xlsx->setColWidth(10, 25);
        $xlsx->setColWidth(11, 30);

        $xlsx->freezePanes('A2');

        $xlsx->downloadAs('wordstat_' . date("d_m_Y_h_i_s") . '.xlsx');
    }

    // Сброс собранной статистики
    public function clearTable()
    {
        YandexWordstat::truncate();
    }

    // Сброс всех задач в обработке
    public function clearJobs()
    {
        DB::table('jobs')->where('queue', 'wordstat')->delete();
    }

    public function getBalance()
    {
        $url = 'http://xmlriver.com/api/get_balance/?user=' . (int) config('yandex_wordstat.xmlriver_user_id') . '&key=' . config('yandex_wordstat.xmlriver_api_key');

        $response = Http::get($url);

        return $response->json();
    }

    // Какое количество задач в очереди
    public function tasksInWork()
    {
        $count = DB::table('jobs')->where('queue', 'wordstat')->count();

        return $count;
    }

    public function totalPhrases()
    {
        return YandexWordstat::count();
    }

    private function phraseHasStat(string $phrase)
    {
        $stat = YandexWordstat::where('phrase', $phrase)->first();

        return $stat;
    }
}
