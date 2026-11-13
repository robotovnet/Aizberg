<?php

namespace Modules\YandexWordstat\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\YandexWordstat\Services\WordstatService;
use Illuminate\Http\Request;

class WordstatController extends Controller
{
    public function __construct(
        private readonly WordstatService $wordstatService
    ) {}

    public function uploadExcel(Request $request)
    {
        if (!$request->file('excel')) {
            return response()->json(['error' => 'Файл не загружен'], 400);
        }

        // Проверить что файл excel
        if ($request->file('excel')->getClientOriginalExtension() !== 'xlsx') {
            return response()->json(['error' => 'Файл не является excel'], 400);
        }

        // Проверить размер файла
        if ($request->file('excel')->getSize() > 1024 * 1024 * 20) {
            return response()->json(['error' => 'Файл слишком большой'], 400);
        }

        // Проверить что файл не пустой
        if ($request->file('excel')->getSize() === 0) {
            return response()->json(['error' => 'Файл пустой'], 400);
        }

        // Проверить что файл не поврежден
        if ($request->file('excel')->getError()) {
            return response()->json(['error' => 'Файл поврежден'], 400);
        }

        return $this->wordstatService->uploadExcel($request->file('excel'));
    }

    public function getBalance()
    {
        $balance = $this->wordstatService->getBalance();

        return response()->json([
            'balance' => $balance,
        ]);
    }

    public function clearStat()
    {
        $this->wordstatService->clearTable();

        return response()->json(['success' => true]);
    }

    public function tasksInWork()
    {
        $tasks = $this->wordstatService->tasksInWork();
        $totalPhrases = $this->wordstatService->totalPhrases();
        
        return response()->json([
            'tasksInWork' => $tasks,
            'totalPhrases' => $totalPhrases,
        ]);
    }

    public function downloadExcel()
    {
        return $this->wordstatService->downloadExcel();
    }

    public function clearJobs()
    {
        $this->wordstatService->clearJobs();

        return response()->json(['success' => true]);
    }
}
