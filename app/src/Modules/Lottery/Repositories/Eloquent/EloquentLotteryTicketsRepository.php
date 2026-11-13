<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Lottery\Entities\LotteryTickets;
use Modules\Lottery\Repositories\LotteryTicketsRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EloquentLotteryTicketsRepository implements LotteryTicketsRepository
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
            throw new ModelNotFoundException('Lottery ticket not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(int $generationId): StreamedResponse
    {
        ini_set('memory_limit', '1024M');

        $tickets = LotteryTickets::where('generation_id', $generationId)
            ->get(['code'])
            ->toArray();

        return $this->downloadCsv($tickets, $generationId);
    }

    /**
     * @inheritDoc
     */
    public function generator(int $totalTickets): int
    {
        $generationId = $this->getGenerationId();

        for ($i = 0; $i < $totalTickets; $i++) {
            while (true) {
                $code = $this->generateRandomString();

                if (LotteryTickets::where('code', $code)->exists()) {
                    continue;
                }

                $this->saveTicket($code, $generationId);

                break;
            }
        }

        return $generationId;
    }

    /**
     * @inheritDoc
     */
    public function getGenerations(): array
    {
        return LotteryTickets::select('generation_id')
            ->selectRaw('COUNT(*) as tickets_count, MIN(created_at) as created_at')
            ->groupBy('generation_id')
            ->get()
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function saveTicket(string $code, int $generationId): LotteryTickets
    {
        return LotteryTickets::create([
            'code' => $code,
            'generation_id' => $generationId,
            'status' => 'unregistered',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTotalRegisteredTickets(): int
    {
        return LotteryTickets::where('status', 'registered')->count();
    }

    /**
     * @inheritDoc
     */
    public function getGenerationId(): int
    {
        $id = LotteryTickets::max('generation_id');

        return (is_null($id)) ? 1 : ++$id;
    }

    /**
     * Generates a random string.
     */
    private function generateRandomString(int $length = 13): string
    {
        $characters = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Downloads a CSV file with the tickets.
     */
    private function downloadCsv(array $data, int $generationId): StreamedResponse
    {
        ini_set('memory_limit', '1024M');

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=tickets_db_' . $generationId . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
        ];

        $callback = function () use ($data) {
            $fh = fopen('php://output', 'w');

            foreach ($data as $row) {
                fputcsv($fh, $row);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
